<?php
	/**
	* coverage-mm map controller 
	* 
	* see https://leafletjs.com/reference-1.7.1.html
    *
	* Kétféle markert definiálhat az admin:
	* 
	* "Általános célú markerek" Ezek tetszőleges térképen, tetszőleges helyen megjeleníthetőek.
	* Ezeket a "default" map-hoz kell felvenni, a definiáláskor tetszőlegesek lehet a poziója,
	* a megjelenítéskor ez felül lesz bírálva.
	* 
	* "Egyedi" markerek Ezeket a konkrét térképhez kell felvenni, abban apozicióban fognak megjelenni
	* ahová definiáláskor az admin elhelyezi őket.
    * 
    * térkép megjelenítő shortcode: 
    *   
    *   [cmm_map map_id=# markers=xxxxxxxx] 
    *      ahol markers = "id:#,lat:#,lng:#;id:#,lat:#,lng:#; ....." 
    *           ahol id - marker ID
    *                lat - földreajzi hosszúság
    *                lng - földrajzi szélesség
    *           a markers -ben lévő "id" -knek álltalános célú markerekre kell mutatniuk.
    *           (tehát a markers -ben az egyes marker definiciókat pontosvessző választja el)
    *           A markers lehet üres string is, illetve el is hagyható
    *   Müködése:
    *       Megjeleniti a "map_id" térképet, a rajta lévő "Egyedi markerekkel", valamint
    *       megjelenítit a "markers" paraméterben felsorolt "álltalános célú" markereket,
    *       a "markers" paraméterben magadott poziciókban.
    *       
    *   Péda:
    *   [cmm_map map_id=5 markers=id:14,lat:14.19543,lng:25.455678;id:15,lat:15.00012345,lng:14.000456]             
	*/

    include_once __DIR__.'/controller.php';
	class MapController extends Controller  {
	    protected $controllerName = 'map';

	    /**
	    * HTML kód ami megejelníti az "id" -jü térképet, a hozzá kapcsolt markerrekkel 
	    * együtt, ezenkivül megjeleniti a "markers" paraméterben felsorolt "általános célú" 
	    * markereket a "markers" paraméterben megadott poziciókban.
	    * @param int $map_id
	    * @param string $markers "id:#,lat:#,lng:#;id:#,lat:#,lng:#;...."
	    * @return string html code
	    */       
	    public function getMapHtml($map_id, $markers) {
	       $result = '567';
	       
	       // parse markers --> [["id" => #, "lat" => #, "lng" => #], ...]
	       if ($markers != '') {
    	       $markers = explode(';',$markers);
    	       for ($i=0; $i<count($markers); $i++) {
    	           $w = explode(',',$markers[$i]);
    	           $markers[$i] = ["id" => 0, "lat" => 0, "lng" => 0];
    	           foreach ($w as $w1) {
    	               $w2 = explode(':',$w1);
    	               $markers[$i][$w2[0]] = $w2[1];
    	           }
    	       }
	       } else {
                $markers = [];	           
	       }
	       
	       // processing, you cannot run multiple instances in parallel
	       $sm = sem_get(getmyinode() + hexdec(substr(md5("cmm_map_shortcode"), 24)));
	       if (sem_acquire($sm)) {
	           $defMapId = $this->model->getDefMapId();
	           if ($defMapId > 0) {
	               if ($this->model->check($map_id, $markers, $defMapId)) {
	                   if ($this->model->updateMarkers($markers, $map_id)) {
	                       // echo map + markers by UMS
	                       $result = do_shortcode('[ultimate_maps id='.$map_id.']');
	                       $this->model->updateMarkers($markers, $defMapId);
	                   }
	               }
	           }
	           sem_release($sm);
	           sem_remove($sm);
	       } else {
	           throw new \Exception('unable to acquire semaphore');
	       }
    	       
    	   // put result    
	       if ($this->model->getErrorMsg() != '') {
	           $result = '<div class="alert alert-error">'.$this->model->getErrorMsg().'</div>';
	       }
	       return $result;
	    }
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('category_adminform');
		}
		
	} // class
?>