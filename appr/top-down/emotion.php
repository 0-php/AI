<?php

Class Emotion {

	var $name;
	var $intensity;
	var $valency;
	var $stensity;

	function Emotion($name, $need = FALSE){
		if($need != FALSE)
			$this->generate_by_needs();

	}

	function generate_by_needs($needs){
		$this->name = $need->intensity * $need->probability_of_completing;
	}

	function affect_physiology(){

	}

}

?>