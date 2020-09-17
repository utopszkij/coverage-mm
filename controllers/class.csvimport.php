<?php
	/**
	* coverage-mm CSV import  
	*/

    include_once __DIR__.'/controller.php';
	class CsvimportController extends Controller  {
	    protected $controllerName = 'csvimport';
	    protected $step = 20; // process /page refresh
	    protected $pause = 2000; // page refresh pause ms
	    protected $task = '';
	    protected $prIndUrl = '';
	    
	    protected function beforeProcess() {
	        // setup $step, $pause, $task, $prIndUrl
	        // get extra data from POST, set data into $_SESSION
	    }
	    
	    protected function processData(array $data,array $colNames) {
            // process csv data line	        
	    }
	    
	    protected function afterProcess(int $num) {
	        // echo success message
	        echo '<p>csv loaded '.$num.'</p>';
	    }
	    
	    protected function errorMessage(string $msg) {
	        echo '<p>error '.$msg.'</p>';
	    }
	    
	    /**
		 * csv file bolvasása
		 * reqied POSTs: csvFieldSeparator, csvFile, optional POSTs: skip, total, targetFile
		 */
		public function import() {
		    $errorMsg = '';
		    $targetFile = __DIR__.'/../work/'.date(YmdHis).'.tmp'; // workfile path
		    $csvFieldSeparator = filter_input(INPUT_POST, 'csvFieldSeparator'); // csv field separator
		    $skip = filter_input(INPUT_POST, 'skip'); // skip csv line
		    $total = 0; // total csv line
		    $handle = false; // csv file read handler
		    $this->beforeProcess();
		    if ($skip != '') {
		        $targetFile = filter_input(INPUT_POST, 'targetFile');
		        $total = filter_input(INPUT_POST, 'total');
		        $handle = fopen($targetFile, "r");
		    } else {
		        $skip = 0;
		        if (move_uploaded_file($_FILES["csvFile"]["tmp_name"], $targetFile)) {
		            // calculate $total;
		            $total = 0;
		            if (($handle = fopen($targetFile, "r")) !== FALSE) {
		                while (($data = fgetcsv($handle, 100000, $csvFieldSeparator)) !== FALSE) {
		                  $total++;
		                }
		            }
		            fclose($handle);
		            $handle = fopen($targetFile, "r");
		        } else {
		            $errorMsg = 'not_uploaded_file';
		        }
		    }
		    if (!$handle) {
		        $errorMsg = 'csv_import_not_valid_handler';
		    }
		    // echo progress indicator
		    $this->progressindicator = $this->getController('progressindicator');
		    $this->progressindicator->setup(true, $total, $skip);
		    if ($errorMsg == '') {
                    $colNames = [];
                    $mum = 0;
		            while (($data = fgetcsv($handle, 10000, $csvFieldSeparator)) !== FALSE) {
		                    if ($num == 0) {
		                        $colNames = $data;
		                    } else {
		                        if ($num > $skip) {
		                            $this->processData($data,$colNames);
		                        }
		                    }
		                    $num++;
		                    if ($num > $skip + $this->step) {
		                      $skip = $skip + $this->step;
		                      fclose($handle);
		                      ?>
		                      <div style="display:none">
                            	<form method="post" enctype="multipart/form-data" id="formCsv"
                            	      action="<?php echo $prIndUrl; ?>">
                            	      <input type="text" name="task" value="<?php echo $this->task; ?>" />
                            	      <input type="text" name="csvFieldSeparator" value="<?php echo $csvFieldSeparator; ?>" />
                            	      <input type="text" name="skip" value="<?php echo $skip; ?>" />
                            	      <input type="text" name="targetFile" value="<?php echo $targetFile; ?>" />
                            	      <input type="text" name="total" value="<?php echo $total; ?>" />
                            	      <button type="submit">OK</button>
								</form>
								<script type="text/javascript">                      
								window.setTimeout("jQuery('#formCsv').submit()",<?php echo $this->pause; ?>);
								</script>
		                      </div>
		                      <?php 
		                      return;
		                    } 
		            } // beolvasó ciklus
		            fclose($handle);
		            unlink($targetFile);
		            $this->progressindicator->hide();
		            $this->afterProcess($num);
		    } else {
		        $this->errorMessage($errorMsg);
		    }
		} // import()
	} // class
?>