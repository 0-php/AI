<?php
include('layer.class.php');
include('utilities.class.php');

class NeuralNetwork {

	var $size;
	var $answers = array();

	function __construct($opts){
		if(is_string($opts))
			$this->opts = $opts = (array) json_decode($opts);
		if(isset($opts['path']))
			$this->loadTrainDataFromFile($opts['path']);
		if(isset($opts['show_info']))
			$this->show_info = $opts['show_info'];
		else
			$this->show_info = TRUE;
		$this->start();
	}
	
	function start(){
		$this->initWeights();
		$this->train();
	}
	
	function loadTrainData($data){
		if(is_string($data))
			$data = (array) json_decode($data);
		$this->trainInput = $data['input'];
		$this->trainOutput = $data['output'];
		
		$this->numInputs = count($this->trainInput[0]);
		$this->numPatterns = count($this->trainInput);
		$this->numOutputs = count($this->trainOutput);
	}

	function loadTrainDataFromFile($path){
		$this->loadTrainData(file_get_contents($path));
	}
	
	// Initialize the random weights
	public function init_weights(){
		for($i=0; $i<$this->size; $i++)
			$this->w[] = rand(0, 10);
	}
	
	function squares_sum($arr){
		$squares_sum = 0.0;		
		foreach($arr as $elem)
			$squares_sum = $squares_sum + ($elem * $elem);
		return $squares_sum;
	}
	
	// Fits number between two borders
	function fitInRange($border1, $border2, $num){
		if($border1 < $border2){
			if($num < $border1)
				$num = $border1;
		} else {
			if($num < $border2)
				$num = $border2;
		}			
	}
}

?>