<?php
global $cmm;
/**
 * cmm rooter object, controller creation and session processing
 */
class Cmm {
    protected $cmm_cookie;
    protected $sessionBuffer = false;
    
    function __construct() {
        global $wpdb;

        // create PKPSESSID cookie if not exists
        if (!session_id()) {
            session_start();
        }
        $this->cmm_cookie = session_id();
                
        // create session table if not exists
        if (!$wpdb->query('CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'cmm_sessions (
            session_id varchar(32),
            name varchar(32),
            value text,
            expire varchar(32),
            key session_id_ind (session_id)
            )
        ')) {
            $this->errorExit($wpdb->last_error);
        };
        
        // delete old sessionvars from database
        $wpdb->query('DELETE FROM '.$wpdb->prefix.'cmm_sessions  
        WHERE expire < "'.strtotime('-1 day').'"');
        if ($wpdb->last_error != '') {
            $this->errorExit($wpdb->last_error);
        }
        
        // get session vars from database into $this->sessionBuffer
        $res = $wpdb->get_row('SELECT * 
        FROM '.$wpdb->prefix.'cmm_sessions 
        WHERE session_id = "'.$this->cmm_cookie.'"');
        if ($res) {
            $this->sessionBuffer = JSON_decode($res->value);
        } else {
            $this->sessionBuffer = new stdClass();
        }
    }

    function __destruct() {
        global $wpdb;
        
        // save $this->sessionBuffer into database
        $rec = [];
        $rec["session_id"] = $this->cmm_cookie;
        $rec["name"] = "data";
        $rec["value"] = JSON_encode($this->sessionBuffer);
        $rec["expire"] = strtotime('+1 day');
        $res = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'cmm_sessions
                WHERE session_id = "'.$this->cmm_cookie.'" AND name = "data"');
        if ($wpdb->last_error != '') {
            $this->errorExit(' 2 '.$wpdb->last_error);
        }
        if ($res) {
            if (!$wpdb->update($wpdb->prefix.'cmm_sessions', $rec,
                ["session_id" => $this->cmm_cookie, "name" => data])) {
                    $this->errorExit($wpdb->last_error);
            }
        } else {
            if (!$wpdb->insert($wpdb->prefix.'cmm_sessions', $rec)) {
                    $this->errorExit($wpdb->last_error);
            }
        }
    }
    
    /**
     * get value from session table
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function getFromSession(string $name, $default='') {
        if (isset($this->sessionBuffer->$name)) {
            $result = $this->sessionBuffer->$name;
        } else {
            $result = $default;
        }
        return $result;
    }
    
    /**
     * set value into session table
     * @param string $name
     * @param unknown $value
     * @return unknown
     */
    public function setToSession(string $name, $value) {
        $this->sessionBuffer->$name = $value;
        return;
    }
    
    /**
     * delete value from session table
     * @param string $name
     */
    public function deleteFromSession(string $name) {
        unset($this->sessinBuffer->$name);
    }
    
    /**
     * get HTML params from GET, POST, session
     * @param string $name
     * @param unknown $defValue
     */
    public function getParam(string $name, $defValue = '') {
        $result = $defValue;
        if (isset($_GET[$name])) {
            $result = $_GET[$name];
        } else if (isset($_POST[$name])) {
            $result = $_POST[$name];
        }
        return $result;
    }
    
    /**
     * include and create controller
     * @param string $name
     * @return unknown|boolean
     */
    public function getController(string $name) {
        if (file_exists(__DIR__.'/controllers/class.'.$name.'.php')) {
            include_once __DIR__.'/controllers/class.'.$name.'.php';
            $className = ucFirst($name).'Controler';
            return new $className ();
        } else {
            return false;
        }
    }
    
    /**
     * csr token létrehozás és tárolás sessionba
     */
    public function createCsrToken(): string {
        $result = rand(1000000,9000000);
        $this->setToSession('csrToken', $result);
        return $result;
    }
    
    /**
     * csr token ellenörzés
     */
    public function checkCsrToken(): bool {
        if (defined('UNITTEST')) {
            return true;
        }
        $result =  ($this->getParam($this->getFromSession('csrToken')) != '1');
        if (!$result) {
            echo 'csr token error'; exit();
        }
        return $result;
    }
    
    /**
     * csak admin oldalon használható funkció
     */
    public function checkAdmin(): bool {
        $result =  is_admin();
        if (!$result) {
            AvoError::createError('error', 'application', $true, 'only asmin use this function', '', $this);
        }
        return $result;
    }
    
    /**
     * elavult funkció, helyette az AvoError:createError a javasolt
     * @param string $msg
     */
    public function errorExit(string $msg) {
        echo $msg; 
        exit();
    }
    
} // class cmm
$cmm = new Cmm();
?>