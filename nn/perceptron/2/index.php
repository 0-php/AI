<?php
include("../../classes/perceptron.class.php");
include('../../classes/tests.class.php');

//Перцептрон будет говорить какую фигуру дали на вход. Этих фигур не было в обучении
$nn = new Perceptron(64);	// матрица будет 8х8, размерность 64.
$tests = new Tests('geometry_2');
$train = $tests->train_data;
$test = $tests->test_data; //print_r($test);

for($i=0; $i<count($train['input']); $i++)
	$nn->learn($train['input'][$i], $train['output'][$i]);

for($i=0; $i<=count($test['input'])-1; $i++){
	$answer = $nn->activation($test['input'][$i]);
	echo $i.": <b>".$answer."</b> (".$test['output'][$i].")<br>";
}
?>