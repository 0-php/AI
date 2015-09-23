<?php
include('classes/adaptive_system.php');

$genome = array(
	'neurons_count' => 10,
	'links_per_neuron' => 3,
	'percent_of_inputs' => 30,
	'percent_of_outputs' => 30
);

$sys = new Adaptive_System($genome);
echo "<h3>Start neurons</h3><pre>"; print_r($sys->neurons); echo "</pre>";

$learn_data = array(
	'inputs' => array(
		array(2,2),
		array(2,3),
		array(1,3),
		array(5,3),
		array(4,5),
		array(8,3),
		array(4,7),
		array(9,5),
		array(7,5)
	),
	'outputs' => array(4, 5, 4, 8, 9, 11, 11, 14, 12)
);

$test_data = array(
	'inputs' => array(
		array(2,2),
		array(4,3),
		array(2,3),
		array(6,7),
		array(5,3),
		array(2,7),
		array(1,3),
		array(2,4)
	),
	'outputs' => array(4, 7, 5, 13, 8, 9, 4, 6)
);
	
$sys->Learn($learn_data);
$sys->Test($test_data);

echo "<h3>Resulting neurons:</h3><pre>"; print_r($sys->neurons); echo "</pre>";
?>