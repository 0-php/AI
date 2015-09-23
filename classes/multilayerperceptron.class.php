<?php
include('neuralnetwork.class.php');

// 1 hidden layer (3 neurons), 1 output neuron
// Back propagation
class MultiLayerPerceptron extends NeuralNetwork {

	//User settings
	var $numEpochs = 500;
	var $numHidden = 3;
	var $lrate_IH = 0.7; //input - hidden
	var $lrate_HO = 0.07; //hiden - output

	//var $numPatterns = 4;
	
	var $hiddenVal = array();
	var $weightsIH = array();
	var $weightsHO = array();
	var $RMSerror; //Root mean squared error
	var $RMSerror_history;

	public function __construct($opts){
		parent::__construct($opts);
	}
	
	function initWeights(){
		for($i=0; $i<$this->numHidden; $i++){
			$this->weightsHO[$i] = (rand() / getrandmax() - 0.5) / 2;
			for($j=0; $j<$this->numInputs; $j++)
				$this->weightsIH[$j][$i] = (rand() / getrandmax() - 0.5) / 5;
		}
	}
	
	function activation(){
		//calculate the outputs of the hidden neurons, the hidden neurons are tanh
		for($i=0; $i<$this->numHidden; $i++){
			$hidVal = &$this->hiddenVal[$i];
			$hidVal = 0.0;
			for($j=0; $j<$this->numInputs; $j++)
				$hidVal = $hidVal + ($this->trainInput[$this->patNum][$j] * $this->weightsIH[$j][$i]);
			$hidVal = tanh($hidVal);
		}

		//calculate the output of the network, the output neuron is linear
		$this->outPred = 0.0;
		for($i=0; $i<$this->numHidden; $i++)
			$this->outPred = $this->outPred + ($this->hiddenVal[$i] * $this->weightsHO[$i]);

		//calculate the error
		$this->errThisPat = $this->outPred - $this->trainOutput[$this->patNum];
	}	
	
	function train(){
		for($i=0; $i<=$this->numEpochs; $i++){
			for($j=0; $j<$this->numPatterns; $j++){
				//srand();
				//$this->patNum = rand(0, $this->numPatterns-1); //??? Results is worse
				$this->patNum = $j;
				
				//calculate the current network output and error for this pattern
				$this->activation();

				//change network weights
				$this->WeightChangesHO();
				$this->WeightChangesIH();
			}

			//display the overall network error after each epoch
			$this->calcOverallError();

			if($i % 50 == 0){
				$this->RMSerror_history[] = array(
					'epoch' => $i,
					'rmserror' => $this->RMSerror
				);
				if($this->show_info == TRUE)
					print "epoch = ".$i."  RMS Error = ".round($this->RMSerror, 7)."</br>";
			}
		}
	}
	
	//adjust the weights hidden-output
	function WeightChangesHO(){
		for($i=0; $i<$this->numHidden; $i++){
			$weight = &$this->weightsHO[$i];
			$weightChange = $this->lrate_HO * $this->errThisPat * $this->hiddenVal[$i];
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
					* $this->lrate_IH
				) * $this->trainInput[$this->patNum][$j];
				$this->weightsIH[$j][$i] = $this->weightsIH[$j][$i] - $weightChange;
			}
		}
	}

	function displayResults(){
		for($i=0; $i<$this->numPatterns; $i++){
			$this->patNum = $i;
			$this->activation();
			$pattern_num = $this->patNum + 1;
			$this->answers[] = array(
				'pattern_num'	=> $pattern_num,
				'expected'		=> $this->trainOutput[$this->patNum],
				'predicted'		=> $this->outPred
			);
			if($this->show_info)
				print "pattern ".$pattern_num.", expected ".$this->trainOutput[$this->patNum]." predicted ".$this->outPred."</br>";
		}
	}

	function calcOverallError(){
		$this->RMSerror = 0.0;
		for($i=0; $i<$this->numPatterns; $i++){
			$this->patNum = $i;
			$this->activation();
			$this->RMSerror = $this->RMSerror + ($this->errThisPat * $this->errThisPat);
		}
		$this->RMSerror = sqrt($this->RMSerror / $this->numPatterns);
	}
}
?>