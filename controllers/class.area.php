<?php
	/**
	* coverage-mm area admin controller 
	*/

    include_once __DIR__.'/controller.php';
    include_once __DIR__.'/class.csvimport.php';
    include_once __DIR__.'/class.progressindicator.php';
    
    class AreaCsvImport extends CsvimportController {
         
        protected function beforeProcess() {
            global $cmm;
            $this->step = 20;  // process line / step
            $this->pause = 2000; // step by step pause ms
            $this->task = 'import_csv2';
            $this->prIndUrl = get_site_url().'/wp-admin/admin.php?page=cmm-areas';
            $country = filter_input(INPUT_POST, 'country');
            $this->model = $this->getModel('area');
            if ($country != '') {
                $res = $this->model->getByName($country);
                if ($res) {
                    $cmm->setToSession('country_id',$res->id);
                    $this->country_id = $res->id;
                } else {
                    $cmm->setToSession('country_id',0);
                    $this->country_id = 0;
                }
                $cmm->setToSession('prevParent','@@@');
                $cmm->setToSession('parent_id',0);
                $this->prevParent = '@@@';
                $this->counter = 0;
                $this->parent_id = 0;
            } else {
                $this->prevParent = $cmm->getFromSession('prevParent','@@@');
                $this->counter = $cmm->getFromSession('counter',0);
                $this->parent_id = $cmm->getFromSession('parent_id',0);
                $this->country_id = $cmm->getFromSession('country_id',0);
            }
        }
        
        protected function processData(array $data,array $colNames) {
            global $cmm;
            $record = new stdClass();
            for ($i=0; $i < count($data); $i++) {
                if ($i < count($colNames)) {
                    $fieldName = $colNames[$i];
                    $record->$fieldName = $data[$i];
                }
            }
            if (!isset($record->isarea)) {
                $record->isarea = 1;
            }
            $record->id = 0;
            
            // "parent" field is parent'Name !    
            if ($record->parent != $this->prevParent) {
                if ($record->parent == '') {
                    // root item, not parent
                    $this->parent_id = 0;   
                } else {
                    // insert parent if not exists
                    $parentRecord = new AreaRecord();
                    $this->model->copy($record, $parentRecord);
                    $parentRecord->id = 0;
                    $parentRecord->name = $record->parent;
                    $parentRecord->slug = '';
                    $parentRecord->area_category = 'region';
                    $parentRecord->parent = $this->country_id;
                    $parentRecord->isarea = $record->isarea;
                    $parentRecord->population = 0;
                    $parentRecord->place = 0;
                    $this->parent_id = $this->model->getOrAddArea($parentRecord);
                }
            }
            $this->prevParent = $record->parent;
            // current area insert or update
            $record->id = 0;
            $record->parent = $this->parent_id;
            $record->slug = '';
            if ($record->isarea == '') {
                $parentRecord->isarea = 1;
            }
            $areaRecord = $this->model->init();
            $this->model->copy($record, $areaRecord);
            $this->model->getOrAddArea($areaRecord);
            $this->counter = $this->counter + 1;
        }

        protected function afterProcess(int $num) {
            $this->msg = __('csv_loaded',CMM).' ('.
                __('readed',CMM).':'.$num.' '.__('writed',CMM).':'.$_SESSION['counter'].')';
            $this->msgClass = 'info notice';
            $this->display('area.adminform');
            unset($_SESSION['counter']);
            unset($_SESSION['country_id']);
            unset($_SESSION['prevParent']);
            unset($_SESSION['parent_id']);
            
            echo '<p>'.date('Y-m-s H:i:d:s').'</p>';
        }
        
        protected function errorMessage(string $msg) {
            $this->msg = __($msg,CMM);
            $this->msgClass = 'error notice';
            $this->display('area.adminform');
            unset($_SESSION['counter']);
            unset($_SESSION['country_id']);
            unset($_SESSION['prevParent']);
            unset($_SESSION['parent_id']);
        }
    } // class
    
    class AreaController extends Controller  {
	    protected $controllerName = 'area';
	    
		/**
		 * echo admin form
		 */
		public function adminForm() {
		    $task = filter_input(INPUT_POST, 'task');
		    if ($task != '') {
		        $this->$task ();
		    } else {
		        // echo progress indicator
		        $this->progressindicator = $this->getController('progressindicator');
		        $this->progressindicator->setup(false, 0, 0);
		        $this->display('area.adminform');
		    }
		}
		
		/**
		 * import from csv 1. form
		 */
		public function import_csv1() {
		    $this->progressindicator = $this->getController('progressindicator');
		    $this->progressindicator->setup(false, 0, 0);
		    $this->display('area.import_csv1');
		}
		
		/**
		 * import from csv 2. form
		 */
		public function import_csv2() {
		    $areaCsvImport = new AreaCsvImport();
            $areaCsvImport->import();
		}

		/**
		 * export one area and this childs
		 * @param string $parentName
		 * @param AreaRecord $record
		 * @param fileHandler $fp
		 */
		protected function exportCsv1(string $parentName, AreaRecord $record, $fp) {
            $record->parent = $parentName;	
            $line = '';
		    foreach ($record as $fv) {
		            $line .= '"'.$fv.'",';
		    }
		    $line .= '""';
		    fwrite($fp, $line."\n");
		    $childs = $this->model->getChilds($record->id);
            foreach ($childs as $child) {
                $this->exportCsv1($record->name, $child, $fp);
            }
		}
		
		/**
		 * export all product_cat to csv file
		 * the csv file write into plugin_dir/work, filename = session_id()
		 */
		public function export_csv() {
		    $fileName = __DIR__.'/../work/'.session_id().'.csv';
		    $fp = fopen($fileName,'w+');
		    $roots = $this->model->getChilds(0);
		    if (count($roots) > 0) {
		        $root = $roots[0];
		        $line = '';
		        foreach ($root as $fn => $fv) {
	                $line .= '"'.$fn.'",';
		        }
		        $line .= '"end"';
		        fwrite($fp,$line."\n");
		    }
	        foreach ($roots as $root) {
		       $this->exportCsv1('',$root,$fp);
		    }
		    fclose($fp);
		    $this->display('exportcsv');
		}
		
		/**
		 * delete session_id() csv file from work dir
		 */
		public function delcsv() {
		    $fileName = __DIR__.'/../work/'.session_id().'.csv';
		    unlink($fileName);
		}
		
	} // class
?>