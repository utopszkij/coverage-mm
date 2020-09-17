<?php
	/**
	* coverage-mm product admin controller 
	*/

    include_once __DIR__.'/controller.php';
	class ProductController extends Controller  {
	    protected $controllerName = 'product';
	    
	    public $id = 0;
	    public $slug = '';
	    public $name = '';
	    public $description = '';
	    public $image = '';
	    public $galery = [];
	    public $unit = '';
	    public $stock = 0;
	    public $planned = 0;
	    public $planned_unit = '';
        public $state = '';
	    public $use_start = '';
	    public $use_days = 0;
	    public $enable_start = '';
	    public $enable_end = '';
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('product.adminform');
		}
		
	} // class
?>