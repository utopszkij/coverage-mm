<?php
/**
 * ELAVULT, NE HASZNÁLD!
 * 
 * coverage_cmm rendszer esemény kezelő objektum
 * @author utopszkij
 * 
 */   
class EventController {
    
    /**
     * insert into database 
     */
    public function insert(string $description, string $data) {
        // event rekord, tárolása adatbázisba use wp_get_user() -t is  
        // date('Y-m-d H:i:s');
        // rekord kiirása az adatbázisba.
    }
    
    /**
     * list events from database
     * @param string $className
     * @param int $classId
     * @param dateTimestring $from
     * @param dateTimeString $to
     */
    public function list(string $objectName = '', int $objectId = 0, string  $from = '',  string $to = ''): array {
        
    }
}
