<?

	include '../../nlp/alchemyapi_php/alchemyapi.php';

	if(isset($_POST['input']))
		$input = $_POST['input'];
	else
		$input = "You're fucked up";

	$alchemyapi = new AlchemyAPI();

	$response = $alchemyapi->sentiment('text', $input, array('sentiment'=>1));

	if ($response['status'] == 'OK') {
		echo 'Sentiment: ', $response['docSentiment']['type'], '<br>';
		if(array_key_exists('score', $response['docSentiment'])) {
			echo 'score: ', $response['docSentiment']['score'], '<br>';
		}
	} else {
		echo 'Error in the sentiment analysis call: ', $response['statusInfo'];
	}

?>