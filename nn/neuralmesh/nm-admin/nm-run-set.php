<?php
set_time_limit(0); //Never timeout
require("lib/controller.class.php");
$app = new Controller;
$app->inc("nmesh");

if(!Model::loadProxy("train")->validate($_GET['s'])) throw new Exception("Training set not found!");
$training_data = $app->model->get("train")->get($_GET['s']);

$nid = $training_data[0]['networkID'];
$data = $app->model->get("network")->get($nid);
$nn = $app->model->get("network")->nn;
$reallearningrate = $data['learningrate'];
$start_time = microtime(true);

for($e=0;$e<$data['epochmax'];$e++) { //loop over max epochs
	
	$epochaverage = 0; //set the average to 0 for each epoch
	
	foreach($training_data as $set) { //loop over training set					
		$inputs = explode("|",trim($set['pattern'])); //create an input array from binary string
		$outputs = explode("|",trim($set['output'])); // '' for outputs
		
		$epochaverage += $nn->train($inputs,$outputs,$reallearningrate); //run train function
	}
	
	$epochaverage = $epochaverage / count($training_data);
	
	$reallearningrate = $data['learningrate'] * $epochaverage; //for better error gradient decent
	if($e==0) $start_mse = $epochaverage; //save the start mse
	
	//if target MSE has been reached stop
	if($epochaverage<$data['targetmse']) break;
}

$end_time = microtime(true) - $start_time;
$app->model->get("epochs")->saveEpoch($nid,$e+1,$start_mse,$epochaverage,$end_time,$_GET['s']);
$app->model->get("cache")->updateCache($nid,$training_data,$nn);

$app->model->get("network")->save($nn,$nid);
Model::direct("nm-set-history.php?s=".$_GET['s']."&n=$nid");
?>