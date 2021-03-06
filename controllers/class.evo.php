<?php
/**
 * coverage_mm abstrak objektumok
 * Készítette Sas Tibor terve alapján Fogler Tibor
 * Licensz: GNU/GPL
 */


/*
* kell hozzá: EvoEventLogController , controllers/class.evoeventlog.php  
*             EvoCategoriesController , controllers/class.evocategories.php
*             EvoEventLogModel , models/model.evoeventlog.php -fájlban  
*             EvoCategoriesModel , models/model.evocategories.php -fájlban  
* 
* minden EvoController leszármazotthoz kell egy 
* ObjectNameModel adatmodell is models/model.objectname.php fájlban
* Ennek minimálissan szükséges obejktumai:
* bool = evoInsert(EvoController)
* bool = evoUpdate(EvoController)
* bool = evoDelete(EvoController)
* bool = evoReset(EvoController)
* bool = evoRread(EvoController, $id)
* array = evoReads($filter, $offset, $limit, $order)
* array = evoGetChilds(Evocontroller)
* array = evoGetParents(Evocontroller)
* object = evoGetRecord(EvoController)
* int = evoGetTotal($filter)
* string = getErrorMsg()
* 
* Gyakorlatilag mindig felülírandó az EvoController->crate  metod.
*  
*/

include_once __DIR__.'/class.evoeventlog.php';
// ha vannak kategóriák akkor include_once __DIR__.'class.evocategories.php';

/**
 * hiba kezelő objektum
 * @author utopszkij
 */
class EvoError {
    /**
     * hiba státusz 'error' | 'warning'
     * @var string
     */
    public $state = 'error';    
    /**
     * Hiba szint 'business' | 'application' | 'system' | 'network'
     * @var string
     */
    public $level = 'ubnknown'; 
    /**
     * fatális hiba
     * @var boolean
     */
    public $fatale = false;     
    /**
     * hiba kód
     * @var string
     */
    public $code = text;      
    /**
     * hiba szöveg
     * @var string
     */
    public $string = text;     
    /**
     * hibát kiváltó objektum tipusa
     * @var string
     */
    public $sourceType = '';    
    /**
     * hibát kiváltó objektum azonosítója
     * @var integer
     */
    public $sourceId = 0;       // int(11) hibát kiváltó objektum id -je
    
    /**
     * hiba esemény
     * @param string $state
     * @param string $level
     * @param bool $fatale
     * @param string $code
     * @param string $string
     * @param EvoController $sorce
     */
    static public function createError(string $state, string $level, bool $fatale, 
        string $code = '', string $string = '', EvoController $sorce) {
            if ($fatale) {
                echo 'Coverage_mm Fatal error '.$state.' '.$level.
                '<br>source='.$source->_controllerName.'.id='.$source->id.
                '<br>code='.$code.
                '<br>string='.$string; 
                exit();  
            } else {
                $source->eventLog->add($source, $state, $level.' '.$code.' '.$string);
            }
    }
} // class EvoError

/**
 * Fázis kezelő objektum
 * @author utopszkij
 */
class EvoPhase {
    /**
     * objektum aminek ez az egyik fázisa
     * @var EvoController
     */
    protected $source; 
    /**
     * azonosító
     * @var integer
     */
    public $id = 0;
    /**
     * fázis neve 'init' | 'build' | 'maintain' | 'unbuild' | 'archive' | ......
     * @var string
     */
    public $name = 'init';    
    /**
     * fázis prioritása 0 a legkevésbé sürgős
     * @var integer
     */
    public $priority = 0;     
    /**
     * fázis állapota '' | 'started' | 'ended' | 'paused' | 'canceled'
     * @var string
     */
    public $state = 'waiting'; 
    /**
     * al fázisok
     * @var array of EvoPhase
     */
    public $subPhases = [];   
    /**
     * fázis inditás időpontja Y-m-d H:i:s vagy üres
     * @var string
     */
    public $startTime = '';   
    /**
     * fázis befejezés időpontja Y-m-d H:i:s vagy üres
     * @var string
     */
    public $endTime = '';     
    /**
     * egyébb infók
     * @var string
     */
    public $data = '';
    
    /**
     * konstruktor
     * @param EvoController $source
     * @param string $name
     * @param string $data
     */
    function __construct(EvoController $source, string $name, string $data = '') {
       $this->source = $source;
       $this->name = $name;
       $this->data = $data;
    }
    
    /**
     * fázis inditása
     * @param string $data
     * @return bool success or not
     */
    public function start(string $data = ''):bool {
        $this->data = $data;
        $this->state = 'started';
        $this->startTime = date('Y-m-d H:i:s');
        return true;
    }
    
