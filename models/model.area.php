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

// baseRecord
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
        
    function __construct($controller = false) {
        parent::__construct($controller);
        $this->modelName = 'area';
        
        // init $this->baseRecord
        $this->baseRecord = new AreaRecord();
        $this->readOrCreateAcfFields();
    }
    
    /**
     * read data from database by name
     * @param string $name
     * @param bool $all  - read ACF fields
     * @return AreaRecord  | false
     */
    public function getByName(string $name) {
        global $wpdb;
        $result = new stdClass();
        $result = $wpdb->get_row('select r.* from '.$this->getSelectFrom().' r where r.name="'.$name.'"');
        $this->lastError = $wpdb->last_error;
        if ($this->lastError != '') {
            $cmm->errorExit($this->lastError);
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
        $result = [];
        $result = $wpdb->get_results('select * 
        from '.$this->getSelectFrom().' 
        where parent = '.$id);
        $this->lastError = $wpdb->last_error;
        if ($this->lastError != '') {
            $cmm->errorExit($this->lastError);
        }
        return $result;
    }
    
    /**
     * check database If not exists create.
     */
    public function checkDatabase() {
        parent::checkDatabase();
        global $wpdb, $cmm;
        $wpdb->query('
            CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_'.$this->modelName.' (
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
        $this->lastError = $wpdb->last_error;
        if ($this->lastError == '') {
            $wpdb->query('
                CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_'.$this->modelName.'meta (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `parent_id` int(1),
                    `name` varchar(128),
                    `value` varchar(128),
                    PRIMARY KEY (`id`),
                    KEY `parent_ind` (`name`)
                 );
            ');
            $this->lastError = $wpdb->last_error;
        }
        if ($this->lastError != '') {
            $cmm->errorExit($this->lastError);
        }
    }
    
    /**
     * get or insert area data
     * @param object $record
     * @return int
     */
    public function getOrAddArea($record): int {
        $result = 0;
        $res = $this->getByName($record->name);
        if ($res) {
            $record->id = $res->id;
            $this->copy($record,$res);
            $this->update($res);
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