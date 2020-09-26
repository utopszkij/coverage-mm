<?php 
/**
 * ELAVULT NE HASZNÁLD!
 * 
 * coverage_cmm error kezelő objektum
 * @author utopszkij
 *
 */

include_once __DIR__.'/class.event.php';
class ErrorController {
    public $handler = false;
    
    public function createError(string $state, string $level, bool $fatal, $code = '', $string = '') {
        $event = new EventController();
        $s = 'state='.$state.' level='.$level.' code='.$code;
        if ($fatal) {
            $s = 'coverage_cmm Fatal error '.$s;
        } else {
            $s = 'covergae_cmm error '.$s;
        }
        $event->insert($s, $string);
        if ($handler) {
            handler($state, $level, $fatal, $code, $string);
        } else if ($fatal) {
            echo $s.' '.$string; exit();
        }
    }
}
