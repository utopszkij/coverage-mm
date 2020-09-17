<?php
	/**
	* process Indicator controller 
	*/

    include_once __DIR__.'/controller.php';
	class ProgressindicatorController extends Controller  {
	    protected $controllerName = 'processindicator';
	    public function show() {
	        ?>
	        <script type="text/javascript">
	        jQuery('#progressIndicator').show();
	        </script>
	        <?php
    	}
	    
	   public function hide() {
	        ?>
	        <script type="text/javascript">
	        jQuery('#progressIndicator').hide();
	        </script>
	        <?php
	   }
	    
	   public function setup(bool $show, int $total, int $skip) {
	        if ($show) {
	            $this->prIndClass = 'block';
	        } else {
	            $this->prIndClass = 'none';
	        }
	        $this->prIndTotal = $total;
	        $this->prIndSkip = $skip;
	        $this->display('progressindicator');
	   }
		
	} // class
?>