<?php
	/**
	* coverage-mm base model 
	* every model is a descendant of this
	*/

	class Model {
	    protected $controller;
	    protected $modelName = 'model';
	    protected $lastError = '';
	    
		/**
		 * php object constructor
		 */ 
		function __construct($controller) {
		    $this->controller = $controller;
		}
	
		/**
		 * insert a new object into database from controller properties
		 * @return int
		 */
		public function insert():int {
		    $this->lastError = '';
		    return 0;
		}
		
		/**
		 * update exists object in database from controller properties
		 * @return bool
		 */
		public function modify():bool {
		    $this->lastError = '';
		    return true;
		}
		
		/**
		 * remove exists object from database by controller->id
		 * @return bool
		 */
		public function remove():bool {
		    $this->lastError = '';
		    return 0;
		}
		
		/**
		 * get last action errorMsg. if OK result = '';
		 * @return string
		 */
		public function getErrorMsg(): string {
		    return $this->lastError;
		}

	} // Model class
?>