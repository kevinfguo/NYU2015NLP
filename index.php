<?php
	require_once 'helper.php';
	require_once 'lex.php';
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
	$uquery = urlencode($_POST['query']);

	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://api.twitter.com/1.1/search/tweets.json?q='.$uquery.'&lang=en&count=100',
		CURLOPT_HTTPHEADER => array("Authorization: Bearer ".$server_output['access_token'])
	));
	
	$server_output = curl_exec($ch);
	$server_output = json_decode($server_output, true);
	foreach($server_output['statuses'] as $tweet){
		printpre($tweet['text']);
	}
	//printpre($server_output);
	curl_close($ch);
} else {

?>

<!DOCROOT>
<html>
	<div>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  			HashTag: <input type="text" name="query"><br>
  			<input type="submit">
		</form>
	</div>
</html>

<?php

}

$lexer = new Lex();
$lexer->makeLex("AFINN-111.txt");
$lexer->showLex();

?>