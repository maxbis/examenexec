<?php

// this file contains my own helper that can be called static from any controller.
// e.g. MyHelpers::checkIP();

namespace app\controllers;

class MyHelpers
{
    public function checkIP() {
        if ( $_SERVER['REMOTE_ADDR'] == '::1' ) return; // php yii server
    
        $file = "../config/ipAllowed.txt";
    
        try { // read file and if not readble raise error and stop
            $lines = file($file);
         } catch (Exception $e) {
            $string = "Cannot acces IP Allowed file ($file) in config";
            writeLog($string);
            echo $string;
            exit;
         }
    
         $ipAllowed=[]; // all lines vlaidated will be put in this array
         for($i=0; $i<count($lines); $i++) {
            $ip = explode(' ',trim($lines[$i]))[0]; // we want teh first word
            if(filter_var(explode('/',$ip)[0], FILTER_VALIDATE_IP)) { // and we want anything beofre the / (note ip = xxx.xxx.xxx.xxx/xx)
                $ipAllowed[] = $ip; // ipnumber validate (note that subnet mask is not validated)
            }
            
         }
         //for($i=0; $i<count($ipAllowed); $i++) {
         //   $a =  self::ipRange($ipAllowed[$i]);
         //   d($a);
         //}
    
        $weAreOK=false;
        foreach ($ipAllowed as $item) {
            $ipRange = self::ipRange($item);
            if ( (int)$_SERVER['REMOTE_ADDR'] >= (int)$ipRange[0] && (int)$_SERVER['REMOTE_ADDR'] <= (int)$ipRange[1] ) {
                $weAreOK=true;
            }
        }
        if ( $weAreOK == false ) {
            $string = "Permission denied for ". $_SERVER['REMOTE_ADDR'];
            writeLog($string);
            echo $string;
            exit;
        }
    }

    private function ipRange($cidr)
    {
        $range = array();
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        return $range;
    }
}
?>