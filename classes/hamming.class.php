<?php
// Hamming neural network with MAXNET network
// Count of neurons = numPatterns
// Count of synapses = numInputs * numPatterns
include('neuralnetwork.class.php');

class Hamming extends NeuralNetwork {

	var $iterations = 0;
	var $input = array();
	var $output = array();
	var $old_output = array();
	var $output_history = array();
	var $answer = NULL;
	var $w = array();
	var $lrate = 0.1; //Learning rate

	function __construct($opts){
		parent::__construct($opts);
	}

	function start(){
		$this->initWeights();
		$this->loadRandomPattern();
		echo "Random pattern index: <b>".$this->randomPatternIndex."</b><br>";
		$c = 0;
		while($this->answer === NULL AND $c < 10){
			$this->maxnet();
			$this->backPropagate();
			$this->output_history[] = $this->output;
			$c++;
		}
	}

	function backPropagate(){
		for($i=0; $i<$this->numPatterns; $i++){
			$real_answer = ($this->randomPatternIndex == $i) ? 1 : 0;
			$this->errThisPat = $real_answer - $this->output[$i];
			$this->weightChanges();
		}
	}

	function weightChanges(){
		for($i=0; $i<$this->numOutputs; $i++){
			for($j=0; $j<$this->numInputs; $j++){
				$weight = &$this->w[$i][$j];
				$weightChange = $lrate * $this->errThisPat * $
				//$w[$i][$j] =
			}
		}
	}

	function WeightChangesHO(){
		for($i=0; $i<$this->numHidden; $i++){
			$weight = &$this->weightsHO[$i];
			$weightChange = $this->LR_HO * $this->errThisPat * $this->hiddenVal[$i];
			$weight = $weight - $weightChange;

			//regularisation on the output weights
			$this->fitInRange(-5, 5, $weight);
		}
	}

	//adjust the weights input-hidden
	function WeightChangesIH(){
		for($i=0; $i<$this->numHidden; $i++){
			for($j=0; $j<$this->numInputs; $j++){
				$weightChange = (
					(1 - ($this->hiddenVal[$i] * $this->hiddenVal[$i]))
					* $this->weightsHO[$i]
					* $this->errThisPat
					* $this->LR_IH
				) * $this->trainInput[$this->patNum][$j];
				$this->weightsIH[$j][$i] = $this->weightsIH[$j][$i] - $weightChange;
			}
		}
	}

	function loadRandomPattern(){
		$this->randomPatternIndex = rand(0, $this->numPatterns - 1);
		$this->input = $this->trainInput[$this->randomPatternIndex];
	}

	function initWeights(){
		for($i=0; $i<$this->numPatterns; $i++)
			for($j=0; $j<$this->numInputs; $j++)
				$this->w[$i][$j] = (rand() / getrandmax()) / 4;
		//echo "<pre>"; print_r($this->w); echo "</pre>";
	}

	function initWeights_(){
		$w = &$this->w;
		$nr0 = 0.28867513459481;
		$nr1 = 0.44721359549996;
		$nr2 = 0.30151134457776;
		$nr3 = 0.31622776601684;
		$nr4 = 0.33333333333333;
		$nr5 = 0.30151134457776;
		$nr6 = 0.28867513459481;
		$nr7 = 0.37796447300923;
		$w[0] = array($nr0, $nr0, $nr0, $nr0, 0, $nr0, $nr0, 0, $nr0, $nr0, 0, $nr0, $nr0, $nr0, $nr0);
		$w[1] = array(0, $nr1, 0, 0, $nr1, 0, 0, $nr1, 0, 0, $nr1, 0, 0, $nr1, 0);
		$w[2] = array($nr2, $nr2, $nr2, 0, 0, $nr2, $nr2, $nr2, $nr2, $nr2, 0, 0, $nr2, $nr2, $nr2);
		$w[3] = array($nr3, $nr3, $nr3, 0, 0, $nr3, 0, $nr3, $nr3, 0, 0, $nr3, $nr3, $nr3, $nr3);
		$w[4] = array($nr4, 0, $nr4, $nr4, 0, $nr4, $nr4, $nr4, $nr4, 0, 0, $nr4, 0, 0, $nr4);
		$w[5] = array($nr5, $nr5, $nr5, $nr5, 0, 0, $nr5, $nr5, $nr5, 0, 0, $nr5, $nr5, $nr5, $nr5);
		$w[6] = array($nr6, $nr6, $nr6, $nr6, 0, 0, $nr6, $nr6, $nr6, $nr6, 0, $nr6, $nr6, $nr6, $nr6);
		$w[7] = array($nr7, $nr7, $nr7, 0, 0, $nr7, 0, 0, $nr7, 0, 0, $nr7, 0, 0, $nr7);
	}

	function maxnet(){
		$input = &$this->input;
		$output = &$this->output;
		$old_output = &$this->old_output;
		if($this->iterations == 0){
			$input = $this->standards($input);
			for($i=0; $i<$this->numOutputs; $i++){
				$output[$i] = 0;
				for($j=0; $j<$this->numInputs; $j++)
					$output[$i] = $output[$i] + ($input[$j] * $this->w[$i][$j]);
			}
			$output = $this->standards($output);
			for($i=0; $i<$this->numOutputs; $i++)
				if($output[$i] < 0)
					$output[$i] = 0;
		} else {
			//output = old output - mean output
			$output_sum = array_sum($old_output);
			for($i=0; $i<$this->numOutputs; $i++)
				$output[$i] = $old_output[$i] + ($output_sum / (-$this->numOutputs));
				
			for($i=0; $i<$this->numOutputs; $i++){
				if($output[$i] < 0.05)
					$output[$i] = 0;
				if($output[$i] > 0.96)
					$output[$i] = 1;
			}
			$output = $this->standards($output);

		}
		$old_output = $output;
		$this->checkResult();
		$this->iterations++;
	}

	function checkResult(){
		$output = &$this->output;
		$nok = $sum = 0; //Number of concurrents and winners
		for($i=0; $i<$this->numOutputs; $i++){
			if(!(($output[$i] == 0) || ($output[$i] == 1)))
				$nok++;
			if($output[$i] == 1)
				$sum++;
		}
		if(($nok == 0) && ($sum == 1)){
			for($i=0; $i<$this->numOutputs; $i++){
				if($output[$i] == 1)
					$this->answer = $i;
			}
		}
	}

	// Maximizing maxs and minimizes mins in range 0 - 1
	function standards($arr){
		$squares_sum = $this->squares_sum($arr);
		for($i=0; $i<count($arr); $i++)
			$arr[$i] = sqrt(($arr[$i] * $arr[$i]) / $squares_sum);
		return $arr;
	}

}

?>