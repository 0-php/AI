<?php

/*
FANN 2.1.0 Extension for PHP 5.2.x helper class.
Copyright (C) 2010 Laurynas Karvelis, FX SMS ALERT (http://www.fxsmsalert.com)

This library is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General
Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option)
any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to
the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// define FANN layer functions
define('FANN_LINEAR', 						0);
define('FANN_THRESHOLD', 					1);
define('FANN_THRESHOLD_SYMMETRIC', 		    2);
define('FANN_SIGMOID', 					    3);
define('FANN_SIGMOID_STEPWISE', 			4);
define('FANN_SIGMOID_SYMMETRIC', 			5);
define('FANN_SIGMOID_SYMMETRIC_STEPWISE', 	6);
define('FANN_GAUSSIAN', 					7);
define('FANN_GAUSSIAN_SYMMETRIC', 			8);
define('FANN_GAUSSIAN_STEPWISE', 			9);
define('FANN_ELLIOT', 						10);
define('FANN_ELLIOT_SYMMETRIC', 			11);
define('FANN_LINEAR_PIECE', 				12);
define('FANN_LINEAR_PIECE_SYMMETRIC', 		13);
define('FANN_SIN_SYMMETRIC', 				14);
define('FANN_COS_SYMMETRIC', 				15);
define('FANN_SIN', 						    16);
define('FANN_COS', 						    17);


/**
 * Fann ANN manipulation class
 */
class fann {
	private $annId;

	public function __construct() {
		if(!function_exists('fann_create_standard')) {
			die('FANN extension is not loaded. Therefore class won\'t be loaded.');
		}
	}

	/**
	 * Create ANN in standard way
	 * @param int number of layers
	 * @param int input layer size
	 * @param int next layer size
	 * @param int next layer size
	 * @param int last layer size
	 * @return void
	 */
	public function create_standard($numberOfLayers, $l1num, $l2num, $l3num = 0, $l4num = 0) {
		$this->annId = fann_create_standard($numberOfLayers, $l1num, $l2num, $l3num, $l4num);

		if($this->annId < 0) {
			die('Could not initialize another ANN network.');
		}
	}

	/**
	 * Create (load) ANN saved from file
	 * @param string filename with absolute path
	 * @return void
	 */

	public function create_from_file($filename) {
		$this->annId = fann_create_from_file($filename);

		if($this->annId < 0) {
			die('Could not initialize another ANN network.');
		}
	}

	/**
	 * Run ANN and get it's output values
	 * @param array input vector
	 * @return mixed array output vector, null in error
	 */

	public function run(array $inputVector) {
		$this->checkIfAnnIsAssigned();
		$status = fann_run($this->annId, $inputVector);


		if($status == true) {
			$output = array();
			$outputCount = $this->get_num_output();

			for($i = 0; $i < $outputCount; $i++) {
				$output[] = $this->get_output($i);
			}

			return $output;
		} else {
			return null;
		}
	}

	/**
	 * Train ANN incrementally
	 * @param array input vector
	 * @param array output vector
	 * @return bool true on success, false on error
	 */

	public function train(array $input_vector, array $output_vector) {
		$this->checkIfAnnIsAssigned();
		return fann_train($this->annId, $input_vector, $output_vector);
	}

	/**
	 * Train ANN with all data given from training file
	 * @param string filename with absolute path
	 * @param int max epochs to train
	 * @param float desired error
	 * @return bool true on success, false on error
	 */

	public function train_on_file($path, $max_epochs, $desired_error) {
		$this->checkIfAnnIsAssigned();
		return fann_train_on_file($this->annId, $path, $max_epochs, $desired_error);
	}

	/**
	 * Test ANN with input and output
	 * @param array input vector
	 * @param array output vector
	 * @return bool true on success, false on error
	 */

	public function test(array $input_vector, array $output_vector) {
		$this->checkIfAnnIsAssigned();
		return fann_test($this->annId, $input_vector, $output_vector);
	}

	/**
	 * Randomize ANN weights
	 * @param double min weight
	 * @param double max weight
	 * @return bool true on success, false on error
	 */

	public function randomize_weights($min_weight, $max_weight) {
		$this->checkIfAnnIsAssigned();
		return fann_randomize_weights($this->annId, $min_weight, $max_weight);
	}

	/**
	 * Gets mean square error (MSE)
	 * @return double MSE
	 */

	public function get_MSE() {
		$this->checkIfAnnIsAssigned();
		return fann_get_MSE($this->annId);
	}

	/**
	 * Save ANN to file
	 * @param string filename with absolute path
	 * @return bool true on success, false on error
	 */

	public function save($filename) {
		$this->checkIfAnnIsAssigned();
		return fann_save($this->annId, $filename);
	}

	/**
	 * Resets mean square error (MSE)
	 * @return bool true on success, false on error
	 */

	public function reset_MSE() {
		$this->checkIfAnnIsAssigned();
		return fann_reset_MSE($this->annId);
	}

	/**
	 * Gets number of inputs
	 * @return int number of inputs
	 */

	public function get_num_input() {
		$this->checkIfAnnIsAssigned();
		return fann_get_num_input($this->annId);
	}

	/**
	 * Gets number of outputs
	 * @return int number of outputs
	 */

	public function get_num_output() {
		$this->checkIfAnnIsAssigned();
		return fann_get_num_output($this->annId);
	}

	/**
	 * Sets activation function for given layer
	 * @param int activation function (SEE TOP OF THIS FILE)
	 * @param int layer
	 * @return bool true on success, false on error
	 */

	public function set_activation_function_layer($activation_function, $layer) {
		$this->checkIfAnnIsAssigned();
		return fann_set_activation_function_layer($this->annId, $activation_function, $layer);
	}

	/**
	 * Sets activation function for hidden layer
	 * @param int activation function (SEE TOP OF THIS FILE)
	 * @return bool true on success, false on error
	 */

	public function set_activation_function_hidden($activation_function) {
		$this->checkIfAnnIsAssigned();
		return fann_set_activation_function_hidden($this->annId, $activation_function);
	}

	/**
	 * Sets activation function for output layer
	 * @param int activation function (SEE TOP OF THIS FILE)
	 * @return bool true on success, false on error
	 */

	public function set_activation_function_output($activation_function) {
		$this->checkIfAnnIsAssigned();
		return fann_set_activation_function_output($this->annId, $activation_function);
	}

	/**
	 * Gets output value after fann::run() method is called (mostly not needed for average user)
	 * @param int output number
	 * @return double output
	 */

	public function get_output($output_number) {
		$this->checkIfAnnIsAssigned();
		return fann_get_output($this->annId, $output_number);
	}

	/**
	 * @return Destroy this ANN network
	 */

	public function destroy() {
		if($this->annId < 0) {
			return;
		}

		fann_destroy($this->annId);
	}

	public function __destroy() {
		$this->destroy();
	}

	/**
	 * Checks if out object still is attached to existing ANN
	 * @return void
	 */

	private function checkIfAnnIsAssigned() {
		if($this->annId < 0) {
			die('First call fann::create_standard or fann::create_from_file method to create ANN network.');
		}
	}
}

?>