    /**
     * fázis befejezése
     * @return bool 
     */
    public function end(): bool {
        $this->state = 'ended';
        $this->endTime = date('Y-m-d H:i:s');
        return true;
    }
    
    /**
     * fázis jelenleg inditható?
     * párhuzamossan inditható fázisok esetében lehet rá szükség.
     * @return bool
     */
    public function canItbeStarted(): bool {
        return true;
    }
} // class EvoPashe


/**
 * EVO objektum
 * @author utopszkij
 */
class EvoController {
    // FIGYELEM AZ ADATBÁZISBA TÁROLANDÓ MEZŐKNÉL CSAK
    // KISBETÜK, SZÁMOK, ALÁHUZÁS LEGYENEK A NEVEKBEN !
    // Az itt következő string, numerik, bool tipusú változók alapértelmezetten
    // az adatbázis egy-egy mezőit jelentik.
    // Ha olyan property -is kell amit nem tárolunk az adatbázisban akkor annak nevék
    // aláhuzás jellel kell kezdeni.
    /**
     * adatmodel
     * @var EvoModel
     */
    protected $model;
    /**
     * objektum neve
     * @var string
     */
    public $_controllerName = '';
    /**
     * esemény kezelő objektum
     * @var EvoEventLogController();
     */
    public $eventLog; 
        
	// azonositó blokk
	/**
	 * objektum példány egyedi azonosító száma
	 * @var integer
	 */
	 public $id = 0;
	 /**
	  * objektum példány rövid mnemonik
	  * @var string
	  */
	  public $name = '';
	  /**
	   * objektum példány hosszabb megnevezése
	   * @var string
	   */
	  public $title = '';  
	  /**
	   * objektum példány publikus
	   * @var boolean
	   */
	  public $published = true; 
	    
	/**
	 * Életciklus blokk {use: true|false, counter:#, state:'', phases:[evoPhase, ....] }
	 * @var unknown
	 */
	public $lifeCycle; 
	    
	// családfa blokk
	    // public $parentId = 0;          // tulajdonos objektum id
	    
	// kategórák blokk
	    // public $categories = [];       //["catType" => [#,...], ...]
	    	    
	    //public $resourceState = '';  // free, reserved
	    
	// Üzleti blokk
	    // public $owner = 0;
	    // public $user = 0;
	    // public $value = '';
	    
	// egyébb adatok blokk
	    // ....
	    
	/**
	 * php object constructor
	 */
	function __construct() {
	    $this->model = $this->getModel($this->_controllerName);
	    $this->eventLog = new EvoEventLogController();
	    $this->lifeCycle = new stdClass();
	    $this->lifeCycle->state = '';
	    $this->lifeCycle->counter = 0;
	    $this->lifeCycle->phases = [];
	    $this->lifeCycle->use = true;
	    $this->create();
	}
	
	    /**
	     * közvetlen gyermekei lekérése adatbázisból
	     * @return array of EvoController' record
	    */
	    public function childs(): array {  // gyemkei array of EvoController
	        $this->model->evoGetChilds($this);
	    }
	    
	    /**
	     * tulajdonosainak lekérése adatbázisból(fel egészen a root elemig)
	     * @return array of EvoController' record
	     */
	    public function parents(): array { 
	        $this->model->evoGetParents($this);
	    }
	    	    
	    /**
	     * objektum adatok alaphelyzetbe állítása pl. új felvitel előkészítéséhez
	     * MINDEN KONKÉÁT EBBŐL SZÁRMAZTATOTT OSZTÁLYBAN FELÜLÍRANDÓ!
	     */
	    public function create() {
	        $this->_controllerName = 'controllerName';
	        $this->id = 0;                // int(11) auto increment egyedi azonosító
	        $this->name = '';             // string(128) név
	        $this->title = '';            // string(256) leírás
	        $this->published = true;      // bool közzétéve igen vagy nem
	        $this->lifeCycle->counter = 0;  // feléledés számláló
	        $this->lifeCycle->state = '';   // státusz = aktiv fázis neve
	        $this->lifeCycle->phases = [
	            "init" => new EvoPhase($this, 'init','init'),
	            "build" => new EvoPhase($this, 'build','build'),
	            "maintain" => new EvoPhase($this, 'maintain','maintain'),
	            "unbuild" => new EvoPhase($this, 'unbuild','unbuild'),
	            "archive" => new EvoPhase($this, 'archive','archive')
	        ];
	        // $this->categories = ["catType" => [], ....];
	        // $this->eventLog->_logging = false; --- ha nem kell naplózni
	    }
	    
