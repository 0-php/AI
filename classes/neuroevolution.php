<?php
/*
	The Neuroevolution method used is based on Symbiotic, Adaptive Neuro-Evolution (SANE; Moriarty, 1997; Moriarty
and Miikkulainen, 1996). SANE has been shown to be a powerful reinforcement learning method for tasks with sparse reinforcement.

SANE differs from other NE systems in that it evolves a population of neurons instead of complete networks.
These neurons are combined to form hidden layers of feedforward networks that are then evaluated on a given problem.

Evolution in SANE proceeds as follows:

1. Initialization. The number of hidden units u in the networks that will be formed is specified and a population of neuron chromosomes
is created. Each chromosome encodes the input and output connection weights of a neuron with a random string of binary numbers.
2. Evaluation. A set of u neurons is selected randomly from the population to form a hidden layer of a feedforward network. The network is
submitted to a trial in which it is evaluated on the task and awarded a fitness score. The score is added to the cumulative fitness
of each neuron that participated in the network. This process is repeated until each neuron has participated in an average of e.g. 10 trials.
3. Recombination. The average fitness of each neuron is calculated by dividing its cumulative fitness by the number of trials in which
it participated. Neurons are then ranked by average fitness. Each neuron in the top quartile is recombined with a higher-ranking neuron
using 1-point crossover and mutation at low levels to create the offspring to replace the lowest-ranking half of the population.
4. The Evaluation–Recombination cycle is repeated until a network that performs sufficiently well in the task is found. In SANE, neurons compete
on the basis of how well, on average, the networks in which they participate perform. A high average fitness means that the neuron contributes
to forming successful networks and, consequently, suggests a good ability to cooperate with others.
The SANE approach has proven faster and more efficient than other reinforcement learning methods such as Adaptive Heuristic Critic,
Q-Learning, and standard neuroevolution, in, for example, the basic pole balancing task and in the robot arm control task
[Moriarty, 1997; Moriarty and Miikkulainen, 1996].
*/
include('../../../lib/php/fn/functions.php');
include('tests.class.php');
include('evolution_nn.class.php');

class NeuroEvolution extends Tests {
	var $test_data = NULL;
	var $chromosome_length = 5;

	function NeuroEvolution(){
	}
	
	//Symbiotic, Adaptive Neuro-Evolution
	function SANE(){
		$in_cnt = count($this->test_data['inputs']);
		$out_cnt = count($this->test_data['outputs']);
		$hidden = array();
		$hidden_evolve = 15; //Number of hidden neurons to evolve
		$hidden_count = 5; //Number of hidden neurons in network
		$evolutions_count = 5;
		$trials_count = 10 * $hidden_count; //Evolving trials
		for($i=0; $i<$hidden_count; $i++)
			$hidden[] = array('input'=>$this->Chromosome($in_cnt, $out_cnt), 'output'=>$this->Chromosome($in_cnt, $out_cnt), 'fitness'=>0, 'trials'=>0);
		//Start the evolution of networks
		for($i=0; $i<$evolutions_count; $i++){
			for($j=0; $j<$trials_count; $j++){
				$nn = new Evolution_NN($this->train_data, $this->test_data);
				$rand_keys = array_rand($hidden, $hidden_count);
				$rand_neurons = array();
				foreach($rand_keys as $value)
					$rand_neurons[] = $hidden[$value];
				$nn->LoadNeurons($rand_neurons);
				$results = $nn->Run();
				$fitness = $this->ComputeFitness($results);
				foreach($rand_keys as $value){
					$hidden[$value]['fitness'] += $fitness; //Adding fitness of current try to cumulative fitness of neuron
					$hidden[$value]['trials'] += 1; // +1 to trials count
				}
			}
			$hidden = $this->SelectBestNeurons($hidden);
			$offspring = $this->Crossover($hidden);
			$offspring = $this->Mutation($offspring);
		}
	}
	
	function ComputeFitness($results){
		for($i=0; $i<count($results); $i++){
			
		}
	}
	
	function Mutation($n){
		$chance = 15; //In percents
		$discount_value = ($total / 100) * $percent;
		for($i=0; $i<$n; $i++){
			if(rand(1, 100) < $chance)
				substr_replace($n[$i]['input'], rand(0, 1), rand(0, $this->chromosome_length), 1);
			if(rand(1, 100) < $chance)
				substr_replace($n[$i]['output'], rand(0, 1), rand(0, $this->chromosome_length), 1);
		}
	}
	
	//Each neuron recombined with a higher-ranking neuron in one-point crossover.
	function Crossover($n){
		$offspring = array();
		for($i=0; $i<count($n); $i++){
			if($i != 0){
				//All data beyond crossover point in either organism string is swapped between the two parent organisms.
				$crossover_point = rand(1, $this->chromosome_length); //If start from the first element (0) - this is just full exchange
				/*recombine(
					array($n[$i - 1]['input'], $n[$i]['input']),
					array($n[$i - 1]['output'], $n[$i]['output'])
				);*/
				
				$half_111 = substr($n[$i - 1]['input'], 0, $crossover_point);
				$half_112 = substr($n[$i - 1]['input'], $crossover_point);
				$half_121 = substr($n[$i - 1]['output'], 0, $crossover_point);
				$half_122 = substr($n[$i - 1]['output'], $crossover_point);
				$half_211 = substr($n[$i]['input'], 0, $crossover_point);
				$half_212 = substr($n[$i]['input'], $crossover_point);
				$half_221 = substr($n[$i]['output'], 0, $crossover_point);
				$half_222 = substr($n[$i]['output'], $crossover_point);
				//Two childs born =)
				$offspring[] = array(
					'input' => $half_111.$half_212,
					'output' => $half_121.$half_222
				);
				$offspring[] = array(
					'input' => $half_112.$half_211,
					'output' => $half_122.$half_221
				);
			}
		}
		return $n;
	}
	
	function recombine($str_1, $str_2, $point){
		$half_11 = substr($str_1, 0, $point);
		$half_12 = substr($str_1, $point);
		$half_21 = substr($str_2, 0, $point);
		$half_22 = substr($str_2, $point);
	}
	
	//Selects best 25% of neurons, but not less 4 for crossover and mutation
	function SelectBestNeurons($n){
		//Computing average fitness of neuron in all evolved networks
		for($i=0; $i<count($n); $i++)
			$n[$i]['avg_fitness'] = $n[$i]['fitness'] / $n[$i]['trials'];
		//Ranging neurons from best to worst
		$n = sortmulti($n, 'avg_fitness', 'desc');
		//Selecting
		$winners_count = count($n) / 4;
		if($winners_count < 4)
			$winners_count = 4;
		return array_slice($n, 0, $winners_count);
	}
	
	//Provides chromosome - random binary string for each input and output of a neuron, reflecting its synapse weights
	function Chromosome($inputs_count, $outputs_count){
		$chromosome = array('inputs'=>array(), 'outputs'=>array());
		for($i=0; $i<$inputs_count; $i++)
			$chromosome['inputs'] = rand_binary_string($this->chromosome_length);
		for($i=0; $i<$outputs_count; $i++)
			$chromosome['outputs'] = rand_binary_string($this->chromosome_length);
		return $chromosome;
	}
	
	function Run(){
		$this->SANE();
	}
}

?>