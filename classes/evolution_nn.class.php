<?php
include('perceptron.class.php');

class Evolution_NN extends Perceptron {

	function Evolution_NN($train, $test){
		$this->train = $train;
		$this->test = $test;
	}

	function LoadNeurons(){
	
	}

	function Run(){
		$answers = array();
		$train = &$this->train;
		$test = &$this->test;
		for($i=0; $i<count($train['input']); $i++)
			$this->learn($train['input'][$i], $train['output'][$i]);

		for($i=0; $i<=count($test['input'])-1; $i++){
			$answer = $this->activation($test['input'][$i]);
			//echo $i.": <b>".$answer."</b> (".$test['output'][$i].")<br>";
			if($answer == 1 && $test['output'][$i] == 1)
				$answers[] = 1;
			elseif($answer == 0 && $test['output'][$i] == -1)
				$answers[] = 1;
			else
				$answers[] = 0;
		}
		return $results;
	}

}

?>