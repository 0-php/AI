<?php

require_once '../Loader.php';

use ANN\Network;
use ANN\Values;

try {
	$objNetwork = Network::loadFromFile('shapes.dat');
}
catch(Exception $e){
	print 'Creating a new one...';
	$objNetwork = new Network;
	$objValues = new Values;

	$shapes = (array) json_decode(file_get_contents('../../data/shapes/lines_and_squares.json'));
	
	for($i=0; $i<count($shapes['output']); $i++){
		$objValues->train()->input($shapes['input'][$i])->output($shapes['output'][$i]);
	}

	$objValues->saveToFile('values_shapes.dat');
	unset($objValues);
}

try {
	$objValues = Values::loadFromFile('values_shapes.dat');
}
catch(Exception $e){
	die('Loading of values failed');
}

$objNetwork->setValues($objValues); // to be called as of version 2.0.6

$boolTrained = $objNetwork->train();

print ($boolTrained) ? 'Network trained' : 'Network not trained completely. Please re-run the script';

$objNetwork->saveToFile('shapes.dat');

$objNetwork->printNetwork();
?>