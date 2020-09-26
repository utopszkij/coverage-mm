<?php 
/**
 * Evo absztrakt objektumok   V 1.01 fejlesztői teszt változat
 *
 * ACF kezeléssel (lásd models/model.php)
 * 
 * Készítette: Sas Tibor tervei alapján Fogler Tibor
 * tibor.fogler@gmail.com
 * Licensz: GNU/GPL
 */
include_once __DIR__.'/model.php';

class EvoModel extends Model {
    
    function __construct($controller = false) {
        parent::__construct($controller);
        $this->modelName = 'evomodel';
        // .... adatbázis táblák kreálása ha még nem léteznek
    }

    /**
     * egy adott objektum beolvasása az adatbázisból id alapján
     * @param EvoController $controller
     * @param int $id
     * @return bool
     */
    public function evoRead(EvoController &$controller, int $id): bool {
        global $wpdb;
        $this->lastError = '';
        $result = true;
        $record = $this->read($id);
        $this->lastError = $wpdb->last_error;
        if (!$record) {
            // not founf
            $result = false;
        } else  {
            foreach ($record as $fn => $fv) {
                $controller->$fn = $fv;
            }
            if (isset($record->lifeCycle) & ($record->lifeCycle != '')) {
                $controller->lifeCycle = JSON_decode($record->lifeCycle);
            } else {
                $controller->lifecycle = new stdClass();
                $this->lifeCycle->state = '';
                $this->lifeCycle->counter = 0;
                $this->lifeCycle->phases = [];
                $this->lifeCycle->use = false;
                $this->init();
            }
            if ($controller->categories) {
                // kategória kapcsolatok beolvasása
                $res = $wpdb->get_results('select *
           from '.$wpdb->prefix.'cm_category_map
           where object_type = "'.$controller->_controllerName.'" and object_id = '.$controller->id);
                foreach ($res as $res1) {
                    if (!isset($controller->categories[$res1->category_type])) {
                        $controller->categories[$res1->category_type] = [];
                    }
                    $controller->categories[$res1->category_type][] = $res1->category_id;
                }
            }
            $result = ($this->lastError == '');
        } // found
        return $result;
    }
    
    /**
     * EvoController adatait adatbázis record -ba konvertálja
     * (fázisok json string formában, a kategóriákat nem tartalmazza a rekord)
     * @param EvoController $controller
     * @return object
     */
    public function evoGetRecord(EvoController $controller) {
        global $wpdb;
        $this->lastError = '';
        $result = new stdClass();
        foreach ($controller as $fn => $fv) {
            if (is_string($fv) | is_numeric($fv) | is_bool($fv)) {
                if (substr($fn,0,1) != '_') {
                    $result->$fv = $fn;
                }
            }
        }
        if (isset($controller->lifeCycle) & ($controller->lifeCycle->use) & ($this->lastError == '')) {
            $result->lifeCycle = JSON_encode($controller->lifeCycles);
        }
        return $result;
    }
    
    /**
     * insert object data into database
     * @param EvoController $controller
     * @return bool
     */
    public function evoInsert(EvoController &$controller): bool {
        global $wpdb;
        $this->lastError = '';
        $record = $this->evoGetRecord($controller);
        if ($this->insert($record)) {
            $controller->id = $record->id;
        }
        $this->lastError = $wpdb->last_error;
        if (($this->lastError == '') & (isset($controller->categories))) {
            // ha vannak kategóriák akkor azt is ki kell tárolni
            foreach ($controller->categories as $catType => $catValues) {
                foreach ($catValues as $catValue) {
                    $catRecord = new stdClass();
                    $catRecord->id = 0;
                    $catRecord->object_type = $controller->_controllerName;
                    $catRecord->object_id = $controller->id;
                    $catRecord->category_type = $catType;
                    $catRecord->category_id = $catValue;
                    $wpdb->insert($wpdb->prefix.'cmm_category_map', $catRecord);
                    if ($wpdb->last_error != '') {
                        $this->lastError = $wpdb->last_error;
                    }
                }
            }
        }
        if (isset($controller->phases) & ($this->lastError == '')) {
            $result->phases = JSON_encode($controller->phases);
        }
        return ($this->lastError == '');
    }
    
    /**
     * objektum adatainak modosítása az adatbázisban
     * @param EvoController $controller
     * @return bool
     */
    public function evoUpdate(EvoController $controller): bool {
        global $wpdb;
        $this->lastError = '';
        $record = $this->evoGetRecord($controller);
        $this->update($record);
        $this->lastError = $wpdb->last_error;
        if (($this->lastError == '') & (isset($controller->categories))) {
            // ha vannak kategóriák akkor azt is ki kell tárolni
            $wpdb->delete($wpdb->prefix.'cmm_category_map',
                ["object_type" => $controller->className,  "object_id" => $controller->id]);
            foreach ($controller->categories as $catType => $catValues) {
                foreach ($catValues as $catValue) {
                    $catRecord = new stdClass();
                    $catRecord->id = 0;
                    $catRecord->object_type = $controller->_controllerName;
                    $catRecord->object_id = $controller->id;
                    $catRecord->category_type = $catType;
                    $catRecord->category_id = $catValue;
                    $wpdb->insert($wpdb->prefix.'cmm_categorie_map', $catRecord);
                    if ($wpdb->last_error != '') {
                        $this->lastError = $wpdb->last_error;
                    }
                }
            }
        }
        return ($this->lastError == '');
    }
    
    /**
     * save vagy insert objekt adatok adatbázisba
     * @param EvoController $controller
     * @return bool
     */
    public function evoSave(EvoController &$controller): bool {
        if ($controller->id == 0) {
            $result = $this->evoInsert($controller);
        } else {
            $result = $this->evoUpdate($controller);
        }
        return $result;
    }
    
    /**
     * objektum adatainak törlése az adatbázisból
     * @param EvoController $controller
     * @return bool
     */
    public function evoDelete(EvoController $controller): bool {
        global $wpdb;
        $this->lastError = '';
        delete($controller->id);
        $this->lastError = $wpdb->last_error;
        if (($this->lastError == '') & (isset($controller->categories))) {
            // ha vannak kategóriák akkor azt is törölni kell
            $wpdb->delete($wpdb->prefix.'cmm_category_map',
                ["object_type" => $controller->className,  "object_id" => $controller->id]);
            $this->lastError = $wpdb->last_error;
        }
        return ($this->lastError == '');
    }
    
    /**
     * rekord sorozat beolvasása az adatbázisból
     * 
     * @param boolean $filter
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @return array
     */
    public function evoReads($filter = false, int $offset = 0, int $limit = 0, string $order = ''): array {
        global $wpdb;
        $this->lastError = '';
        $limitSql = ' offset '.$offset;
        if ($limit != 0) $limitSql .= ' limit '.$limit;
        $whereSql = '1';
        foreach ($filter as $fn => $fv) {
            $whereSql .= ' and `t.'.$fn.'` = "'.$fv.'"';
        }
        $result = $wpdb->get_results('select t.* 
        from '.$this->getSelectFrom().' t
        where '.$whereSql.'
        order by '.$order.'
        '.$limitSql);
        $this->lastError = $wpdb->last_error;
        return $result;
    }
    
    /**
     * filter -el szürt rekordok száma
     * 
     * @param array $filter
     * @return int
     */
    public function evoGetTotal(array $filter): int {
        global $wpdb;
        $this->lastError = '';
        $whereSql = '1';
        foreach ($filter as $fn => $fv) {
            $whereSql .= ' and `t.'.$fn.'` = "'.$fv.'"';
        }
        $res = $wpdb->get_results('select count(t.id) cc
        from '.$wpdb.prefix.'cmm_'.$this->modelName.' t
        where '.$whereSql);
        if (count($res) > 0) {
            $result = $res[0]->cc;
        } else {
            $result = 0;
        }
        $this->lastError = $wpdb->last_error;
        return $result;
    }

    /**
     * közvetlen gyermek objektumok beolvasása adatbázisból
     * @param EvoVontroller $controller
     * @return array
     */
    public function evoGetChilds(EvoVontroller $controller): array {
        $result = [];
        global $wpdb;
        $result = $wpdb->get_results('select *
        from '.$wpdb->prefix.$this->modelName.'
        where parent='.$controller->id);
        $this->lastError = $wpdb->last_error;
        return $result;
    }
    
    /**
     * tulajdonosok lekérése
     * @param EvoController $controller
     * @return array
     */
    public function evoGetParents(EvoController $controller): array {
        $result = [];
        global $wpdb;
        $res = $wpdb->get_row('select *
        from '.$wpdb->prefix.$this->modelName.'
        where id='.$controller->parent);
        $this->lastError = $wpdb->last_error;
        while ($res) {
            $result[] = $res->id;
            if (!in_array($res->parent) & ($res->parent != $controller->id)) {
                $res = $wpdb->get_row('select *
                from '.$wpdb->prefix.$this->modelName.'
                where id='.$res->parent);
                $this->lastError = $wpdb->last_error;
            } else {
                // tulajdonos hurok !!!!!
                $res = false;
            }
        }
        return $result;
    }
    
}
?>