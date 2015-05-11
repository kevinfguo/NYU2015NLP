<?php
	require_once 'helper.php';
	require_once 'lex.php';
	require_once 'wordList.php';
	require_once 'AFINNlex.php';
	require_once 'LabMTlex.php';
	require_once 'ANEWlex.php';
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
	echo '<table style="width:100%">';
	echo '<tr><th>Tweet body</th><th>Key Eval</th><th>System Eval</th><th>Agreement</th></tr>';
	$n=0;
	$correct=0;
	while(!feof($file)){
		$item = fgetcsv($file);
		$text = strtolower($item[5]);
		$text = preg_replace('/[^a-z0-9 ]+/', ' ', $text);
		$text = preg_replace('/\s+/', ' ', $text);
		$text = trim($text);
		$text = explode(' ', $text);
		$AFINNvals = array();
		$LabMTvals = array();
		$ANEWvals = array();
		$x = 0; $y = 0; $z = 0;
		foreach ($text as $word){
			if (array_key_exists($word, $AFINNdictionary)){
				$AFINNvals[] = $AFINNdictionary[$word];
				$x++;
			}else{
				$AFINNvals[] = 0;
			}
			if (array_key_exists($word, $LabMTdictionary)){
				$LabMTvals[] = $LabMTdictionary[$word];
				$y++;
			}else{
				$LabMTvals[] = 0;
			}
			if (array_key_exists($word, $ANEWdictionary)){
				$ANEWvals[] = $ANEWdictionary[$word];
				$z++;
			}else{
				$ANEWvals[] = 0;
			}
		}
		$eval = '';
		if ($x == 0){
			$AFINN_tweet_valence = 0;
			$eval = $eval.'N';
		}else{
			$AFINN_tweet_valence = array_sum($AFINNvals)/$x;
			if ($AFINN_tweet_valence > 0){
				$eval = $eval.'+';
			}else if ($AFINN_tweet_valence < 0){
				$eval = $eval.'-';
			}else{
				$eval = $eval.'N';
			}
		}
		if ($y == 0){
			$LabMT_tweet_valence = 0;
			$eval = $eval.'N';
		}else{
			$LabMT_tweet_valence = array_sum($LabMTvals)/$y;
			if ($LabMT_tweet_valence > 0){
				$eval = $eval.'+';
			}else if ($LabMT_tweet_valence < 0){
				$eval = $eval.'-';
			}else{
				$eval = $eval.'N';
			}
		}
		if ($z == 0){
			$ANEW_tweet_valence = 0;
			$eval = $eval.'N';
		}else{
			$ANEW_tweet_valence = array_sum($ANEWvals)/$z;
			if ($ANEW_tweet_valence > 0){
				$eval = $eval.'+';
			}else if ($ANEW_tweet_valence < 0){
				$eval = $eval.'-';
			}else{
				$eval = $eval.'N';
			}
		}
		echo '<tr>';
		echo '<td>'.$item[5].'</td>'; 
		echo '<td>'.$item[0].'</td>'; 
		echo '<td>'.consensus($eval).'</td>';
		if ($item[0] == 4 && consensus($eval) == '+'){
			echo '<td>'.'Yes'.'</td>'; 
			$correct++;
		}else if($item[0] == 0 && consensus($eval) == '-'){
			echo '<td>'.'Yes'.'</td>'; 
			$correct++;
		}else if($item[0] == 2 && consensus($eval) == 'N'){
			echo '<td>'.'Yes'.'</td>'; 
			$correct++;
		}else{
			echo '<td>'.'No'.'</td>'; 
		}
		echo '</tr>';
		$n++;
	}
	echo '</table>';
	printpre($n);
	printpre($correct);
	printpre($correct/$n);
	//print_r(fgetcsv($file));
	fclose($file);
	
?>