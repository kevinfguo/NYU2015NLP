<?php
	require 'helper.php';
	ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

	Class Lex{
		private $dictionary = array();

		public function makeLex($file){
			$file_handler = fopen($file, "r");
			while (!feof($file_handler)){
				$line = fgets($file_handler);
				$line = explode("\t", $line);
				$this->dictionary[$line[0]] = $line[1];
			}
			fclose($file_handler);
		}

		public function showLex(){
			foreach($this->dictionary as $key => $value){
				echo $key;
				echo " ";
				echo $value;
				echo "<br>";
			}
		}
	}
	
?>