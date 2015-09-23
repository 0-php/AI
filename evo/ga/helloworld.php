<?php
require_once('../../lib/php/classes/ga.php');
require_once('../../lib/php/fn/functions.php');
include('chart.php');

class HelloWorld {
    var $string;

    function HelloWorld(){
        $this->string = rand_str(1);
    }	
}

class HelloWorldProgram {
	var $maxfitness;
	/*
	function add_data_for_chart($data){
		echo "<pre>"; print_r($data); echo "</pre>";
	}*/
	
	function show_stats($ga){ //echo "<pre>"; print_r($ga->history); echo "</pre>";
		$i = 1;
		echo "<table><tr><td>Gen №</td><td>Parameter</td><td>Fitness</td></tr>";
		foreach($ga->history as $gen){
			echo "<tr>";
			echo "<td>".$i."</td>";
			foreach($gen['population_stats'] as $individ){
				foreach($individ['props'] as $prop){
					echo "<td>".$prop['name'].": <b>".$prop['value']."</b></td>";
					if(count($individ['props'] > 1))
						echo "<br>";
				}
			}
			echo "</tr>";
			$i++;
		}
		echo "</table>";
		
	}
	
	function run(){
		$data = array();
		$adam = new HelloWorld();
		$eve = new HelloWorld();
		$ga = new GA();
		$ga->population = array($adam, $eve);
		add_data_for_chart($ga->population);
		$ga->fitness_function = array('HelloWorldProgram','fitness');    //Uses the 'total' function as fitness function
		$ga->num_couples = 1;                //4 couples per generation (when possible)
		$ga->death_rate = 2;                //No kills per generation
		$ga->generations = 50;                //Executes 100 generations
		$ga->crossover_functions = array('string'=>'avg_char_code'); //Array with functions, like $property=>$func
		$ga->mutation_function = 'char_gradual_change';        //Uses the 'inc' function as mutation function
		$ga->mutation_rate = 60;            //10% mutation rate
		$ga->evolve();                        //Run
		add_data_for_chart($ga->population);
		//add_data_for_chart(GA::select($ga->population, $ga->fitness_function, 1)); //The best
		$this->show_stats($ga);
	}

	//Fitness function. Less difference of char codes = more fitness
	function fitness($obj){
		$fitness = 500 - abs(char_code_diff_between_strings($obj->string, "d"));
		//echo "fit: $fitness<br>";
		return $fitness;
	}

}

//Average of char codes of two strings
function avg_char_code($letter1, $letter2){
	$a = ord($letter);
	$b = ord($letter); //echo "CODE: ".$b."<br>";
	$c = chr(round(($a + $b) / 2));
	//echo "CROSSOVER: ".$str3."<br>";
	return $c;
}

//Total difference between ascii character codes of strings
function char_code_diff_between_strings($letter1, $letter2){
	$diff = 0;
	//for($i=0; $i<strlen($str1)-1; $i++){
		// subtract the ascii difference between the target character and the chromosome character.
		// Thus 'c' is fitter than 'd' when compared to 'a'.
		$diff += ord($letter1) - ord($letter2);
	//}
	return $diff;
}

//Mutation function. Just increments or decrements the char code of string
function char_gradual_change($letter){ $len1 = strlen($string);
	//cho "BEFORE MUTATION: ".$string."<br>";
	//for($i=0; $i<strlen($string); $i++){
		$rand = mt_rand(1000000, 9999999);
		$rand = floatval('.'.$rand);
		$code = ord($letter);
		if($rand > 0.5){
			$code += 5;
			$letter = chr($code);
		} else {
			$code -= 5;
			$letter = chr($code);		
		}
	//}//echo "AFTER MUTATION: ".$string."<br>";
	return $letter;
}

$helloworld = new HelloWorldProgram();
$helloworld->run();
?>