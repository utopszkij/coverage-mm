<?php
/**
* covreage_mm picture sablon objektum
*/

/*
 * pictogram sablon objektum
 * 
 * A txt ben értelmezett formális paraméterek:
 * {userRealName}  - a sortkodban lehet user=logged paraméter is ez esetbe onnan veszi
 * {userNickName}  - a sortkodban lehet user=logged paraméter is ez esetbe onnan veszi
 * {userProfileLink} - a sortkodban lehet user=logged paraméter is ez esetbe onnan veszi
 * {areaName} - a shortcode -ban lehet area=szám paraméter, ez esetben onnan veszi  
 * {areaLink} - a shortcode -ban lehet area=szám paraméter, ez esetben onnan veszi
 * {url1}
 * {url2}
 * {url3}
 * {date}
 * és a hozzákapcsolt ACF fieldek kapcsos zárójelbe tett nevei.
 * 
 * shortcode -ban megadható paraméterek
 *    id  -- kötelező
 *    user=szám vagy logged  defaultlogged
 *    area=szám              default 0 
 *    userRealName           default user' real name
 *    userNickName           default user' nick name
 *    userProfileLink        default user' profile link
 *    areaNeme               default area' name
 *    areaLink               default area' link
 *    url1                   default '#'
 *    url2                   default '#'
 *    url3                   default '#'
 *    date                   default mai nap
 * @author utopszkij
 */
class PictogramsablonController extends EvoController {
	    public $_controllerName = 'pictogramsablon';
	    
	    /**
	     * objektum példány létrehozása
	     */
	    public function create() {
	        parent::create();
	        $this->id = 0;                // int(11) auto increment egyedi azonosító
	        $this->name = '';             // string(128) név
	        $this->title = '';            // string(256) leírás
	        $this->published = true;      // bool közzétéve igen vagy nem
	        $this->media = '';            // html kód  image|video|audio|map
	        $this->txt = '';              // html kód
	        $this->backgroundColor = '';  // background color
	        $this->created = '';          // datetime Y-m-d H:i:s
	        $this->modified = '';         // datetime Y-m-d H:i:s vagy üres
	        $this->author = 0;            // felvivő user id
	        $this->categories = [];
	        $this->lifeCycle->use = false;
	        $this->eventLog->logging = false;
	        
	        echo 'pictorgramsablon create '.JSON_encode($this->lifeCycle).'<br>';
	        
	        
	    }
	    
	    
	    /**
	     * univerzális adatform
	     * @param string $title
	     * @param string $action
	     * @param string $msg
	     * @param string $msgClass
	     * 
	     * POST -ban érkezik id és érkezhetnek további adatok is 
	     */
	    protected function form(string $title, string $action, string $msg = '', string $msgClass = '') {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        $this->action = $action;
	        $this->title = $title;
	        $this->msg = $msg;
	        $this->msgClass = $msgClass;
	        $this->csrToken = $cmm->createCsrToken();
	        // objektum adatok alvasása a GET/POST -ból
	        foreach ($this as $fn => $fv) {
	            $this->$fn = $cmm->getParam($fn, $fv);
	        }
	        $this->display('pictogramsablon.adminform');
	    }
	    
