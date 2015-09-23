<?php
define('NN_CLASSES_PATH', '../classes/');

include(NN_CLASSES_PATH.'nn_batch.class.php');

set_time_limit(10);

$hamming = new NN_Batch(array(
	'type'		=>	'hamming',
	'path'		=>	'../data/3x5_digits.json',
	'times'		=>	1, //No need more than one?
	'accuracy'	=>	90
));
?>