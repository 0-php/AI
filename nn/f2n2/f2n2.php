<?php

class nn {
	var $backpropagation; /* Whether backpropagation is going to be used to train this network */
	var $lmax; /* Maximum number of neurons in a layer */
	var $layer_count; /* Total number of layers */
	var $layer_structure; /* The nth element of the array represents the number of neurons in the nth layer */
	var $learning_rate; /* Learning rate in backpropagation */
	var $momentum; /* Momentum in learning rate */
	var $weights; /* Neural net weights */
	var $weight_change; /* Stores weight change (for momentum) */
	var $bias; /* Neural net bias */
	var $layers; /* Neuron values */
	var $fitness; /* (Neuroevolution only) fitness of neural network */
	
	/* Constructor */
	/* Allocate memory required by neural network and set internal variables */
	function nn($nn_layer_count, $nn_layer_structure, $nn_backpropagation = 1, $nn_learning_rate = 0.1, $nn_momentum = 0.9) {
		global $weights, $weight_change, $layers, $bias, $layer_count, $layer_structure, $backpropagation, $learning_rate, $momentum, $lmax;
		
		/* Initialize neural network structure and settings */
		$layer_count = $nn_layer_count;
		for($i=0; $i<count($nn_layer_structure); $i++) {
			$layer_structure[$i] = $nn_layer_structure[$i];
		}
		$backpropagation = $nn_backpropagation;
		$learning_rate = $nn_learning_rate;
		$momentum = $nn_momentum;
		$fitness = 0.0;
		
		/* Calculate maximum neurons in a layer */
		$lmax = 0;
		for($i=0; $i<$layer_count; $i++) {
			if ($layer_structure[$i] > $lmax) $lmax = $layer_structure[$i];
		}
		
		/* Allocate enough memory */
		$weights = array();
		if ($backpropagation) $weight_change = array();
		$bias = array();
		$layers = array();
		for($i=0; $i<$layer_count; $i++) {
			$weights[$i] = array();
			if ($backpropagation) $weight_change[$i] = array();
			$bias[$i] = array();
			$layers[$i] = array();
			for($j=0; $j<$lmax; $j++) {
				$weights[$i][$j] = array();
				if ($backpropagation) $weight_change[$i][$j] = array();
			}
		}
		
		/* Randomly weight neural network */
		for($i=0; $i<$layer_count; $i++) {
			for($j=0; $j<$lmax; $j++) {
				for($k=0; $k<$lmax; $k++) {
					/* Values of x should fall between -1 and 1 */
					$x = mt_rand(-1000000000, 1000000000) / 1000000000;
					$weights[$i][$j][$k] = $x;
					if ($backpropagation) $weight_change[$i][$j][$k] = 0;
				}
				$x = mt_rand(-1000000000, 1000000000) / 1000000000;
				$bias[$i][$j] = $x;
			}
		}
	}
	
	/* Calculate neuron values */
	/* Returns values of output neurons given input neuron values */
	function calculate($inputs) {
		global $layer_structure, $layer_count, $layers, $weights, $bias;
		
		/* Load input neurons */
		for($i=0; $i<$layer_structure[0]; $i++) {
			$layers[0][$i] = $inputs[$i];
		}
		
		/* Layered processing */
		for($l=1; $l<$layer_count; $l++) {
			for($i=0; $i<$layer_structure[$l]; $i++) {
				$layers[$l][$i] = $bias[$l-1][$i];
				for($j=0; $j<$layer_structure[$l-1]; $j++) {
					$layers[$l][$i] += $layers[$l-1][$j] * $weights[$l-1][$j][$i];
				}
				$layers[$l][$i] = 1.0 / (1.0 + exp(-$layers[$l][$i]));
			}
		}
		
		return $layers[$layer_count-1];
	}
	
	/* Calculate neuron values and return ID of largest output neuron */
	function calculate_max_output_id($inputs) {
		global $layer_structure, $layer_count;
		
		$output_neurons = $this->calculate($inputs);
		
		$maximum = -1000000000.0;
		$out = 0;
		for($i=0; $i<$layer_structure[$layer_count-1]; $i++) {
			if ($output_neurons[$i] > $maximum) {
				$maximum = $output_neurons[$i];
				$out = $i;
			}
		}
		
		return $out;
	}
	
	/* Mutate weights of each neuron and bias according to Gaussian distribution with specified standard deviation */
	/* Not used in combination with backpropagation; mostly useful for neuroevolution */
	function mutate($std_deviation = 1.0) {
		global $layer_structure, $layer_count, $bias, $weights, $lmax;
		
		for($l=$layer_count-2; $l>=0; $l--) {
			for($i=0; $i<$layer_structure[$l]; $i++) {
				for($j=0; $j<$layer_structure[$l+1]; $j++) {
					do {
						$x1 = (mt_rand(0, 1000000000) / 500000000) - 1;
						$x2 = (mt_rand(0, 1000000000) / 500000000) - 1;
						$w = $x1 * $x1 + $x2 * $x2;
					} while($w >= 1.0);
					$w = sqrt((-2.0 * log($w)) / $w) * $std_deviation;
					$weights[$l][$i][$j] += $x1 * $w;
				}
			}
			for($j=0; $j<$layer_structure[$l+1]; $j++) {
				do {
					$x1 = (mt_rand(0, 1000000000) / 500000000) - 1;
					$x2 = (mt_rand(0, 1000000000) / 500000000) - 1;
					$w = $x1 * $x1 + $x2 * $x2;
				} while($w >= 1.0);
				$w = sqrt((-2.0 * log($w)) / $w) * $std_deviation;
				$bias[$l][$j] += $x1 * $w;
			}
		}
	}
	
