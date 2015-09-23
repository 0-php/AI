<?php

require('./fann.class.php');
$fann = new fann();

$trainFile = dirname(__FILE__) . '/train_data.txt';
$filename = dirname(__FILE__) . '/test.txt';


// train
$fann->create_standard(4, 2, 8, 9, 1);

/*
$fann->train(array(1, 1), array(1));
$fann->train(array(1, 0), array(1));
$fann->train(array(0, 1), array(1));
$fann->train(array(0, 0), array(0));
*/

$fann->set_activation_function_hidden(FANN_SIGMOID_SYMMETRIC);
$fann->set_activation_function_output(FANN_SIGMOID_SYMMETRIC);

$fann->train_on_file($trainFile, 1000, 0.01);

$res = $fann->run(array(1, -1));
var_dump($res);

$fann->save($filename);

exit;
// run
$fann->create_from_file($filename);


$res = $fann->run(array(1, 1));
$res = $fann->run(array(1, 0));
$res = $fann->run(array(0, 1));
$res = $fann->run(array(0, 0));

?>