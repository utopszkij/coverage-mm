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
include_once __DIR__.'/model.evomodel.php';

class EvoEventLogModel extends EvoModel {

    public $id = 0;            // int(11) autoincremet, egyedi azonosító szám
    public $eventtime ="";     // datetime esemény bekövetkezés időpontja Y.m.d H:i:s
    public $loggedUser = "";   // int(11) bejelntkezett user nickName, vagy ''
    public $sourceType = "";   // string(64) eseményt kiváltó objektum tipusa
    public $sourceId = 0;      // int(11) eseményt kiváltó objektum id-je
    public $sourceTitle = '';  // string(128) esemny kiváltó objekt title adata
    public $eventName = "";    // sring(64) esemény token pl:insert, update...
    public $eventData = "";    // text json string egyéb információk az eseménnyel kapcsolatban
    
    
    function __construct($controller = false) {
        parent::__construct($controller);
        $this->modelName = 'evomodel';
        global $wpdb;
        $wpdb->query('
            CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_'.$this->modelName.' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `event_time` varchar(32),
                `logged_user` varchar(32),
                `source_type` varchar(128),
                `source_id` dint(11),
                `source_title` varchar(256),
                `event_name` varchar(32),
                `event_data` text,
                PRIMARY KEY (`id`),
                KEY `source_ind` (`source_id`),
                KEY `user_ind` (`logged_user`)
             );
        ');
        
    }

}
?>