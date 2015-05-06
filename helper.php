<?php
ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

function printpre($obj) {
    echo '<pre>';
    if(is_object($obj) || is_array($obj)) {
        print_r ($obj);
    } else {
       echo $obj;
    } 
    echo '</pre>';
  }
?>