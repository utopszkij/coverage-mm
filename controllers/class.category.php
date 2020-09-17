<?php
	/**
	* coverage-mm category controller 
	*/

    include_once __DIR__.'/controller.php';
	class CategoryController extends Controller  {
	    protected $controllerName = 'category';


	    public $id = 0;
	    public $slug = '';
	    public $name = '';
	    public $type = 'area';
	    public $parent_id = '';
	    public $place = 0;
       public $planned = '';
       public $planned_unit = '';
	    public $state = '';
	    public $enable_start = '';
	    public $enable_end = '';
	     
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('category.adminform');
		}
		
	} // class
?>