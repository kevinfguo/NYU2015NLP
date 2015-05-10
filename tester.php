<?php
	ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

	$AFINNlexer = new AFINNLex();
	$AFINNlexer->makeLex();
	//$AFINNlexer->showLex();
	$AFINNdictionary = $AFINNlexer->getLex();
	//printpre($dictionary);

	$LabMTlexer = new LabMTLex();
	$LabMTlexer->makeLex();
	$LabMTdictionary = $LabMTlexer->getLex();

	$ANEWlexer = new ANEWLex();
	$ANEWlexer->makeLex();
	$ANEWdictionary = $ANEWlexer->getLex();

	$file = fopen("testdata.manual.2009.06.14.csv","r");
	print_r(fgetcsv($file));
	fclose($file);
	
?>