	    /**
	     * object példány inicializálása
	     */
	    public function init() {
	        if ($this->lifeCycle->phases['init']->canItbeStarted()) {
	            if (!$this->lifeCycle->phases['init']->start('init')) {
	                EvoError::createError('error', 'application', true, 'init phase start', '', $this);
	            }
	            $this->lifeCycle->state = 'init';
	            $this->create();
	            
	            if (!$this->lifeCycle->phases['init']->end()) {
	                EvoError::createError('error', 'application', true, 'init end', '', $this);
	            }
	        } else {
	            EvoError::createError('warninrg', 'application', false, 'init cannot be started', '', $this);
	        }
	    }
	    
	    /**
	     * ojectum példány felépítése
	     */
	    public function build() {
	        if ($this->lifeCycle->phaes['build']->canItbeStarted()) {
	            if (!$this->lifeCycle->phases['build']->start('build')) {
	                EvoError::createError('error', 'application', true, 'build phase start', '', $this);
	            }
	            $this->lifeCycle->state = 'build';
	            // .....a konkrét objektum példány feléptéséhez szükséges dolgok.....
	            if (!$this->phases['build']->end()) {
	                EvoError::createError('error', 'application', true, 'build end', '', $this);
	            }
	            $this->eventLog->add($source, 'build', '');
	        } else {
	            EvoError::createError('warninrg', 'application', false, 'build cannot be started', '', $this);
	        }
	    }
	    
	    /**
	     * objektum példány publikálása és tárolása adatbázisba
	     */
	    public function publish() {
	        if ($this->lifeCycle->phaes['maintain']->canItbeStarted()) {
	            if (!$this->phases['maintain']->start('maintain')) {
	                EvoError::createError('error', 'application', true, 'maintain phase start', '', $this);
	            }
	            $this->published = true;
	            $this->save();
	            $this->eventLog->add($source, 'publish', '');
	        }
	    }
	    
	    /**
	     * objektum pédány "mainteain" állapotba helyezése
	     */
	    public function maintain() {
	        if ($this->lifeCycle->phaes['maintain']->canItbeStarted()) {
	            if (!$this->phases['maintain']->start('maintain')) {
	                EvoError::createError('error', 'application', true, 'maintain phase start', '', $this);
	            }
	            $this->lifeCycle->state = 'maintain';
	            // .....a konkrét objektum üzembehelyezéséhez szükséges dolgok ......
	            if (!$this->phases['maintain']->end()) {
	                EvoError::createError('error', 'application', true, 'maintain end', '', $this);
	            }
	        } else {
	            EvoError::createError('warninrg', 'application', false, 'maintain cannot be started', '', $this);
	        }
	    }
	    
	    /**
	     * objektum pédány modosítás kezdete
	     */
	    public function editStart() {
            // .... a konkrét objektum modosítás megkezdéséhez szükséges dolgok
	    }
	    
	    /**
	     * objektum példány modosítás vége, objektum tárolás az adatbázisba
	     */
	    public function editEnd() {
	        $this->save();
	        $this->eventLog->add($source, 'editEnd', '');
	    }
	    
	    /**
	     * objektum pédány "nem nyilvános állapotba" helyezése
	     */
	    public function unpublish() {
	        if ($thi->lifeCycle->phaes['archive']->canItbeStarted()) {
	            if (!$this->phases['archive']->start('archive')) {
	                EvoError::createError('error', 'application', true, 'archive phase start', '', $this);
	            }
	            $this->published = false;
	            $this->save();
	            $this->eventLog->add($source, 'unpublish', '');
	        }
	    }
	    
	    /**
	     * objectum pédány megszüntetse, törlése az adatbázisból
	     */
	    public function unbuild() {
	        if ($this->lifeCycle->phaes['unbuild']->canItbeStarted()) {
	            if (!$this->phases['unbuild']->start('unbuild')) {
	                EvoError::createError('error', 'application', true, 'unbuild phase start', '', $this);
	            }
	            $this->eventLog->add($source, 'unpublish', '');
	            $this->delete();
	        } else {
	            EvoError::createError('warninrg', 'application', false, 'unbuild cannot be started', '', $this);
	        }
	    }
	    
	    /**
	     * objectum példány törlése
	     */
	    public function erase() {
	        $this->unbuild();
	        $this->eventLog->add($source, 'erase', '');
	    }
	    
