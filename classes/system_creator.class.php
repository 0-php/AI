<?php
//Class realizing management of process creating functional systems
include('system.class.php');
include('../../lib/php/classes/threads.class.php');

class System_Creator {
	var $energy = 1000; //Energy of computer, that will be spend on creating systems
	var $simultaneous_systems_count = 2;
	var $queue = array();
	
	function System_Creator($num, $test = NULL){
		//Creation and rating of chromosomes of future systems
		$ratings = array();
		$chromosomes = array();
		for($i=0; $i<$num; $i++){
			$chromosome = $this->Random_Chromosome();
			$rating = $this->Rate_Chromosome($chromosome);
			$ratings[] = $rating;
			$chromosomes[] = $chromosome;
		}
		rsort($ratings);
		foreach($ratings as $key=>$value)
			$this->queue[$key] = array(
				'rating' => $value,
				'chromosome' => $chromosomes[$key]
			);
		//Spreading energy between most perspective systems in first launch
		$count = $this->simultaneous_systems_count;
		$energy_per_system = $this->energy / $count;
		//Creating systems
		$threads = new Threads;
		
		for($i=0; $i<$count; $i++){
			$threads->newThread('D:/web/www/lh/tests/fuzzy/system/system.php', $this->queue[0]);
			//Delete first element and reindex queue
			unset($this->queue[0]);
			$this->queue = array_values($this->queue);
		}
		while(false !== ($result = $threads->iteration()))
			echo $result."\r\n";
		//$this->Run_Test($test);
	}
	
	function Rate_Chromosome(){
		return rand(1, 5);
	}
	
	function Random_Chromosome(){
		return array(
			'elements_count' => rand(1, 5),
			'links_per_element' => rand(5, 10),
			'elements' => array(
				'is_input' => rand(0, 1),
				'is_output' => rand(0, 1),
				//'activation_function' => $this->funcs[$this->Random_Function()],
				'activation_function' => 'Activation_Function',
				'activation_probability' => rand(1, 100),
				
			),
			'links' => array(
			
			)
		);
	}
	
	function Random_Function(){
		return array_rand($this->funcs);
	}
}
?>