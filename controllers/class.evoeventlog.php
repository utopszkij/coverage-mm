<?php
/**
* Evo esemény naplozó objektum   V 1.01 fejlesztői teszt változat
* 
* Készítette: Sas Tibor tervei alapján Fogler Tibor
* tibor.fogler@gmail.com
* Licensz: GNU/GPL
*/

include_once __DIR__.'/../models/model.evoeventlog.php';

/**
 * evntLog objektum
 * @author utopszkij
 */
class EvoEventLogController {
    /**
     * adatmodell
     * @var unknown
     */
    protected $model;
    /**
     * naplózás kell vagy nem
     * @var boolean
     */
    public $_logging = true;
    /**
     * azonosító szám
     * @var integer
     */
    public $id = 0;
    /**
     * esemény időpontja Y-m-d H:i:s
     * @var string
     */
    public $event_time ="";
    /**
     * bejelentkezett user nick neve
     * @var string
     */
    public $logged_user = "";
    /**
     * eseményt kiváltó objektum tipusa
     * @var string
     */
    public $source_type = "";
    /**
     * eseményt kiváltó objektum azonosítója
     * @var integer
     */
    public $source_id = 0;      // int(11) eseményt kiváltó objektum id-je
    /**
     * eseményt kiváltó objektum title adata
     * @var string
     */
    public $source_title = '';
    /**
     * esemény megnevezése
     * @var string
     */
    public $event_name = "";
    /**
     * kiegészítő adatok
     * @var string
     */
    public $event_data = "";    // text json string egyéb információk az eseménnyel kapcsolatban
    
    /**
     * konstruktor
     */
    function __construct() {
        $this->model = new EvoEventLogModel();
    }
    
    /**
     * php object destructor
     */
    function __desruct() {
    }
    
    /**
     * esemény naplóbejegyzés kiírása az adatbázisba
     * @param EvoController $source
     * @param string $eventName
     * @param string $eventData
     * @return bool succes or not
     */
    public function add(EvoController $source, string $eventName, string $eventData):bool {
        $this->model = new EvoEventLogModel();
        $this->event_time = data('Y-m-d H:i:s');
        $loggedUser = wp_get_user();
        if ($loggedUser->data->id > 0) {
            $this->logged_user = wp_get_user()->data->user_login;
        } else {
            $this->logged_user = '';
        }
        $this->source_type = $source->_controllerName;
        $this->source_id = $source->id;
        $this->source_title = $source->title;
        $this->event_name = $eventName;
        $this->event_data = $eventData;
        if ($this->_logging) {
            if (!$this->model->evoInsert($this)) {
                EvoError:createError('error', 'application', true,
                    'add eventLog', $this->model->getErrorMsg(), $sorce); 
            }
        }
        return true;
    }
    
} // class EventLogController

?>