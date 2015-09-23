<?php
	//Simple perceptron
	class Perceptron {
		private $W;		// веса
		private $size;	// размерность
		private $threshold;	// порог

		/**
		 * Activation function
		 * @param array $vector
		 * @return int
		 */
		public function activation($vector){
			$sum = 0;
			for($i=0; $i<count($vector); $i++){
				$sum += $vector[$i] * $this->W[$i];
			}
			if($sum > $this->threshold)
				return 1;
			return 0;
		}

		/**
		 * Constructor. Argument is number of neurons
		 * @param int $n
		 */
		public function __construct($n){
			$this->size	= $n;
			$this->threshold = 100;
			$this->init_weight();
		}

		/**
		 * Initialization of starting weights. Random
		 */
		public function init_weight(){
			for($i=0; $i<$this->size; $i++)
				$this->W[] = rand(0, 10);
		}

		/**
		 * Save to file. Rewriting if file exists
		 * @param string $filename
		 */
		public function weight_save($filename){
			$serialize = serialize($this->W);
			fwrite(fopen($filename, "w"), $serialize);
		}

		/**
		 * Load weights from file
		 * @param string $filename
		 */
		public function weight_load($filename){
			$this->W = unserialize(file_get_contents($filename));
		}

		public function learn($vector, $d){
			if($d != $this->activation($vector)){ //We don't know this vector
				// Learning
				for($i=0; $i<$this->size; $i++){
					$this->W[$i] += $d * $vector[$i];
				}
			}
		}
	}
?>