<?php

class Sensor {
	var $data;

	function Sensor($data){
		$this->data = $data;
		$this->quantize();
	}

	function quantize(){
		$this->data = str_split($this->data);
	}

	function dump(){
		return $this->data;
	}
}

?>