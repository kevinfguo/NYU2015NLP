<?php
	require 'helper.php';
	ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

	Class ANEWLex implements WordList{
		private $dictionary = array();

		public function makeLex(){
			$file_handler = fopen("ANEW-all-valence.txt", "r");
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

		public function getLex(){
			return $this->dictionary;
		}
	}
	
?>