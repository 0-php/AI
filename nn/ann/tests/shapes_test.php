<?php

require_once '../Loader.php';

use ANN\Network;
use ANN\Values;

try {
	$objNetwork = Network::loadFromFile('shapes.dat');
} catch(Exception $e){
	die('Network not found');
}

try {
	$objValues = Values::loadFromFile('values_shapes.dat');
} catch(Exception $e){
	die('Loading of shapes failed');
}

$objValues->reset();

$shapes = (array) json_decode(file_get_contents('../../data/shapes/lines_and_squares_test.json'));

foreach($shapes as $shape)
	$objValues->input($shape);

$objNetwork->setValues($objValues);

echo "<pre>"; print_r($objNetwork->getOutputs()); echo "</pre>";

?>