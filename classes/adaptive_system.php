<?php
//System of adaptive neuron-like elements

class Adaptive_System {
	var $genome;
	var $neurons = array();
	var $links = array();
	
	function Adaptive_System($genome){
		$this->genome = $genome;
		for($i=0; $i<$genome['neurons_count']; $i++){
			$this->neurons[] = $this->Element();
			//Making links to random elements with random weights
			for($j=0; $j<$genome['links_per_neuron']; $j++)
				$this->links[] = $this->Link($i);
		}
	}
	
	function Live(){
		//For each neuron
		for($j=0; $j<count($this->neurons); $j++){
			//Find weighted sum:
			$weights = 0;
			$inputs = 0;
			//For each link
			for($k=0; $k<count($this->links); $k++){
				//If it contains our element (it is input), action potential * weight of link
				if($this->links[$k][1] == $j){
					$weights += $this->neurons[$this->links[$k][0]]['potential'] * $this->links[$k][2];
					$inputs++;
				}
			}
			echo "Inputs: ".$inputs."<br>";
			if($inputs > 0)
				$weighted_sum = $weights / $inputs;
			else
				$weighted_sum = 0;
			//Debug
			//echo $weighted_sum."<br>";
			//Activation function
			echo "Weighted sum: ".$weighted_sum."<br>";
			if($weighted_sum > $this->neurons[$j]['threshold'])
				$this->neurons[$j]['potential'] = 1;
		}
	}
	
	function Learn($data){
		//$this->Load_Data($data);
		$this->Live();
	}
	
	function Test($data){
	
	}
	
	function Element($action_potential = NULL, $threshold = NULL, $is_input = NULL, $is_output = NULL){
		if($action_potential == NULL)
			$action_potential = random_float(0, 1);
		if($threshold == NULL)
			$threshold = rand(0, 1);
		if($is_input == NULL)
			$is_input = (int) probability($this->genome['percent_of_inputs']);
		if($is_output == NULL)
			$is_output = (int) probability($this->genome['percent_of_outputs']);
		return array(
			'potential' => $action_potential,
			'threshold' => $threshold,
			'is_input' => $is_input,
			'is_output' => $is_output
		);
	}
	
	function Link($from = NULL, $to = NULL, $weight = NULL){
		if($from == NULL)
			$from = rand(0, $this->genome['neurons_count']);
		if($to == NULL)
			$from = rand(0, $this->genome['neurons_count']);
		if($weight == NULL)
			$weight = rand(0, 1);
		return array($from, $to, $weight);
	}
}

function random_float($min, $max){
	return ($min + lcg_value() * (abs($max - $min)));
}

function probability($chance, $out_of = 100){
    $random = mt_rand(1, $out_of);
    return $random <= $chance;
}

?>