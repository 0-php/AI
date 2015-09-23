<?php

class Kohonen {
	var $neurons[] = array;
	var $iterNum;
	var $epsilon;

	//Arguments: $m - dimension, $iterNum - number of iterations, $epsilon - epsilon, $neigbourhood - function of topological neigbourhood
	function Kohonen($m, $iterNum, $epsilon, $neigbourhood){
		//Creating map
		$this->iterNum = $iterNum;
		$this->epsilon = $epsilon;
		for($i=0; $i<$m; $i++){
			$neurons[$i] = array();
			for($j=0; $j<$m; $j++)
				$neurons[$i][$j] = 0;
		}
		
	}
	
	function StartLearning(){
		$iter = 0;
		while($iter <= $this->iterNum && $currEpsilon > $this->epsilon){
			$patternsToLearn = array();
			foreach($p in $patterns)
				$patternsToLearn[] = $p;
			$randPattern = rand();
			$pattern = array(); //Size is inputLayerDimension
			for($i=0; $i<$numPatterns; $i++){
				$pattern = $patternsToLearn[
			}
		}
	}
}

?>