	    /**
	     * felviteli adatform, POST -ban érkezhetnek form adatok is
	     */
	    public function addform() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $this->init();
	        $this->form('add_picturesablon','add');
	    }
	    
	    /**
	     * modosítási adatform, POST -ban érkezhetnek form adatok is
	     */
	    public function editform() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $this->id = $cmm->getParam('id');
	        if ($this->model->evoRead($this, (int)$this->id)) {
	           $this->form('edit_picturesablon','edit');
	        } else {
	            $this->browserForm('not found','error notice');
	        }
	    }
	    
	    /**
	     * adat tárolás felvitel vagy modosítás után
	     * POST ban érkeznek a form adatok, beleértve az id -t is
	     */
	    public function saveTask() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        // objektum adatok alvasása a POST -ból
	        foreach ($this as $fn => $fv) {
	            $this->$fn = $cmm->getParam($fn,$fv);
	        }
	        $errorMsg = $this->checkData();
	        if ($errorMsg != '') {
	            if ($this->id == 0) {
	                $this->form('add_picturesablon','add',$errorMsg,'error notice');
	            } else {
	                $this->form('edit_picturesablon','edit',$errorMsg,'error notice');
	            }
	        } else {
    	        if ($this->model->evoSave($this)) {
    	            // sikeres tárolás
    	            $this->browserForm('success_save','info notice');
    	        } else {
    	            if ($this->data->id == 0) {
    	                $this->form('add_picturesablon','add',$this->model->getErrorMsg(),'error notice');
    	            } else {
    	                $this->form('edit_picturesablon','edit',$this->model->getErrorMsg(),'error notice');
    	            }
    	        }
	        }
	    } // save function
	    
	    /**
	     * törlés elött biztonsági kérdés
	     * POST -ban érkezik az id
	     */
	    public function suredelete() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        $this->id = $cmm->getParam('id');
	        if ($this->model->evoRead($this, (int)$this->id)) {
	           $this->display('pictogramsablon.admindelete');
	        } else {
	            $this->browserForm('not found','error notice');
	        }
	    }
	    
	    /**
	     * törlés végrehajtása
	     * POST -ban érkezik az id
	     */
	    public function dodelete() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        $this->id = $cmm->getParam('id');
	        if ($this->model->evoRead($this, (int)$this->id)) {
	            if ($this->model->delete((int)$this->id)) {
	                $this->browserForm('success_delete','info notice');
	            } else {
	                $this->browserForm('error in delete','error notice');
	            }
	        } else {
	            $this->browserForm('not found','error notice');
	        }
	    }
	    
	    /**
	     * böngésző képernyő
	     * GET -ben érkezhet: offset, limit, filterstr, order, total
	     * ugyanezek sessionból is érkezhetnek
	     */
	    public function browserform(string $msg='', string $msgClass='') {
	        global $cmm;
	        $cmm->checkAdmin();
	        $this->msg = $msg;
	        $this->msgClass = $msgClass;
	        $this->offset = $cmm->getParam('offset');
	        $this->limit = $cmm->getParam('limit');
	        $this->filterstr = $cmm->getParam('filterstr');
	        $this->order = $cmm->getParam('order');
	        $this->total = $cmm->getParam('total');
	        if (!$this->offset) $this->offset = $cmm->getFromSession('picoffset',0);
	        if (!$this->limit) $this->limit = $cmm->getFromSession('piclimit',20);
	        if (!$this->filterstr) $this->filterstr = $cmm->getFromSession('picfilterstr','');
	        if (!$this->order) $this->order = $cmm->getFromSession('picorder','1');
	        if (!$this->total) $this->total = $cmm->getFromSession('pictotal',0);
	        $cmm->setToSession('picoffset',$this->offset);
	        $cmm->setToSession('piclimit',$this->limit);
	        $cmm->setToSession('picfilterstr',$this->filterstr);
	        $cmm->setToSession('picorder',$this->order);
	        $cmm->setToSession('pictotal',$this->total);
	        $res = $this->model->getRecords($this->offset, $this->limit, $this->filterstr, $this->order);
	        $this->items = $res->items;
	        $this->total = $res->total;
	        $this->display('pictogramsablon.adminbrowser');
	    }
	    
	    /**
	     * import csv első képernyő
	     */
	    public function importcsv() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        
	        echo 'csv import';
	        
	    }
	    
	    /**
	     * export csv
	     */
	    public function exportcsv() {
	        global $cmm;
	        $cmm->checkAdmin();
	        $cmm->checkCsrToken();
	        
	        echo 'csv export';
	        
	    }
	    
	    /**
	     * sortcode értelmező, végrehajtó
	     */
	    public function sortcode(array $attrs):string {
	        return 'picturesablon sortcode';
	    }
	    
	    protected function checkdata(): string {
	        return '';
	    }
	    
} // Picturesablon class
		
?>