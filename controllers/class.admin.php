<?php
	/**
	* coverage-mm admin controller 
	*/

    include_once __DIR__.'/controller.php';
	class AdminController extends Controller  {
	    public $controllerName = 'admin';
        public $cmmMapKey = '';
        
        function __construct() {
            parent::__construct();
            $this->cmmMapKey = get_option('cmmMapKey', null);
        }
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		    $task = filter_input(INPUT_POST, 'task');
		    if ($task != '') {
		      $this->$task ();   
		    } else {
		        $this->display('adminform');
		    }
		}
		
	} // AdminController class
?>