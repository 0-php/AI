<?php

$inputs = 3;
$hidden = 3;
$outputs = 1;

$inputNeurons = array();
$hiddenNeurons = array();
$outputNeurons = array();

for($i=0; $i<$inputs; $i++)
	$inputNeurons[$i] = random_float(-1, 1);
for($i=0; $i<$hidden; $i++)
	$hiddenNeurons[$i] = array('threshold' => random_float(-1, 1));

$xor_test = (array) json_decode(file_get_contents('data/xor.json'));


function random_float($min, $max){
	return ($min + lcg_value() * (abs($max - $min)));
}
?>