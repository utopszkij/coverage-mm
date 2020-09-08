<?php
	/**
	* coverage-mm admin controller 
	*/

    include_once __DIR__.'/controller.php';
	class AdminController extends Controller  {
	    public $controllerName = 'admin';

		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('adminform');
		}
		
	} // AdminController class
?>