<?php
	/**
	* coverage-mm distributor controller 
	*/

    include_once __DIR__.'/controller.php';
	class DistributorController extends Controller  {
	    protected $controllerName = 'distributor';

	    public $id = 0;
	    public $nick = '';
	    public $name = '';
	    public $email = '';
        public $planned = '';
        public $planned_unit = '';
	    public $state = '';
	    public $enable_start = '';
	    public $enable_end = '';
	    
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('distributor.adminform');
		}
		
	} // class
?>