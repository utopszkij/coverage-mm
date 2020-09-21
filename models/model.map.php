<?php 
/**
* UMS - laflet map kezelő objektum
*
*/ 
include_once __DIR__.'/model.php';
include_once __DIR__.'/model.area.php';
class MapModel extends Model {
    
    protected $lastError = '';
    
    /**
     * parse wp style params, can you use only cord_x, coord_y, zoom
     * @param string $params
     * @return array ["name" => value, ....]
     */
    protected function parseParams(string $params): array {
        $result = array();
        $w = explode(';',$params);
        $i = 0;
        while ($i < count($w)) {
            $items = explode(':',$w[$i]);
            $name = str_replace('"','',$items[2]);
            $items = explode(':',$w[$i+1]);
            $result[$name] = str_replace('"','',$items[2]);
        }
        return $result;
    }
        
    /**
     * update in wp style params, 
     * can you use only "coord_x, "coord_y, zoom" items!
     * @param array $params
     * @param string $name
     * @param string $value
     */
    protected function updateParams(string $params, string $name, string $value): string {
        $w = explode(';',$params);
        $i = 0;
        for ($i=0; $i<count($w); $i++) {
            $items = explode(':',$w[$i]);
            if ('"'.$name.'"' == $items[2]) {
                $w[$i+1] = 's:'.strlen($value).':"'.$value.'"';                
            }
        }
        $result = implode(';',$w);
        return $result;
    }
    
    /**
     * get map record by id
     * @param int $id
     * @return array $map ["fieldName" => value, ....] or null
     */
    public function getMapById(int $id) {
        $mapRec = [];
        global $wpdb;
        $mapRec = $wpdb->get_row('select * from '.$wpdb->prefix.'ums_maps where id = '.$id, ARRAY_A );
        $this->lastError = $wpdb->last_error;
        return $mapRec;
    }
    
    /**
     * get map record by title
     * @param string $title
     * @return array $map ["fieldName" => value, ....] or null
     */
    public function getMapByTitle(string $title) {
        $mapRec = [];
        global $wpdb;
        $mapRec = $wpdb->get_row('select * from '.$wpdb->prefix.'ums_maps where title = "'.$title.'"', ARRAY_A );
        $this->lastError = $wpdb->last_error;
        return $mapRec;
    }
    
    /**
     * add new default map record into db from "default" map record
     * @param string $title
     * @param float $lat
     * @param float $lan
     * @param int $zoom
     * @return int - new map record id
     */
    public function addDefaultMap(string $title, float $lat, float $lng, int $zoom): int {
       $result = 0;
       global $wpdb;
       $mapRec = array();
       $maprec['id'] = 0;
       $mapRec['title'] = '';
       $mapRec['engine'] = 'leaflet';
       $mapRec['params'] = 'a:60:{s:11:"width_units";s:1:"%";s:16:"membershipEnable";s:1:"0";s:26:"adapt_map_to_screen_height";s:0:"";s:4:"type";N;s:8:"map_type";s:50:"https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";s:16:"map_display_mode";N;s:10:"map_center";a:3:{s:7:"address";s:116:"Szabadszállás, Kunszentmiklósi járás, Bács-Kiskun megye, Dél-Alföld, Alföld és Észak, 6080, Magyarország";s:7:"coord_x";s:16:"46.9052455464292";s:7:"coord_y";s:17:"19.32478667396323";}s:8:"language";N;s:11:"enable_zoom";N;s:17:"enable_mouse_zoom";N;s:16:"mouse_wheel_zoom";s:1:"1";s:9:"zoom_type";N;s:4:"zoom";s:1:"6";s:11:"zoom_mobile";s:1:"8";s:8:"zoom_min";s:1:"1";s:8:"zoom_max";s:2:"21";s:19:"navigation_bar_mode";s:4:"none";s:12:"zoom_control";N;s:14:"dbl_click_zoom";s:1:"1";s:19:"street_view_control";N;s:11:"pan_control";N;s:16:"overview_control";N;s:9:"draggable";s:6:"enable";s:15:"map_stylization";N;s:18:"marker_title_color";s:7:"#000000";s:17:"marker_title_size";s:2:"19";s:23:"marker_title_size_units";s:2:"px";s:16:"marker_desc_size";s:2:"13";s:22:"marker_desc_size_units";s:2:"px";s:19:"hide_marker_tooltip";N;s:28:"center_on_cur_marker_infownd";N;s:19:"marker_infownd_type";N;s:29:"marker_infownd_hide_close_btn";N;s:20:"marker_infownd_width";s:3:"200";s:26:"marker_infownd_width_units";s:4:"auto";s:21:"marker_infownd_height";s:3:"100";s:27:"marker_infownd_height_units";s:4:"auto";s:23:"marker_infownd_bg_color";s:7:"#FFFFFF";s:16:"marker_clasterer";s:4:"none";s:21:"marker_clasterer_icon";N;s:27:"marker_clasterer_icon_width";N;s:28:"marker_clasterer_icon_height";N;s:26:"marker_clasterer_grid_size";s:2:"60";s:33:"marker_clasterer_background_color";s:7:"#2196f3";s:29:"marker_clasterer_border_color";s:7:"#1c7ba7";s:27:"marker_clasterer_text_color";s:5:"white";s:19:"marker_filter_color";N;s:26:"marker_filter_button_title";N;s:12:"marker_hover";s:1:"1";s:35:"slider_simple_table_width_dimension";N;s:31:"slider_simple_table_width_title";N;s:33:"slider_simple_table_width_address";N;s:37:"slider_simple_table_width_description";N;s:38:"slider_simple_table_width_getdirection";N;s:17:"markers_list_type";N;s:18:"markers_list_color";s:7:"#55BA68";s:9:"is_static";N;s:16:"hide_empty_block";N;s:15:"autoplay_slider";N;s:14:"slide_duration";N;}';
       $mapRec['html_options'] = 'a:2:{s:5:"width";s:3:"100";s:6:"height";s:3:"250";}';
       $mapRec['create_date'] = date('Y-m-d H:i:s');
       // update $mapRec
       $s = $mapRec['params'];
       $s = $this->updateParams($s, 'coord_x', $lat);
       $s = $this->updateParams($s, 'coord_y', $lng);
       $s = $this->updateParams($s, 'zoom', $zoom);
       $mapRec['params'] = $s;
       $mapRec['title'] = $title;
       $mapRec['id'] = 0;
       // insert new map record into database
       if ($wpdb->insert($wpdb->prefix.'ums_maps', $mapRec)) {
          $result = $wpdb->insert_id;
       }
       $this->lastError = $wpdb->last_error;
       return $result;
    }
    
