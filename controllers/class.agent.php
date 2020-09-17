<?php
	/**
	* coverage-mm agent controller 
	*/

    include_once __DIR__.'/controller.php';
	class AgentController extends Controller  {
	    protected $controllerName = 'agent';

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
		     $this->display('agent.adminform');
		}
		
	} // class
?>