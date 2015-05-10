<?php
	require_once 'helper.php';
	require_once 'lex.php';
	require_once 'wordList.php';
	require_once 'AFINNlex.php';
	require_once 'LabMTlex.php';
	require_once 'ANEWlex.php';
	ini_set('display_errors', true); ini_set('display_startup_errors', true); error_reporting(E_ALL);

	$consumer_key = '8ndEpZxQUwYGoTGnj27Xj3sIB';
	$consumer_secret = '83qdyRR16qsujckGFJzfwBYXWrF5tKOeqOl6MeOqpINuqXURFM';
	$bearer_token = $consumer_key.":".$consumer_secret;
	$bearer_token_encode = base64_encode($bearer_token);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Basic ".$bearer_token_encode,
		"Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
		));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close($ch);
	$server_output = json_decode($server_output, true);

	//printpre($server_output);

if (isset($_POST['query'])){
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

	$uquery = urlencode($_POST['query']);

	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://api.twitter.com/1.1/search/tweets.json?q='.$uquery.'+-filter:retweets+-filter:replies+-filter:links&lang=en&count=100',
		CURLOPT_HTTPHEADER => array("Authorization: Bearer ".$server_output['access_token'])
	));
	
	$server_output = curl_exec($ch);
	$server_output = json_decode($server_output, true);
	echo '<table style="width:100%">';
	echo '<tr><th>Tweet body</th><th>AFINN Valence Score</th><th>LabMT Valence Score</th><th>ANEW Valence Score</th><th>Agreement</th><th>Final</th></tr>';
	foreach($server_output['statuses'] as $tweet){
		//printpre($tweet['text']);
		echo '<tr>';
		echo '<td>'.$tweet['text'].'</td>';
		$text = strtolower($tweet['text']);
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
		echo '<td>'.round($AFINN_tweet_valence, 3).'</td>';
		echo '<td>'.round($LabMT_tweet_valence, 3).'</td>';
		echo '<td>'.round($ANEW_tweet_valence, 3).'</td>';
		echo '<td>'.$eval.'</td>';
		echo '<td>'.consensus($eval).'</td>';
		echo '</tr>';
		//printpre($tweet_valence);
		//printpre($text);
		//printpre($vals);
	}
	echo '</table>';
	//printpre($server_output);
	curl_close($ch);
} else {

?>

<!DOCROOT>
<html>
	<div>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  			Query: <input type="text" name="query"><br>
			Example: "#happy", "#friday", "cake"
  			<input type="submit">
		</form>
	</div>
</html>

<?php

}

?>