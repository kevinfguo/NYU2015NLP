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

function consensus($res){
	$parts = str_split($res);
	$pos = 0; $neg = 0; $neut = 0;
	foreach($parts as $opinion){
		if ($opinion == 'N'){
			$neut++;
		}else if ($opinion == '+'){
			$pos++;
		}else if ($opinion == '-'){
			$neg++;
		}
	}
	if ($pos > $neg && $pos > $neut){
		return '+';
	}else if ($neg > $pos && $neg > $neut){
		return '-';
	}else{
		return 'N';
	}
}
?>