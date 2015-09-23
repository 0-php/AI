<?php

class StrangeSystem {
	//Settings
	var $ticks = 100; //Time of the system
	var $elements_count = 10; //Count of elements in the system
	var $minimum_energy = 0;
	var $maximum_energy = 99;
	var $maximum_energy_to_send = 2;
	
	var $elements = array();

	function StrangeSystem(){
		for($i=0; $i<$this->ticks; $i++){
			$elems = $this->get_random_elements();
			$elems = $this->change_state($elems);
		}
	}
	
	function get_random_elements(){
		$elems = array();
		for($i=0; $i<count($this->elements); $i++)
			if(rand(0, 1) == 1)
				$elems[] = $this->elements[$i];
		return $elems;
	}
	
	//Changing state of the element
	function change_state($elem, $state = NULL){
		$elems = &$this->elements;
		if($state == NULL)
			$state = rand(1, 3);
		switch($state){
			case 1: //Element will be linked to another (Quantum entanglement)
				$elem = &$elems[rand(0, count($elems)];
				break;
			case 2:
				break;
			case 3:
				break;
		}
	}
	
	function random_change_energy(){
		$elems = array();
		for($i=0; $i<count($this->elements); $i++)
			$elems[] = rand($this->minimum_energy, $this->maximum_energy);
		echo "<pre>";
		for($i=0; $i<100; $i++){
			$idx = rand(0, count($this->elements)-1);
			$elems = $this->transport_energy($elems, $idx);
		}
	}
	
	function transport_energy($elems, $from_idx){
		$from = &$elems[$from_idx];
		$elems_to = array_rand_elements($elems);
		foreach($elems_to as $key=>$val){
			if($from != $this->minimum_energy){ //Not sending if minimal level of energy
				if($from_idx != $key){ //Not sending to itself
					$to = &$elems[$key];
					$energy = rand($this->minimum_energy, $this->maximum_energy_to_send);
					//Checking if sending energy more than element has
					if($from - $energy < $this->minimum_energy)
						$energy = $from;
					$from -= $energy;
					$to += $energy;
					if($to > $this->maximum_energy)
						$to = $this->maximum_energy;
					echo "<br>idx ".$from_idx." send ".$energy." to idx ".$key."<br><br>";
					echo "Balance: ";
					print_r($elems);
				}
			}
		}
		return $elems;
	}
}

?>