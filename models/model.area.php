<?php 
/**
 * Area object data model
 * Store in database:  wp_product_cat
 * 
 * Ugyanennek van egy ACF -es változata is: acf_model.area.php
 * 2020.09.18 teszt szerint a 3100 település betöltése CSV -ből
 *    ACF változattal: 2 óra 30 perc
 *    ezzel a változattal: 1 óra 15perc
 */
include_once __DIR__.'/model.php';

class AreaRecord {
    public $id = 0;
    public $isarea = 0;
    public $name = '';
    public $slug = '';
    public $center_lat = 0.0;
    public $center_lng = 0.0;
    public $map_zoom = 7;
    public $area_category = '';
    public $enable_start = '';
    public $enable_end = '';
    public $status = 'active';
    public $description = '';
    public $population = 0;
    public $place = 0.0;
    public $poligon = '';
    public $parent = 0;
} // class

class AreaModel extends Model { 
    
    function __construct($controller) {
        parent::__construct($controller);
        $this->checkDatabase();
        $this->modelName = 'area';
    }
    
    /**
     * init AreaRecord
     * @return AreaRecord
     */
    public function init():AreaRecord {
        $result = new AreaRecord();
        $result->id = 0;
        $result->isarea = false;
        $result->name = '';
        $result->slug = '';
        $result->center_lat = 0.0;
        $result->center_lng = 0.0;
        $result->map_zoom = 7;
        $result->area_category = '';
        $result->enable_start = '';
        $result->enable_end = '';
        $result->status = 'active';
        $result->description = '';
        $result->population = 0;
        $result->place = 0.0;
        $result->poligon = '';
        $result->parent = 0;
        return $result;
    }
    
    /**
     * read data from database into controlelr
     * @param int $id
     * @param bool $all  - read ACF fields
     * @return AreaRecord | false
     */
    public function getById(int $id, bool $all = true) {
        return $this->getBy('term_id',$id, $all);
    }
    
    /**
     * read data from database into controlelr
     * @param string $name
     * @param bool $all  - read ACF fields
     * @return AreaRecord  | false
     */
    public function getByName(string $name, bool $all = true) {
        return $this->getBy('name',$name, $all);
    }
    
    /**
     * read data from database into controlelr
     * @param string $fieldName
     * @parem mixed $value
     * @param bool $all - read ACF fields
     * @return AreaRecord | false
     */
    public function getBy(string $fieldName, $value, bool $all = true) {
        global $wpdb;
        $res = $wpdb->get_row('select * 
        from '.$wpdb->prefix.'product_cat 
        where `'.$fieldName.'` = "'.$value.'"');
        $this->lastError = $wpdb->last_error;
        if ($res) {
            $result = new AreaRecord();
            $this->copy($res, $result);
        } else {
          $result = false;
        }
        return $result;
    }
    
    /**
     * get childs
     * @param int $id
     * @return array   - array of AreaRecord
     */
    public function getChilds(int $id): array {
        global $wpdb;
        $result = $wpdb->get_results('select * 
        from '.$wpdb->prefix.'product_cat 
        where parent = '.$id);
        $this->lastError = $wpdb->last_error;
        return $result;
    }
    
    /**
     * save data into database from controller
     * @param AreaRecord $record
     * @return int  new ID or 0 if error
     */
    public function insert(AreaRecord &$res): int {
        global $wpdb;
        $res->id = 0;
        $this->lastError = '';
        $result = 0;
        $wpdb->insert($wpdb->prefix.'product_cat', (array) $res);
        $res->id = $wpdb->insert_id;
        $this->lastError = $wpdb->last_error;
        if ($this->lastError != '') {
            echo 'fatal error '.$this->lastError; exit();
        }
        return $res->id;
    }
    
    /**
     * update data in database
     * @param AreaRecord $record
     * @return bool
     */
    public function modify(AreaRecord $res): bool {
        global $wpdb;
        $wpdb->update($wpdb->prefix.'product_cat',(array) $res, ["id" => $res->id]);
        $this->lastError = $wpdb->last_error;
        return ($this->lastError == '');
    }
    
    /**
     * delete data from database
     * @param AreaRecord $record
     * @return bool
     */
    public function remove(AreaRecord $res): bool {
        $this->lastError = '';
        $wpdb->delete($wpdb->prefix.'product_cat',["id" => $res->id]);
        $this->lastError = $wpdb->last_error;
        return ($this->lastError == '');
    }
    
    /**
     * check database If not exists create.
     */
    public function checkDatabase() {
        global $wpdb;
        $wpdb->query('
            CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'product_cat (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `isarea` int(1),
                `name` varchar(128),
                `slug` varchar(128),
                `center_lat` decimal(16,12),
                `center_lng` decimal(16,12),
                `map_zoom` int(1),
                `area_category` varchar(32),
                `enable_start` varchar(32),
                `enable_end` varchar(32),
                `status` varchar(32),
                `description` text,
                `population` int(11),
                `place` decimal(8,4),
                `poligon` text,
                `parent` int(11),
                PRIMARY KEY (`id`),
                KEY `name_ind` (`name`),
                KEY `area_category_ind` (`area_category`),
                KEY `parent_ind` (`parent`)
             );
        ');
        
        // create page type is not exists
        register_post_type($this->modelName,
                array(
                    'labels' => array(
                        'name' => 'cmm_'.$this->modelName,
                        'singular_name' => __($this->modelName, CMM)
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => $this->modelName),
                    'show_in_rest' => true,
                )
        );
    }
    
    /**
     * copy object to AreaRecord
     * @param object $source
     * @param AreaRecord $destination
     */
    public function copy($source, AreaRecord &$destination) {
        foreach ($destination as $fn => $fv) {
                if (isset($source->$fn)) {
                    $destination->$fn = $source->$fn;
                }
        }
    }
    
    /**
     * get or insert area data
     * @param object $record
     * @return int
     */
    public function getOrAddArea($record): int {
        $result = 0;
        $res = $this->getByName($record->name, true);
        if ($res) {
            $record->id = $res->id;
            $this->copy($record,$res);
            $this->modify($res);
            $result = $res->id;
        } else {
            $res = $this->init();
            $record->id = 0;
            $this->copy($record,$res);
            $this->insert($res);
            $result = $res->id;
        }
        return $result;
    }
    
} // class
?>