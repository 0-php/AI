<?php
require_once('../../lib/php/classes/ga.php');
require_once('../../lib/php/fn/functions.php');
include('chart.php');

class Digit {
    var $digit;

    function Digit(){
        $this->digit = rand(1, 100);
    }	
}

class DigitProgram {
	var $maxfitness;
	/*
	function add_data_for_chart($data){
		echo "<pre>"; print_r($data); echo "</pre>";
	}*/
	/*
	function show_stats($ga){ //echo "<pre>"; print_r($ga->history); echo "</pre>";
		$i = 1;
		echo "<table><tr><td>Gen №</td><td>Parameter</td><td>Fitness</td></tr>";
		foreach($ga->history as $gen){
			echo "<tr>";
			echo "<td>".$i."</td>";
			foreach($gen['population_stats'] as $individ){ echo "<pre>"; print_r($individ); echo "</pre>";
				echo "<td>";
				foreach($individ['props'] as $prop){
					echo $prop['name'].": <b>".$prop['value']."</b>";
					//if(count($individ['props'] > 1))
					//	echo "<br>";
				}
				echo "</td>";
			}
			echo "</tr>";
			$i++;
		}
		echo "</table>";
		
	}*/
	
	function run(){
		$data = array();
		$adam = new Digit();
		$eve = new Digit();
		$ga = new GA();
		$ga->population = array($adam, $eve);
		//add_data_for_chart($ga->population);
		$ga->fitness_function = array('DigitProgram','fitness');	//Uses the 'total' function as fitness function
		$ga->num_couples = 1;										//4 couples per generation (when possible)
		$ga->death_rate = 2;										//2 kills per generation
		$ga->generations = 50;										//Executes 100 generations
		$ga->crossover_functions = array('digit'=>'crossover');		//Array with functions, like $property=>$func
		$ga->mutation_function = 'mutation';						//Uses the 'inc' function as mutation function
		$ga->mutation_rate = 60;									//10% mutation rate
		$ga->evolve();												//Run
		
		
		echo "<pre>";
		//print_r($ga);
		echo "</pre>";
		//add_data_for_chart($ga->population);
		//add_data_for_chart(GA::select($ga->population, $ga->fitness_function, 1)); //The best
		//$this->show_stats($ga);
	}

	//Fitness function. Less difference of char codes = more fitness
	function fitness($obj){
		$digit = 50;
		$fitness = 100 - abs($digit - $obj->digit);
		//echo "fit: $fitness<br>";
		return $fitness;
	}

}

//Difference of digits
function crossover($digit1, $digit2){
	return abs($digit1 - $digit2);
}

//Increments or decrements digit
function mutation($digit){
	return (rand(0, 1) == 1) ? $digit++ : $digit--;
}

$helloworld = new DigitProgram();
$helloworld->run();
?>