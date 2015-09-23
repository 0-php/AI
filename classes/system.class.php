<?php
//System of neuron-like elements

class System {
	var $chromosome;
	var $elements = array();
	var $sensors = array(); //Id's of elements and corresponding symbols
	var $effectors = array(); //Id's of elements and corresponding actions
	var $links = array();
	var $ticks = 10; //Time for action
	var $history = array();
	
	function System($chromosome){
		$this->chromosome = $chromosome;
		//Making elements with random action potential and threshold
		$el_count = $chromosome['elements_count'];
		for($i=0; $i<$el_count; $i++){
			$this->elements[] = $this->Element();
			//Making links to random elements with random weights
			for($j=0; $j<$chromosome['links_per_element']; $j++)
				$this->links[] = $this->Link($i);
		}
	}
	
	function Learn($data){
		$this->Load_Input($data['inputs']);
	}
	
	function &Create_Sensor($symbol){
		$sensor_id = count($this->sensors);
		$new_element = $this->Element();
		$this->elements[] = &$new_element;
		$sensor = array(
			'symbols' => $symbol,
			'element' => &$new_element;
		);
		$this->sensors[] = &$sensor;
		return $sensor;
	}
	
	function Load_Input($data){
		$sensors = $this->Find_Sensors();
		for($i=0; $i<count($data); $i++){
			for($j=0; $j<count($data[$i]); $j++){
				$id = multidimentional_search($sensors, array('symbols' => $data[$i][$j]));
				if($id != FALSE) //Sensor with that symbol already exists
					$sensors['elements'][$id]['action_potential'] = 1;
				else { //New symbol, creating new sensor
					$sensor = &$this->Create_Sensor($data[$i][$j]);
					$sensor['element']['action_potential'] = 1;
				}
			}
		}
		if(count($sensors) < count($data)){
			if(count($this->elements) < count($data))
				$this->Add_Elements((count($data) - count($this->elements)) * 3);
			$this->Make_Sensors(count($data) - count($sensors));
			$sensors = $this->Find_Sensors();
		} elseif(count($inputs) > $count($data)){
			$diff = count($inputs) - count($data);
			for($i=0; $i<$diff; $i++)
				unset($inputs[rand(0, count($inputs))]);
		}
		for($i=0; $i<count($data); $i++){
			$key = rand($inputs[rand(0, count($inputs))]);
			$this->elements[$key]['action_potential'] = 1;
		}
	}
	
	function Load_Output($data){
		$outputs = $this->Find_Outputs();
		if(count($outputs) < count($data)){
			if(count($this->elements) < count($data))
				$this->Add_Elements((count($data) - count($this->elements)) * 3);
			$this->Make_Outputs(count($data) - count($outputs));
			$outputs = $this->Find_Outputs();
		} elseif(count($outputs) > $count($data)){
			$diff = count($outputs) - count($data);
			for($i=0; $i<$diff; $i++)
				unset($outputs[rand(0, count($outputs))]);
		}
		for($i=0; $i<count($data); $i++){
			$key = rand($outputs[rand(0, count($outputs))]);
			$this->elements[$key]['action_potential'] = 1;
		}
	}
	
	function Add_Elements($num){
		for($i=0; $i<$num; $i++)
			$this->elements[] = $this->Element();
	}
	
	function Find_Sensors(){
		$sensors = array();
		for($i=0; $i<count($this->elements); $i++){
			if($this->elements[$i]['is_sensor'] == 1){
				$id = count($sensors['element_id']);
				$sensors['element_id'][$id] = $i;
				$sensors['symbol'][$id] = $this->elements[$i]
			}
		}
		return $sensors;
	}
	
	function Make_Sensors($num){
		$c = 0;
		while($num > $c){
			$key = array_rand($this->elements);
			if($this->elements[$key]['is_sensor'] != 1){
				$this->elements[$key]['is_sensor'] = 1;
				$c++;
			}
		}
	}
	
	function Find_Outputs(){
		$outputs = array();
		for($i=0; $i<count($this->elements); $i++)
			if($this->elements[$i]['is_output'] == 1)
				$outputs[] = $i;
		return $outputs;
	}
	
	function Make_Outputs($num){
		$c = 0;
		while($num > $c){
			$key = array_rand($this->elements);
			if($this->elements[$key]['is_output'] != 1){
				$this->elements[$key]['is_output'] = 1;
				$c++;
			}
		}
	}
	
	function Run(){
		for($i=0; $i<$this->ticks; $i++){
			$this->history['elements'] = $this->elements;
			//For each element
			for($j=0; $j<count($this->elements); $j++){
				//Find weighted sum:
				$weights = 0;
				$inputs = 0;
				//For each link
				for($k=0; $k<count($this->links); $k++){
					//If it contains our element (it is input), action potential * weight of link
					if($this->links[$k][1] == $j){
						$weights += $this->elements[$this->links[$k][0]]['potential'] * $this->links[$k][2];
						$inputs++;
					}
				}
				if($inputs > 0)
					$weighted_sum = $weights / $inputs;
				else
					$weighted_sum = 0;
				//Debug
				//echo $weighted_sum."<br>";
				//Activation function
				if($weighted_sum > $this->elements[$j]['threshold'])
					$this->elements[$j]['potential'] = 1;
				$this->Visualize();
			}
		}
	}
	
	function Visualize(){
		echo "<script src='../../../lib/js/jquery/jquery.js'></script>
		<script>
			history = ".json_encode($this->history).";
		<table border=1 cellpadding=5>";
		$sqrt = sqrt(count($this->elements));
		for($i=0; $i<$sqrt; $i++){
			echo "<tr>";
			for($j=0; $j<count($this->elements); $j++)
				echo "<td><h3>".$this->elements[$i]['potential']."</h3>".$this->elements[$i]['threshold']."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function Element($action_potential = NULL, $threshold = NULL, $is_input = NULL, $is_output = NULL){
		if($action_potential == NULL)
			$action_potential = random_float(0, 0.5);
		if($threshold == NULL)
			$threshold = rand(0, 1);
		if($is_input == NULL)
			$is_input = (int) probability($this->chromosome['percent_of_inputs']);
		if($is_output == NULL)
			$is_output = (int) probability($this->chromosome['percent_of_outputs']);
		return array(
			'potential' => $action_potential,
			'threshold' => $threshold,
			'is_input' => $is_input,
			'is_output' => $is_output
		);
	}
	
	function Link($from = NULL, $to = NULL, $weight = NULL){
		if($from == NULL)
			$from = rand(0, $this->chromosome['elements_count']);
		if($to == NULL)
			$from = rand(0, $this->chromosome['elements_count']);
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

function multidimensional_search($parents, $searched) { 
  if (empty($searched) || empty($parents)) { 
    return false; 
  } 
  
  foreach ($parents as $key => $value) { 
    $exists = true; 
    foreach ($searched as $skey => $svalue) { 
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
    } 
    if($exists){ return $key; } 
  } 
  
  return false; 
} 

?>