<?php
define('NN_CLASSES_PATH', '../classes/');

include(NN_CLASSES_PATH.'nn_batch.class.php');

// the data here is the XOR data
// it has been rescaled to the range
// [-1][1]
// an extra input valued 1 is also added
// to act as the bias
// the output must lie in the range -1 to 1

//Last digit in any input is bias
$data = '{
	"input": [
		[1,-1,1],
		[-1,1,1],
		[1,1,1],
		[-1,-1,1]
	],
	"output": [1,1,-1,-1]
}';

set_time_limit(0);

$MLP = new NN_Batch(array(
	'type'		=>	'mlp',
	'path'		=>	'../data/xor.json',
	'times'		=>	10,
	'accuracy'	=>	90
));
?>