<?php

$digit = 10;

$population_size = 100;
$generations = 100;

for($i=0; $i<$generations; $i++){
	$population = new_population($population_size);
	$fitness = fitness($population);
	$fitness = mutate($fitness);
	echo "Fitness: ".$fitness."<br>";
	if($fitness == 0){
		echo "Done at generation ".$i;
		break;
	}
}

//Creating population
function new_population($size){
	$population = array();
	for($i=0; $i<$size; $i++)
		$population[] = new Thing;
	return $population;
}

//Computing fitness function
function fitness($population){
	$best = 100;
	for($i=0; $i<$population; $i++){
		$fitness = abs($population[$i]->digit - $digit);
		if($fitness < $best)
			$best = $fitness;
	}
	return $fitness;
}

function mutate($digit){
	return rand(0, 1) == 1 ? $digit++ : $digit--;
}

class Thing {
	var $digit;
	
	function Thing(){
		$this->digit = rand(1, 100);
	}
}

?>