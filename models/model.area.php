<?php 
include_once __DIR__.'/model.php';

class AreaRecord {
    public $id = 0;
    public $isarea = false;
    public $name = '';
    public $slug = '';
    public $center_lat = 0.0;
    public $center_lng = 0.0;
    public $amp_zoom = 7;
    public $area_category = '';
    public $enableStart = '';
    public $enableEnd = '';
    public $status = 'active';
    public $description = '';
    public $population = 0;
    public $place = 0.0;
    public $poligon = '';
    public $parent = 0;
}

class AreaModel extends Model {
    
    protected $acfFields = ['area_category','isarea','population','place','poligon',
        'enable_start','enable_end','status','center_lat', 'center_lng', 'map_zoom'];
    
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
        $result->amp_zoom = 7;
        $result->area_category = '';
        $result->enableStart = '';
        $result->enableEnd = '';
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
        $result = false;
        $this->lastError = '';
        $rec = get_term_by($fieldName, $value, 'product_cat');
        if ($rec) {
            $rec->id = $rec->term_id;
            if ($all) {
                foreach ($this->acfFields as $acfField) {
                    $meta = get_term_meta($rec->id, $acfField, true);
                    $rec->$acfField = $meta;
                }
            }
            unset($rec->term_id);
            $result = new AreaRecord();
            $this->copy($rec,$result);
            $this->lastError = '';
        } else {
            $result = false;
            $this->lastError = 'not found';
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
        $recs = $wpdb->get_results('select * 
        from '.$wpdb->prefix.'term_taxonomy 
        where parent = '.$id.' and taxonomy="product_cat"');
        $result = [];
        foreach ($recs as $rec) {
            $result[] = $this->getById($rec->term_id, true);
        }
        return $result;
    }
    
    /**
     * save data into database from controller
     * @param AreaRecord $record
     * @return int  new ID or 0 if error
     */
    public function insert(AreaRecord &$res): int {
        $res->id = 0;
        $this->lastError = '';
        $result = 0;
        $w = wp_insert_term($res->name, 'product_cat', [
            "description" => $res->description,
            "slug" => $res->slug,
            "parent" => $res->parent
        ]);
        if (isset($w['term_id'])) {
            $res->id = $w['term_id'];
            foreach ($this->acfFields as $acfField) {
                if (!add_term_meta($res->id, $acfField, $res->$acfField)) {
                    $res->id = 0;
                    $this->lastError = 'error in write acfField';
                }
            }
        } else {
            $res->id = 0;
            $this->lastError = 'error in add to terms';
        }
        return $res->id;
    }
    
    /**
     * update data in database from controller
     * @param AreaRecord $record
     * @return bool
     */
    public function modify(AreaRecord $res): bool {
        $this->lastError = '';
        $result = true;
        $w = wp_update_term($res->id, 'product_cat', [
            "name" => $res->name,
            "slug" => $res->slug,
            "description" => $res->description,
            "parent" => $res->parent
        ]);
        foreach ($this->acfFields as $acfField) {
            if (!update_term_meta($res->id, $acfField, $res->$acfField)) {
                $result = false;
                $this->lastError = 'error in update acfField';
            }
        }
        return $result;
    }
    
    /**
     * delete data from database
     * @param AreaRecord $record
     * @return bool
     */
    public function remove(AreaRecord $res): bool {
        $this->lastError = '';
        $result = false;
        if (wp_delete_term($res->id, 'product_cat')) {
                $result = true;
                foreach ($this->acfFields as $acfField) {
                    if (!delete_term_meta($res->id, $acfField)) {
                        $result = false;
                    }
                }
        }
        $this->controller->id = 0;
        return $result;
    }
    
    /**
     * check ACF gropu anfd fields. If not exists create.
     */
    public function checkDatabase() {
        $groupId = $this->getAcfGroupId('cmm product_cat extends');
        if ($groupId == 0) {
            $groupId = $this->addAcfGroup('cmm product_cat extends',
                'a:7:{s:8:"location";a:1:{i:0;a:1:{i:0;a:3:{s:5:"param";s:8:"taxonomy";s:8:"operator";s:2:"==";s:5:"value";s:11:"product_cat";}}}s:8:"position";s:6:"normal";s:5:"style";s:7:"default";s:15:"label_placement";s:3:"top";s:21:"instruction_placement";s:5:"label";s:14:"hide_on_screen";s:0:"";s:11:"description";s:0:"";}');
        }
        if ($this->getAcfFieldId('isarea',$groupId) == 0) {
            $this->addAcfField($groupId, 'isarea',
                'a:10:{s:4:"type";s:10:"true_false";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:7:"message";s:0:"";s:13:"default_value";i:0;s:2:"ui";i:0;s:10:"ui_on_text";s:0:"";s:11:"ui_off_text";s:0:"";}');
        }
        if ($this->getAcfFieldId('area_category',$groupId) == 0) {
            $this->addAcfField($groupId, 'area_category',
                'a:12:{s:4:"type";s:8:"checkbox";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:7:"choices";a:12:{s:13:"micro_village";s:13:"micro_village";s:12:"mini_village";s:12:"mini_village";s:7:"village";s:7:"village";s:11:"big_village";s:11:"big_village";s:9:"mini_city";s:9:"mini_city";s:7:"subcity";s:7:"subcity";s:8:"big_city";s:8:"big_city";s:9:"subregion";s:9:"subregion";s:6:"region";s:6:"region";s:7:"country";s:7:"country";s:9:"continent";s:9:"continent";s:5:"other";s:5:"other";}s:12:"allow_custom";i:0;s:13:"default_value";a:1:{i:0;s:5:"other";}s:6:"layout";s:10:"horizontal";s:6:"toggle";i:0;s:13:"return_format";s:5:"value";s:11:"save_custom";i:0;}');
        }
        if ($this->getAcfFieldId('center_lat',$groupId) == 0) {
            $this->addAcfField($groupId, 'center_lat',
                'a:15:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}');
        }
        if ($this->getAcfFieldId('center_lng',$groupId) == 0) {
            $this->addAcfField($groupId, 'center_lng',
                'a:15:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}');
        }
        if ($this->getAcfFieldId('map_zoom',$groupId) == 0) {
            $this->addAcfField($groupId, 'map_zoom',
                'a:15:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}');
        }
        if ($this->getAcfFieldId('map_id',$groupId) == 0) {
            $this->addAcfField($groupId, 'map_id',
                'a:10:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:2:"[]";s:11:"placeholder";s:0:"";s:9:"maxlength";s:0:"";s:4:"rows";s:0:"";s:9:"new_lines";s:0:"";}');
        }
        if ($this->getAcfFieldId('poligon',$groupId) == 0) {
            $this->addAcfField($groupId, 'poligon',
                'a:10:{s:4:"type";s:8:"textarea";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:2:"[]";s:11:"placeholder";s:0:"";s:9:"maxlength";s:0:"";s:4:"rows";s:0:"";s:9:"new_lines";s:0:"";}');
        }
        if ($this->getAcfFieldId('population',$groupId) == 0) {
            $this->addAcfField($groupId, 'population',
                'a:12:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";i:0;s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:3:"min";s:0:"";s:3:"max";s:0:"";s:4:"step";s:0:"";}');
        }
        if ($this->getAcfFieldId('place',$groupId) == 0) {
            $this->addAcfField($groupId, 'place',
                'a:12:{s:4:"type";s:6:"number";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:3:"km2";s:3:"min";s:0:"";s:3:"max";s:0:"";s:4:"step";s:0:"";}');
        }
        if ($this->getAcfFieldId('enable_start',$groupId) == 0) {
            $this->addAcfField($groupId, 'enable_start',
                'a:8:{s:4:"type";s:11:"date_picker";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:14:"display_format";s:5:"Y.m.d";s:13:"return_format";s:5:"Y-m-d";s:9:"first_day";i:1;}');
        }
        if ($this->getAcfFieldId('enable_end',$groupId) == 0) {
            $this->addAcfField($groupId, 'enable_end',
                'a:8:{s:4:"type";s:11:"date_picker";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:14:"display_format";s:5:"Y.m.d";s:13:"return_format";s:5:"Y-m-d";s:9:"first_day";i:1;}');
        }
        if ($this->getAcfFieldId('status',$groupId) == 0) {
            $this->addAcfField($groupId, 'status',
                'a:13:{s:4:"type";s:6:"select";s:12:"instructions";s:0:"";s:8:"required";i:1;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:7:"choices";a:3:{s:5:"draft";s:5:"draft";s:6:"active";s:6:"active";s:6:"closed";s:6:"closed";}s:13:"default_value";b:0;s:10:"allow_null";i:1;s:8:"multiple";i:0;s:2:"ui";i:0;s:13:"return_format";s:5:"value";s:4:"ajax";i:0;s:11:"placeholder";s:0:"";}');
        }
    }
    
    /**
     * copy AreaRecord
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
        $res = $this->getByName($record->name, false);
        if ($res) {
            $record->id = $res->id;
            $this->copy($record,$res);
            $this->modify($res);
            $result = $res->id;
        } else {
            $res = $this->model->init();
            $record->id = 0;
            $this->copy($record,$res);
            $this->insert($res);
            $result = $res->id;
        }
        return $result;
    }
}
?>