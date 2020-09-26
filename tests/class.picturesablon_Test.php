<?php

/**
 * unit test
 * use:  cd /pluginpath
 *       phpunit tests
 */
declare(strict_types=1);
define ('UNITTEST',1);
include_once './tests/mock.php';
include_once './controllers/class.evo.php';
include_once './controllers/class.cmm.php';
include_once './controllers/class.pictogramsablon.php';

use PHPUnit\Framework\TestCase;

global $testId;

// test Cases
class classlTest extends TestCase {
    protected $controller;
    
    function __construct() {
        parent::__construct();
        $this->controller = new PictogramSablonController();
    }
    public function test_start() {
        // create and init test database
        //databaseInit();
        $this->assertEquals('','');
    }
    
    public function test_init() {
        // global $database,$testId;
        // $this->assertEquals(0, $this->class->id);
        $this->assertEquals('','');
    }
    
    public function test_addform() {
        global $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $this->controller->addform();
        $this->assertEquals('','');
    }
    
    public function test_editform_ok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = true;
        $this->controller->editform();
        $this->assertEquals('','');
    }
    
    
    public function test_editform_notok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = false;
        $this->controller->editform();
        $this->assertEquals('','');
        $this->expectOutputRegex('/not found/');
    }
    
    public function test_savetask() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $this->controller->savetask();
        $this->assertEquals('','');
    }
    
    public function test_suredelete_ok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = true;
        $this->controller->suredelete();
        $this->assertEquals('','');
    }
    
    public function test_suredelete_notok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = false;
        $this->controller->suredelete();
        $this->assertEquals('','');
        $this->expectOutputRegex('/not found/');
    }
    
    public function test_dodelete_ok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = true;
        $this->controller->dodelete();
        $this->assertEquals('','');
    }
    
    public function test_dodelete_notok() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] ='1';
        $wpdb->initResults();
        $wpdb->setResult = false;
        $this->controller->dodelete();
        $this->assertEquals('','');
        $this->expectOutputRegex('/not found/');
    }
    
    public function test_browserform() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] = '1';
        $this->controller->browserform();
        $this->assertEquals('','');
    }
    
    public function test_importcsv() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] = '1';
        $this->controller->importcsv();
        $this->assertEquals('','');
    }
    
    public function test_exportcsv() {
        global $wpdb, $cmm;
        $cmm->setToSession('csrToken','1');
        $_POST['1'] = '1';
        $this->controller->exportcsv();
        $this->assertEquals('','');
    }
    
    public function test_sortcode() {
        $this->controller->sortcode([]);
        $this->assertEquals('','');
    }
    
}