    /**
     * create map record if not exists, update if exists for exists $term
     * @param int $term_id
     */
    public function createOrUpdateMap(int $term_id): bool {
        /* ez igy nem jó rekurzivan önmagát hívja!
        global $wpdb;
        if (($term_id == 0) | ($term_id == '')) {
            return true;
        }
        $areaModel = new AreaModel(false);
        $this->lastError = '';
        // get product_cat
        $cat = $areaModel->getById($term_id);
        if ($cat) {
            $title = $cat->name;
            $center_lat = $cat->center_lat;
            $center_lng = $cat->center_lng;
            $zoom = $cat->map_zoom;
            $isarea = $cat->isarea;
            $map_id = $cat->map_id;
            if ($isarea == 1) {
                if (($map_id == 0) | ($map_id == '')) {
                    // create new map record
                    $map_id = $this->addDefaultMap($title, $center_lat, $center_lng, $zoom);
                    $this->lastError = $wpdb->last_error;
                    // update product_cat record
                    $cat->map_id = $map_id;
                    $areaModel->modify($cat);
                } else {
                    // get exists map record
                    $mapRec = $this->getMapById($map_id);
                    // update $mapRec
                    $s = $mapRec['params'];
                    $s = $this->updateParams($s, 'coord_x', $center_lat);
                    $s = $this->updateParams($s, 'coord_y', $center_lng);
                    $s = $this->updateParams($s, 'zoom', $zoom);
                    $s = $this->updateParams($s, 'address', '');
                    $mapRec['params'] = $s;
                    $mapRec['title'] = $title;
                    // update in database
                    $wpdb->update($wpdb->prefix.'ums_maps',$mapRec,["id" => $map_id]);
                    $this->lastError = $wpdb->last_error;
                }
            } // isarea == 1
        } // $term megvan
        */
        return ($this->lastError == '');
    }
    
    /**
     * get lasetError
     * @return string
     */
    public function getErrorMsg(): string {
        return $this->lastError;
    }
    
    /**
     * get default map record' id
     * @return int
     */
    public function getDefMapId():int {
        $result = 0;
        $mapRec = $this->getMapByTitle('default');
        if ($mapRec) {
            $result = $mapRec['id'];
        }
        return $result;
    }
    
    /**
     * chek params
     * @param int $map_id
     * @param array $markers [[id => #, lat => #, lng => #],.....]
     * @param int $defMapId
     * @return bool
     */
    public function check(int $map_id, array $markers, int $defMapId):bool {
        $result = true;
        global $wpdb;
        // map_id valid ?
        $mapRec = $this->getMapById($map_id);
        if ($mapRec) {
            if ($map_id != $defMapId) {
                // all marker.id is valid and linked into defMapId ?
                foreach ($markers as $marker) {
                    $markerRec = $wpdb->get_row('select * from '.$wpdb->prefix.'ums_markers where id = "'.$marker['id'].'"', ARRAY_A );
                    if (!$markerRec) {
                        $this->lastError .= ' '.$marker['id'].' marker is not found.';
                    } else {
                        if ($markerRec['map_id'] != $defMapId) {
                            $this->lastError .= ' '.$marker['id'].' marker is not assign to deafult map.';
                        }
                    }
                }
            } else {
                $this->lastError = $map_id.' is the default map.';
            }
        } else {
            $this->lastError = $map_id.' map not found.';
        }
        return $result;
    }
    
    /**
     * update markers map:id
     * @param array $markers [[id => #, lat => #, lng => #],.....]
     * @param int $map_id
     */
    public function updateMarkers(array $markers, int $map_id):bool {
        $result = true;
        global $wpdb;
        foreach ($markers as $marker) {
            if (!$wpdb->update($wpdb->prefix.'ums_markers',["map_id" => $map_id],["id" => $marker['id']])) {
                $this->lastError .= ' '.$wpdb->last_error;
            }
        }
        return ($this->lastError == '');
    }
   
}
?>