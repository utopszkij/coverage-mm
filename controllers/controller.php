<?php
	/**
	* coverage-mm base controller 
	* every controller is a descendant of this
	*/

	class Controller {
	    protected $controllerName = '';
	    protected $model;
	    protected $lastError = '';
	    
	    /**
	     * object properties
	     */
	    public $id = 0;
	    // ...
	    
	    /**
	     * php object constructor
	     */
	    function __construct() {
	        $this->model = $this->getModel($this->controllerName);
	        $this->create();
	    }
	    
	    /**
	     * php object destructor
	     */
	    function __desruct() {
	        $this->kill();
	    }
	    
	    /**
	     * main constructor
	     */
	    public function create() {
	    }
	    
	    /**
	     * main objecct destructor
	     */
	    public function  kill () {
	    }
	    
	    /**
	     * insert a new object into database
	     * @return int
	     */
	    public function insert():int {
	        return $this->model->insert();
	    }
	    
	    /**
	     * update exists object in database
	     * @return bool
	     */
	    public function modify():bool {
	        return $this->model->modify();
	    }
	    
	    /**
	     * remove exists object from database
	     * @return bool
	     */
	    public function remove():bool {
	        return $this->model->remove();
	    }
	    
	    /**
	     * get last action errorMsg. if OK result = '';
	     * @return string
	     */
	    public function getErrorMsg(): string {
	        return $this->model->getErrorMsg();
	    }
	    
	    /**
	     * erase object from database
	     */
	    public function erase() {
	        $this->remove();
	    }
	    
	    /**
	     * init propertys for insert
	     */
	    public function init() {
	        $this->id = 0;
	        // ...
	    }
	    
	    /**
	     * move object
	     */
	    public function move() {
	    }
	    
	    /**
	     * select object
	     */
	    public function select() {
	    }
	    
	    /**
	     * deselect object
	     */
	    public function deselect() {
	    }
	    
	    /**
	     * sort object
	     */
	    public function sort() {
	    }
	    
        /**
         * include html template
         * find first in templateDir/coverage-mm/controllerName
         *      second in plugindir/views/controllerName
         * @param string $tmplName
         */
		protected function display(string $tmplName) {
		    $tmplDir = get_template_directory();
		    if (file_exists($tmplDir.'/coverage_mm/'.$tmplName.'.php')) {
		        $path = $tmplDir.'/coverage_mm';
		    } else {
		        $path = __DIR__.'/../views';
		    }
		    include ($path.'/'.$tmplName.'.php');
		}
		
		/**
		 * create model object
		 * @param string $modelName
		 * @return Model | false
		 */
		protected function getModel(string $modelName) {
		    $result = false;
		    if (file_exists(__DIR__.'/../models/model.'.$modelName.'.php')) {
		        include_once __DIR__.'/../models/model.'.$modelName.'.php';
		        $modelClassName = ucFirst($modelName).'Model';
		        $result = new $modelClassName ($this);
		    } else {
		        $result = false;
		    }
		    return $result;
		}
		
		/**
		 * create controller object
		 * @param string $controllerlName
		 * @return Controller | false
		 */
		protected function getController(string $controllerName) {
		    $result = false;
		    if (file_exists(__DIR__.'/class.'.$controllerName.'.php')) {
		        include_once __DIR__.'/class.'.$controllerName.'.php';
		        $controllerClassName = ucFirst($controllerName).'Controller';
		        $result = new $controllerClassName ();
		    } else {
		        $result = false;
		    }
		    return $result;
		}
	} // Controller class
		
?>