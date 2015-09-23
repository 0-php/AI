<?php

class Utilities extends NeuralNetwork {
	/**
	 * Saving weights to file
	 * Rewrites existing file
	 *
	 * @param string $filename
	 */
	public function weight_save($filename){
		$serialize = serialize($this->w);
		fwrite(fopen($filename, "w"), $serialize);
	}

	/**
	 * Loading weights from file
	 * @param string $filename
	 */
	public function weight_load($filename){
		$this->w = unserialize(file_get_contents($filename));
	}
}

?>