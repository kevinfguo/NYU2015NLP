<?php

	require 'helper.php';
	ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

	interface WordList{
		public function makeLex();
		public function showLex();
		public function getLex();
	}

?>