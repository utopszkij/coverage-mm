<?php
	/**
	* coverage-mm base model. Use one table and ACF fields 
	* use "cmm_{modelName}" ACF group
	* every model is a descendant of this
	*/

    include_once __DIR__.'/model.php';
	class PictogramsablonModel extends EvoModel {
		/**
		 * php object constructor
		 */ 
		function __construct($controller = false) {
		    global $wpdb, $cmm;
		    parent::__construct($controller);
		    $this->modelName = "picturesablon";  
		    $wpdb->query('
            CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_'.$this->modelName.' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(128),
                `title` varchar(128),
                `published` decimal(1),
                `media` varchar(256),
                `txt` text,
                `backgroundcolor` varchar(32),
                `created` varchar(32),
                `modified` varchar(32),
                `author` decimal(11),
                `categories` varchar(256),
                PRIMARY KEY (`id`)
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
		        $cmm->errorExit($this->lasterror);;
		    }
		}  // construct
		
		/**
		 * rekord készlet olvasása
		 * @param int $offset
		 * @param int $limit
		 * @param string $filterstr
		 * @param string $order
		 * @return object {total:#, items:[ .....]}
		 */
		public function getRecords(int $offset, int $limit, string $filterstr, string $order) {
		    $result = new stdClass();
		    $result->total = 0;
		    $result->items = [];
		    return $result;
		}
		
		
	} // Model class
?>