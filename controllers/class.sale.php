<?php
	/**
	* coverage-mm sale admin controller 
	*/

    include_once __DIR__.'/controller.php';
	class SaleController extends Controller  {
	    protected $controllerName = 'sale';
	    
	    public $id = 0;
	    public $customer_id = 0;
	    public $product_id = 0;
	    public $quantity = 0;
	    public $unit = '';
	    public $price = 0;
	    public $currency = 'EUR';
	    public $date = '';
	    public $state = '';
	    public $distributor_id = 0;
	    public $agent_id = 0;
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		     $this->display('sale.adminform');
		}
		
	} // class
?>