	    // ===============  objektum lista kezelés ==========================
	    /**
	     * objektum lista rendezése
	     * @var sql order string
	     */
	    protected $_orderedBy = '';
	    /**
	     * objektum lista 
	     * @var array of object'data
	     */
	    protected $_items = []; 
	    /**
	     * lista filter ["mezőnév" => érték] or false
	     * @var boolean
	     */
	    protected $_filter = false;
	        
	    /**
	     * az aktuális objektum példány beillesztése a lista végére
	     */
	    public function listAppend() {
	        $this->_items[] = $this->model->evoGetRecord($this);
	    }
	    
	    /**
	     * Az aktuális objektum példány beillesztése a listába az "i" -edik pozióba
	     * @param int $i
	     */
	    public function listInsert(int $i) {
	        $this->_items = array_splice( $this->_items, $i, 0, $this->model->evoGetRecord($this));
	    }
	    
	    /**
	     * lista filter beállítása 
	     * @param array $filter [{fieldname:"", relation:"=", value:érték}, ....]
	     */
	    public function listFilter(array $filter) {
	        $this->_filter = $filter;
	        $this->_items = $this->model->evoReads($this->_filter, 0, 0, $this->_orderBy);
	    }
	    
	    /**
	     * filter törlése
	     */
	    public function listUnfilter() {
	        $this->_filter = false;
	        $this->_items = $this->model->evoReads($this->_filter, 0, 0, $this->_orderBy);
	    }

	    /**
	     * átrendezés
	     * @param string $newOrder
	     */ 
	    public function listReorder(string $newOrder) {
	        $this->_orderBy = $newOrder; 
	        $this->_items = $this->model->evoReads($this->_filter, 0, 0, $this->_orderBy);
	    }
	    
	    /**
	     * az aktuális objektum példány kitörlése az objektumok listájából
	     */
	    public function listDelete() {
	       $j = -1; 
	       for ($i = 0; $i < count($this->items); $i++) {
	           if ($this->_items[$i]->id == $this->id) {
	               $j = $i;
	           }
	       }
	       if ($j >= 0) {
	           $this->_items = array_splice( $this->_items, $j, 1);
	       }
	    }
	    
	    	    
        // =============== MVC program technikai metodusok ================== 	    
	    
	    /**
	     * objektum példány beolvasása az adatbázisból
	     * @param int $id
	     * @return bool
	     */
	    protected function read(int $id):bool {
	        $result = true;
	        $this->model->evoRead($this, $id);
	        if ($this->model->getErrorMsg() != '') {
	            EvoError:createError('error', 'application', false,
	                'read data', $this->model->getErrorMsg(), $this);
	            $result = false;
	        }
	        $this->lifeCycle->ounter++;
	        return $result;
	    }
	    
	    /**
	     * objektum példány adatainak tárolása adatbázisba (insert vagy update)
	     * @return bool success or not
	     */
	    protected function save():bool {
	        if ($this->id == 0) {
	            $result = $this->model->evoInsert($this);
	        } else {
	            $result = $this->model->evoUpdate($this);
	        }
	        if ($this->model->getErrorMsg() != '') {
	            EvoError:createError('error', 'application', true,
	                'save data', $this->model->getErrorMsg(), $this);
	        }
	        return $result;
	    }
	    
	    /**
	     * objektum példány adatainak törlése az adatbázisból
	     * @return bool success or not
	     */
	    protected function delete(): bool {
	        $result = $this->model->evoDelete($this->id);
	        if ($this->model->getErrorMsg() != '') {
	            EvoError:createError('error', 'application', false,
	                'deletee data', $this->model->getErrorMsg(), $this);
	        }
	        return $result;
	    }
	    
	    /**
         * include html template
         * find first in templateDir/coverage-mm/controllerName
         *      second in plugindir/views/controllerName
         * @param string $tmplName
         */
		protected function display(string $tmplName) {
		    $tmplDir = get_template_directory();
		    if (file_exists($tmplDir.'/coverage_mm/'.$tmplName.'.php')) {
		        $path = $tmplDir.'/coverage_mm';
		    } else {
		        $path = __DIR__.'/../views';
		    }
		    include ($path.'/'.$tmplName.'.php');
		}
		
		/**
		 * create model object
		 * @param string $modelName
		 * @return Model | false
		 */
		protected function getModel(string $modelName) {
		    $result = false;
		    if (file_exists(__DIR__.'/../models/model.'.$modelName.'.php')) {
		        include_once __DIR__.'/../models/model.'.$modelName.'.php';
		        $modelClassName = ucFirst($modelName).'Model';
		        $result = new $modelClassName ($this);
		    } else {
		        $result = false;
		    }
		    return $result;
		}
		
	} // Evo class
		
?>