	/* Calculate mean-square error */
	function get_error($desired_output) {
		global $layer_count, $layer_structure, $layers;
		
		$error = 0;
		$l = $layer_count-1;
		for($i=0; $i<$layer_structure[$l]; $i++) {
			$diff = ($layers[$l][$i] - $desired_output[$i]);
			$error += $diff * $diff;
		}
		$error /= $layer_structure[$l];
		
		return $error;
	}
	
	/* Train the neural network through backpropagation */
	function backpropagate($desired_output) {
		global $layers, $layer_count, $layer_structure, $lmax, $weights, $weight_change, $bias, $learning_rate, $momentum, $backpropagation;
		
		if (!$backpropagation) {
			die("Error: tried to use backpropagation when object is not initialized with backpropagation enabled\n");
		}
		
		$errors = array();
		for($i=0; $i<$layer_count; $i++) {
			$errors[$i] = array();
		}
		
		/* Output layer errors */
		$l = $layer_count - 1;
		for($i=0; $i<$layer_structure[$l]; $i++) {
			$errors[$l][$i] = ($desired_output[$i] - $layers[$l][$i]) * $layers[$l][$i] * (1 - $layers[$l][$i]);
		}
		
		/* Hidden layer errors */
		for($l=$layer_count-2; $l>0; $l--) {
			for($i=0; $i<$layer_structure[$l]; $i++) {
				$sum = 0;
				for($j=0; $j<$layer_structure[$l+1]; $j++) {
					$sum += $errors[$l+1][$j] * $weights[$l][$i][$j];
				}
				$errors[$l][$i] = $sum * $layers[$l][$i] * (1 - $layers[$l][$i]);
			}
		}
		
		/* Adjust weights and biases */
		for($l=$layer_count-2; $l>=0; $l--) {
			for($i=0; $i<$layer_structure[$l]; $i++) {
				for($j=0; $j<$layer_structure[$l+1]; $j++) {
					$change = $learning_rate * $errors[$l+1][$j] * $layers[$l][$i];
					$weights[$l][$i][$j] += $change + $momentum * $weight_change[$l][$i][$j];
					$weight_change[$l][$i][$j] = $change;
				}
			}
			for($j=0; $j<$layer_structure[$l+1]; $j++) {
				$bias[$l][$j] += $learning_rate * $errors[$l+1][$j];
			}
		}
	}
	
	/* Save neural network weights and biases to a file so it can be loaded later or by another program using F2N2 */
	function save($filename) {
		global $weights, $bias, $layer_count, $lmax;
		
		$fp = fopen($filename, "w");
		
		/* Write headers */
		fwrite($fp, "NN001\n");
		fwrite($fp, $layer_count . " " . $lmax . "\n");
		
		/* Save weights */
		for($l=0; $l<$layer_count; $l++) {
			for($i=0; $i<$lmax; $i++) {
				for($j=0; $j<$lmax; $j++) {
					fwrite($fp, round($weights[$l][$i][$j], 15) . " ");
				}
				fwrite($fp, round($bias[$l][$i], 15) . " ");
			}
		}
		
		fclose($fp);
	}
	
	/* Load neural network weights and biases from a file created by the save function of F2N2 */
	function load($filename) {
		global $weights, $bias, $layer_count, $lmax;
		
		$fp = fopen($filename, "r");
		
		/* Read headers */
		$tmp = fgets($fp, 10);
		$hdr = explode(" ", fgets($fp, 10));
		$loaded_layer_count = intval($hdr[0]);
		$loaded_lmax = intval($hdr[1]);
		
		/* Check headers for incompatible neural network structure */
		if ($loaded_layer_count != $layer_count || $loaded_lmax != $lmax) {
			die("Error: tried to load weights of incompatible neural network from file\n");
		}
		
		/* Load weights */
		for($l=0; $l<$layer_count; $l++) {
			for($i=0; $i<$lmax; $i++) {
				for($j=0; $j<$lmax; $j++) {
					$w = "";
					while(($c = fgetc($fp)) !== " ") {
						$w .= $c;
					}
					$weights[$l][$i][$j] = floatval($w);
				}
				$w = "";
				while(($c = fgetc($fp)) !== " ") {
					$w .= $c;
				}
				$bias[$l][$i] = floatval($w);
			}
		}
		
		fclose($fp);
	}
}

?>
