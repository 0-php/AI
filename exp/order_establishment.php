<?php
//This program simulates system of several elements with random energy, which randomly giving energy to each other.
//No one element can give more energy, that it has. In the end, sum of energy, carried by elements will be equal maximum energy of one element,
//or initial sum of energies if they lower
include('../../lib/php/fn/functions.php');

class ElementsWithEnergy {
	var $count_of_elements = 4;
	var $minimum_energy = 0;
	var $maximum_energy = 99;
	var $maximum_energy_to_send = 2;

	function ElementsWithEnergy(){
		$elems = array();
		for($i=0; $i<$this->count_of_elements; $i++)
			$elems[] = rand($this->minimum_energy, $this->maximum_energy);
		echo "<pre>";
		for($i=0; $i<100; $i++){
			$idx = rand(0, count($elems)-1);
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

$sysorder = new ElementsWithEnergy();
?>