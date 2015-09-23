<?php

/* demo.php
 * $Id: demo.php,v 1.3 2004/01/19 22:33:51 tadpole9 Exp $
 *
 * This file should help explain the FANN API from a PHP perspective. It
 * is basically a PHP port of the simple_test/simple_train examples from
 * the FANN distribution. The original C versions can be found in
 * $FANN_DIR/examples
 *
 * There are a few functions that aren't demonstrated in this file, here
 * are their prototypes:
 *
 * void fann_randomize_weights(resource ann [, double min [, double max]])
 * void fann_set_learning_rate(resource ann, float learning_rate)
 * void fann_set_activation_function_hidden(resource ann, int activation_function)
 * void fann_set_activation_function_output(resource ann, int activation_function)
 * void fann_set_activation_hidden_steepness(resource ann, int activation_function)
 * void fann_set_activation_output_steepness(resource ann, int activation_function)
 * double fann_get_MSE(resource ann)
 * double fann_get_learning_rate(resource ann)
 * long fann_get_num_input(resource ann)
 * long fann_get_num_output(resource ann)
 * long fann_get_activation_function_hidden(resource ann)
 * long fann_get_activation_function_output(resource ann)
 * double fann_get_activation_hidden_steepness(resource ann)
 * double fann_get_activation_output_steepness(resource ann)
 * long fann_get_total_neurons(resource ann)
 * long fann_get_total_connections(resource ann)
 *
 * If you have any questions or comments, please e-mail Evan Nemerson
 * <evan@coeus-group.com>
 */

/* If you don't want to compile FANN into PHP... */
if ( !extension_loaded('fann') ) {
  if ( !dl('fann.so') ) {
    exit("You must install the FANN extension. You can get it from http://fann.sf.net/\n");
  }
}

/* Create an artificial neural network */
$ann = fann_create(
		   array(2, 4, 1), /* layers. in this case, three layers- two input neurons, 4 neurons in a hidden layer, and one output neuron */
		   1.0, /* learning rate */
		   0.7 /* connection rate */
		   );

/* To load from a file, you can use. If your version of PHP includes the streams API (4.3.0+ ?),
 * this can be anything accessible through streams (http, ftp, https, etc) */
// $ann = fann_create("http://example.com/xor_float.net");

/* Train the network using the same data as is in the xor.data file */
fann_train($ann,
	   array(
		 array(
		       array(0,0), /* Input(s) */
		       array(0) /* Output(s) */
		       ),
		 array(
		       array(0,1), /* Input(s) */
		       array(1) /* Output(s) */
		       ),
		 array(
		       array(1,0), /* Input(s) */
		       array(1) /* Output(s) */
		       ),
		 array(array(1,1), /* Input(s) */
		       array(0) /* Output(s) */
		       )
		 ),
	   100000, /* Maximum number of epochs */
	   0.00001, /* Desired error. */
	   1000 /* Number of epochs between reports */
	   );

/* To achieve the same effect as the above with the data stored in an external file... Also works
 * with the streams API, when available. */
// fann_train($ann, '/home/tadpole/local/src/fann/examples/xor.data', 100000, 0.00001, 1000);

print_r(fann_run($ann, array(0,0))); // Should be ~ 0
print_r(fann_run($ann, array(0,1))); // Should be ~ 1
print_r(fann_run($ann, array(1,0))); // Should be ~ 1
print_r(fann_run($ann, array(1,1))); // Should be ~ 0

/* This function is pretty simple. It will use the streams API if available. */
fann_save($ann, 'xor_float.net');

?>
