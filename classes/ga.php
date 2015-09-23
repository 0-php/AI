<?php
//------------------------------------------------------------------------------------------
// GA 1.0 - 09/09/2005
//
// Created by Rafael C.P. (a.k.a. Kurama_Youko)
// Contact: rcpinto@inf.ufrgs.br
// Personal homepage: http://www.inf.ufrgs.br/~rcpinto (Portuguese only, sorry!)
//
// Getting Started
// GA::crossover($parent1,$parent2,$cross_functions)
// -> crossover $parent1 and $parent2 with $cross_functions, returning a new instance
// of the same class as $parent1 and $parent2. $cross_function may be a single function name or an
// associative array where the keys are the property names and the values are function names.
// GA::mutate(&$object,$mutation_function)
// -> causes mutation on $object, using $mutation_function, which is a function name.
// GA::fitness($object,$fitness_function)
// -> calculates and returns the fitness value for an object, using a $fitness_function.
// GA::select($objects,$fitness_function,$n=2)
// -> Given an array of objects of the same class, selects the best $n objects using a given
// $fitness_function. Returns an array with the selected objects.
// GA::kill(&$objects,$fitness_function,$n=2)
// -> Given an array of objects of the same class, selects the worst $n objects using a given
// $fitness_function and removes them of the $objects array.
//------------------------------------------------------------------------------------------
class GA {

    var $population;				//Objects array (same classes)
    var $fitness_function;			//The fitness function name (string)
    var $crossover_functions;		//The crossover function name (string) or array
    var $mutation_function;			//The mutation function name (string)
    var $mutation_rate;				//Mutation rate per child (%)
    var $generations;				//Number of generations
    var $num_couples;				//Number of couples for each generation
    var $death_rate;				//Number of killed objects for each generation
	var $history = array();			//History of population for statistics

	//parent1(2) - Objects of same class
	//cross_functions - string with function name or array with keys - properties, and values - strings
    function crossover($parent1, $parent2, $cross_functions){ //echo "Crossover of ".strlen($parent1->string)." and ".strlen($parent2->string)."<br>";
        $class = get_class($parent1);
        if($class != get_class($parent2))
			return false;
        if(!is_array($cross_functions)){ //Only one function for crossover
            $cross_function = $cross_functions;
			$cross_functions = array();
        }
        $child = new $class();
        $properties = get_object_vars($parent1);
        foreach($properties as $property => $value){
            if($cross_function)
				$cross_functions[$property] = $cross_function;
			$child->$property = call_user_func($cross_functions[$property], $parent1->$property, $parent2->$property);
		}
		return $child;
    }

    function mutate(&$object, $mutation_function){ //echo "Mutation of ".strlen($object->string)."<br>";
		$properties = get_object_vars($object);
		foreach($properties as $property => $value)
			$object->$property = call_user_func($mutation_function, $object->$property);
    }
/*
    function fitness($object, $fitness_function){
		return $fitness_function($object);
    }*/

    //PRIVATE
    static function best($a, $b){
        if($a[1] == $b[1])
			return 0;
        return ($a[1] < $b[1]) ? 1 : -1;
    }


    function select($objects, $fitness_function, $n = 2){
		foreach($objects as $object)
			$selection[] = array($object, call_user_func($fitness_function, $object));
        usort($selection, array("GA", "best"));
        $selection = array_slice($selection, 0, $n);
        foreach($selection as $selected)
            $winners[] = $selected[0];
        return $winners;
    }

    //PRIVATE
    function worst($a, $b){
		if($a[1] == $b[1])
			return 0;
        return ($a[1] < $b[1]) ? -1 : 1;
    }

    function kill(&$objects, $fitness_function, $n = NULL){
		if($n == NULL)
			$n = $this->number_to_kill();
		foreach($objects as $object)
			$selection[] = array($object, call_user_func($fitness_function, $object));
        usort($selection, array("GA", "worst"));
        $selection = array_slice($selection, 0, count($selection) - $n);
        $objects = array();
        foreach($selection as $selected)
            $objects[] = $selected[0];
    }
	
	//After killing must be at least 3 individuals
	function number_to_kill(){
		return min($this->death_rate, count($this->population) - 5);
	}

    //PRIVATE
    function mass_crossover($objects, $cross_functions){
        foreach($objects as $object){
            if(!isset($obj1))
				$obj1 = $object;
            else {
                $children[] = $this->crossover($obj1, $object, $this->crossover_functions);
                $obj1 = null;
            }
        }
        return $children;
    }

    //PRIVATE
    function mass_mutation(&$objects){
        foreach($objects as $key => $object){
            if(rand(1, 100) <= $this->mutation_rate)
				$this->mutate($objects[$key], $this->mutation_function);
        }
    }
	
	function write_population_history(){
		$pop_stats = array();
		foreach($this->population as $individ){
			$idx = count($pop_stats);
			$pop_stats[$idx] = array('props'=>array());
			$ind_stats = &$pop_stats[$idx];
			$props = get_object_vars($individ);
			foreach($props as $prop => $val)
				$ind_stats['props'][] = array(
					'name' => $prop,
					'value' => $val
				);
			$pop_fitness[] = call_user_func($this->fitness_function, $individ);
		}
		$this->history[] = array(
			'population_stats' => $pop_stats
		);
	}
	
    function evolve(){
        for($i=0; $i<$this->generations; $i++){ //echo "<pre>"; print_r($this->population); echo "</pre>";
			$this->write_population_history();
			$num = 2 * min($this->num_couples, floor(count($this->population) / 2));
            $couples = $this->select($this->population, $this->fitness_function, $num);
            $children = $this->mass_crossover($couples, $this->crossover_functions);
            $this->mass_mutation($children);
            $this->population = array_merge($this->population, $children);
            $this->kill($this->population, $this->fitness_function);
        }
    }
}
?>