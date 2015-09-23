/*
  +----------------------------------------------------------------------+
  | FANN Extension for PHP                                               |
  +----------------------------------------------------------------------+
  | Copyright (c) 2003-2004 Evan Nemerson                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.0 of the PHP license,       |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_0.txt.                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Evan Nemerson <evan@coeus-group.com>                         |
  +----------------------------------------------------------------------+
*/

/* $Id: fann.c,v 1.12 2004/06/04 10:38:13 tadpole9 Exp $ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_fann.h"

#include <fann.h>
#include <fann_internal.h>

static int le_fann;
#define le_fann_name "FANN"

static zend_class_entry *php_fann_ce_ptr;

function_entry fann_functions[] = {
	PHP_FE(fann_create,                             NULL)
	PHP_FE(fann_train,                              NULL)
	PHP_FE(fann_save,                               NULL)
	PHP_FE(fann_run,                                NULL)
	PHP_FE(fann_randomize_weights,                  NULL)
	PHP_FE(fann_init_weights,                       NULL)

	PHP_FE(fann_set_learning_rate,                  NULL)
	PHP_FE(fann_set_activation_function_hidden,     NULL)
	PHP_FE(fann_set_activation_function_output,     NULL)
	PHP_FE(fann_set_activation_hidden_steepness,    NULL)
	PHP_FE(fann_set_activation_output_steepness,    NULL)

	PHP_FE(fann_get_MSE,                            NULL)
	PHP_FE(fann_get_learning_rate,                  NULL)
	PHP_FE(fann_get_num_input,                      NULL)
	PHP_FE(fann_get_num_output,                     NULL)
	PHP_FE(fann_get_activation_function_hidden,     NULL)
	PHP_FE(fann_get_activation_function_output,     NULL)
	PHP_FE(fann_get_activation_hidden_steepness,    NULL)
	PHP_FE(fann_get_activation_output_steepness,    NULL)
	PHP_FE(fann_get_total_neurons,                  NULL)
	PHP_FE(fann_get_total_connections,              NULL)

	{NULL, NULL, NULL}
};

#ifdef PHP_FANN_OO
function_entry fann_oo_functions[] = {
	ZEND_NAMED_FE(__construct,                  ZEND_FN(fann_create),                             NULL)
	PHP_ME(fannOO, __set,                       NULL,                                             0)
	PHP_ME(fannOO, __get,                       NULL,                                             0)

	ZEND_NAMED_FE(train,                        ZEND_FN(fann_train),                              NULL)
	ZEND_NAMED_FE(save,                         ZEND_FN(fann_save),                               NULL)
	ZEND_NAMED_FE(run,                          ZEND_FN(fann_run),                                NULL)
	ZEND_NAMED_FE(randomizeWeights,             ZEND_FN(fann_randomize_weights),                  NULL)
	ZEND_NAMED_FE(initWeights,                  ZEND_FN(fann_init_weights),                       NULL)

	ZEND_NAMED_FE(setLearningRate,              ZEND_FN(fann_set_learning_rate),                  NULL)
	ZEND_NAMED_FE(setActivationFunctionHidden,  ZEND_FN(fann_set_activation_function_hidden),     NULL)
	ZEND_NAMED_FE(setActivationFunctionOutput,  ZEND_FN(fann_set_activation_function_output),     NULL)
	ZEND_NAMED_FE(setActivationHiddenSteepness, ZEND_FN(fann_set_activation_hidden_steepness),    NULL)
	ZEND_NAMED_FE(setActivationOutputSteepness, ZEND_FN(fann_set_activation_output_steepness),    NULL)

	ZEND_NAMED_FE(getMSE,                       ZEND_FN(fann_get_MSE),                            NULL)
	ZEND_NAMED_FE(getLearningRate,              ZEND_FN(fann_get_learning_rate),                  NULL)
	ZEND_NAMED_FE(getNumInput,                  ZEND_FN(fann_get_num_input),                      NULL)
	ZEND_NAMED_FE(getNumOutput,                 ZEND_FN(fann_get_num_output),                     NULL)
	ZEND_NAMED_FE(getActivationFunctionHidden,  ZEND_FN(fann_get_activation_function_hidden),     NULL)
	ZEND_NAMED_FE(getActivationFunctionOutput,  ZEND_FN(fann_get_activation_function_output),     NULL)
	ZEND_NAMED_FE(getActivationHiddenSteepness, ZEND_FN(fann_get_activation_hidden_steepness),    NULL)
	ZEND_NAMED_FE(getActivatounOutputSteepness, ZEND_FN(fann_get_activation_output_steepness),    NULL)
	ZEND_NAMED_FE(getTotalNeurons,              ZEND_FN(fann_get_total_neurons),                  NULL)
	ZEND_NAMED_FE(getTotalConnections,          ZEND_FN(fann_get_total_connections),              NULL)

	{NULL, NULL, NULL}
};
#else
function_entry fann_oo_functions[] = {
	{NULL, NULL, NULL}
};
#endif

static struct fann * php_fann_get_ann(zval *obj TSRMLS_DC) {
	zval **tmp;
	int id_to_find;
	void *property;
	int type;

	if (obj) {
		if ( Z_TYPE_P(obj) != IS_OBJECT ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Invalid object.");
			return NULL;
		}
		if (zend_hash_find(Z_OBJPROP_P(obj), "ann", 4, (void **)&tmp) == FAILURE) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Unable to find property.");
			return NULL;
		}
		id_to_find = Z_LVAL_PP(tmp);
	} else {
		return NULL;
	}

	property = zend_list_find(id_to_find, &type);

	if (!property || type != le_fann) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Unable to find identifier.");
		return NULL;
	}

	return property;
}

zend_module_entry fann_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"fann",
	fann_functions,
	PHP_MINIT(fann),
	PHP_MSHUTDOWN(fann),
	PHP_RINIT(fann),	
	PHP_RSHUTDOWN(fann),
	PHP_MINFO(fann),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1",
#endif
	STANDARD_MODULE_PROPERTIES
};

#define PHP_FANN_ERROR_CHECK(ann) \
if ( fann_get_errno((struct fann_error *)ann) != 0 ) { \
	php_error_docref(NULL TSRMLS_CC, E_WARNING, fann_get_errstr((struct fann_error *)ann)); \
	RETURN_FALSE; \
}

#ifdef COMPILE_DL_FANN
ZEND_GET_MODULE(fann)
#endif

int php_fann_html_callback(unsigned int epochs, float error) {
	zend_printf("Epochs %8d. Current error: %.10f<br/>\n", epochs, error);
	return 0; // We always want to keep going until error < desired || epoch > max.
}

static void destroy_fann(zend_rsrc_list_entry *rsrc TSRMLS_DC)
{
	struct fann *ann;
	ann = rsrc->ptr;

	fann_destroy(ann);
}

static void php_fann_set_activation_function(INTERNAL_FUNCTION_PARAMETERS, int where) {
	zval *fann_arg;
	struct fann *ann;
	long af;

        if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &af) == FAILURE)
			WRONG_PARAM_COUNT;
                fann_arg = getThis();
        }
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ol", &fann_arg, &af) == FAILURE)
			WRONG_PARAM_COUNT;
        }
	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	switch ( af ) {
	case FANN_SIGMOID:
	case FANN_THRESHOLD:
	case FANN_SIGMOID_STEPWISE:
	case FANN_SIGMOID_SYMMETRIC:
		switch ( where ) {
		case 0:
			fann_set_activation_function_hidden(ann, af);
			break;
		case 1:
			fann_set_activation_function_output(ann, af);
			break;
		}
		break;
	default:
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Activation function not supported.");
		break;
	}
}

static void php_fann_set_activation_steepness(INTERNAL_FUNCTION_PARAMETERS, int where) {
	zval *fann_arg;
	struct fann *ann;
	double steep;

        if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "d", &steep) == FAILURE)
			WRONG_PARAM_COUNT;
                fann_arg = getThis();
        }
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "od", &fann_arg, &steep) == FAILURE)
			WRONG_PARAM_COUNT;
        }
	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	switch ( where ) {
	case 0:
		fann_set_activation_hidden_steepness(ann, steep);
		break;
	case 1:
		fann_set_activation_output_steepness(ann, steep);
		break;
	}
}

static void php_fann_get_property(INTERNAL_FUNCTION_PARAMETERS, int what) {
	zval *fann_arg;
	struct fann *ann;

        if ( getThis() ) {
                fann_arg = getThis();
        }
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &fann_arg) == FAILURE)
			WRONG_PARAM_COUNT;
        }
	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	switch ( what ) {
	case 0:
		RETURN_DOUBLE(fann_get_MSE(ann));
		break;
	case 1:
		RETURN_DOUBLE(fann_get_learning_rate(ann));
		break;
	case 2:
		RETURN_LONG(fann_get_num_input(ann));
		break;
	case 3:
		RETURN_LONG(fann_get_num_output(ann));
		break;
	case 4:
		RETURN_LONG(fann_get_activation_function_hidden(ann));
		break;
	case 5:
		RETURN_LONG(fann_get_activation_function_output(ann));
		break;
	case 6:
		RETURN_DOUBLE(fann_get_activation_hidden_steepness(ann));
		break;
	case 7:
		RETURN_DOUBLE(fann_get_activation_output_steepness(ann));
		break;
	case 8:
		RETURN_LONG(fann_get_total_neurons(ann));
		break;
	case 9:
		RETURN_LONG(fann_get_total_connections(ann));
		break;
	default: // What the...? This is an internal function.
		RETURN_FALSE;
		break;
	}
}

static struct fann_train_data * php_fann_get_train_data_from_file(INTERNAL_FUNCTION_PARAMETERS, char * filename) {
	struct fann_train_data * data;

#ifdef php_stream_open_wrapper_as_file
	FILE * train_file;

	train_file = php_stream_open_wrapper_as_file(filename, "r", REPORT_ERRORS, NULL);
	data = fann_read_train_from_fd(train_file, filename);
	fclose(train_file);
#else
	data = fann_read_train_from_file(filename);
#endif // php_stream_open_wrapper_as_file

	return data;
}

static struct fann_train_data * php_fann_get_train_data_from_array(INTERNAL_FUNCTION_PARAMETERS, zval * train_data) {
	zval **dataset, **io, **inpout;
	HashPosition train_data_pos, dataset_pos, io_pos;
	unsigned int ioro, c, x, y;
	struct fann_train_data * data;

	data = (struct fann_train_data *)emalloc(sizeof(struct fann_train_data));

	data->num_data = zend_hash_num_elements(Z_ARRVAL_P(train_data));
	data->num_input = data->num_output = 0;
	data->input = (float **)emalloc(sizeof(float *)*(data->num_data));
	data->output = (float **)emalloc(sizeof(float *)*(data->num_data));

	/* If anyone knows of a better way, I would /love/ to hear about it. */
	for (zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(train_data), &train_data_pos), c = 0;
	     zend_hash_get_current_data_ex(Z_ARRVAL_P(train_data), (void **) &dataset, &train_data_pos) == SUCCESS;
	     zend_hash_move_forward_ex(Z_ARRVAL_P(train_data), &train_data_pos), c++) {
		if ( zend_hash_num_elements(Z_ARRVAL_PP(dataset)) != 2 ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING,
					 "Argument #2 to fann_train_on_data improperly structured- could not find exact input and output arrays.");
			RETVAL_FALSE;
		}
		for ( zend_hash_internal_pointer_reset_ex(Z_ARRVAL_PP(dataset), &dataset_pos), ioro = 0;
		      zend_hash_get_current_data_ex(Z_ARRVAL_PP(dataset), (void **) &io, &dataset_pos) == SUCCESS;
		      zend_hash_move_forward_ex(Z_ARRVAL_PP(dataset), &dataset_pos), ioro++ ) {
                	if ( (y = zend_hash_num_elements(Z_ARRVAL_PP(io))) == 0 ) {
			       	php_error_docref(NULL TSRMLS_CC, E_WARNING, "Argument #2 to fann_train_on_data invalid- arrays must have values.");
			       	RETVAL_FALSE;
			}

			if ( ioro == 0 ) {
				data->input[c] = (float *)emalloc(sizeof(float) * y);
                        }
			else {
				data->output[c] = (float *)emalloc(sizeof(float) * y);
                        }

			for ( zend_hash_internal_pointer_reset_ex(Z_ARRVAL_PP(io), &io_pos), x = 0;
			      zend_hash_get_current_data_ex(Z_ARRVAL_PP(io), (void **) &inpout, &io_pos) == SUCCESS;
			      zend_hash_move_forward_ex(Z_ARRVAL_PP(io), &io_pos), x++ ) {
				if ( ioro == 0 ) {
					if ( data->num_input == 0 ) {
						data->num_input = y;
                                        }
					if ( y != data->num_input ) {
						php_error_docref(NULL TSRMLS_CC, E_WARNING,
								 "Argument #2 to fann_train_on_data inconsistent- the size of the input array must be uniform.");
						RETVAL_FALSE;
					}
					convert_to_double(*inpout);
					data->input[c][x] = Z_DVAL_PP(inpout);
				}
				else {
					if ( data->num_output == 0 ) {
						data->num_output = y;
                                        }
					if ( y != data->num_output ) {
						php_error_docref(NULL TSRMLS_CC, E_WARNING,
								 "Argument #2 to fann_train_on_data inconsistent- the size of the output array must be uniform.");
						RETVAL_FALSE;
					}
					convert_to_double(*inpout);
					data->output[c][x] = Z_DVAL_PP(inpout);
				}
			}
		}
	}
	return data;
}

static void php_fann_destroy_train_data(struct fann_train_data * data) {
	unsigned int c;

	/* Have to use efree with emalloc */
	for ( c=0 ; c<(data->num_data) ; c++ ) {
		efree(data->input[c]);
		efree(data->output[c]);
	}
	efree(data->input);
	efree(data->output);
	efree(data);
}

PHP_MINIT_FUNCTION(fann)
{
	le_fann = zend_register_list_destructors_ex(destroy_fann,NULL,le_fann_name,module_number);
	zend_class_entry php_fann_ce;

#ifdef PHP_FANN_OO
	zend_internal_function fe_set, fe_get;

	fe_set.type = ZEND_INTERNAL_FUNCTION;
	fe_set.handler = ZEND_FN(fannOO___set);
	fe_set.function_name = NULL;
	fe_set.scope = NULL;
	fe_set.fn_flags = 0;
	fe_set.prototype = NULL;
	fe_set.num_args = 2;
	fe_set.arg_info = NULL;
	fe_set.pass_rest_by_reference = 0;

	fe_get.type = ZEND_INTERNAL_FUNCTION;
	fe_get.handler = ZEND_FN(fannOO___get);
	fe_get.function_name = NULL;
	fe_get.scope = NULL;
	fe_get.fn_flags = 0;
	fe_get.prototype = NULL;
	fe_get.num_args = 2;
	fe_get.arg_info = NULL;
	fe_get.pass_rest_by_reference = 0;

	INIT_OVERLOADED_CLASS_ENTRY(php_fann_ce, "fann", fann_oo_functions, NULL, (zend_function *)&fe_get, (zend_function *)&fe_set);
#else
	INIT_CLASS_ENTRY           (php_fann_ce, "fann", fann_oo_functions);
#endif

	php_fann_ce_ptr = zend_register_internal_class(&php_fann_ce TSRMLS_CC);

	REGISTER_LONG_CONSTANT("FANN_SIGMOID",		FANN_SIGMOID, 			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("FANN_THRESHOLD",	FANN_THRESHOLD, 		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("FANN_SIGMOID_STEPWISE",	FANN_SIGMOID_STEPWISE,		CONST_CS | CONST_PERSISTENT);

	return SUCCESS;
}


PHP_MSHUTDOWN_FUNCTION(fann)
{
	return SUCCESS;
}



PHP_RINIT_FUNCTION(fann)
{
	return SUCCESS;
}



PHP_RSHUTDOWN_FUNCTION(fann)
{
	return SUCCESS;
}


PHP_MINFO_FUNCTION(fann)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "Fast Artificial Neural Network (FANN) library support", "enabled");
#ifdef PHP_FANN_OO
	php_info_print_table_row(2, "FANN object oriented API", "enabled");
#else
	php_info_print_table_row(2, "FANN object oriented API", "disabled");
#endif
	php_info_print_table_end();

}

/* {{{ mixed fann_create(mixed data [, double connection_rate, double learning_rate])
   Create an artificial neural network. */
PHP_FUNCTION(fann_create)
{
	struct fann *ann;

	if ( ZEND_NUM_ARGS() == 3 ) {
		zval *neuron_arg, **entry;
		HashPosition pos;
		double connection_rate, learning_rate;
		unsigned int c = 0, *layers;

		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "add", &neuron_arg, &connection_rate, &learning_rate) == FAILURE)
			WRONG_PARAM_COUNT;

		if ( (connection_rate < 0.0) || (connection_rate > 1.0) ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Connection rate must be between 0 and 1.");
			RETURN_FALSE;
		}
	  
		if ( (learning_rate < 0.0) ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Learning rate must be positive.");
			RETURN_FALSE;
		}
	  
		layers = (unsigned int *)emalloc(zend_hash_num_elements(Z_ARRVAL_P(neuron_arg)) * sizeof(unsigned int));
		for (zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(neuron_arg), &pos), c = 0;
		     zend_hash_get_current_data_ex(Z_ARRVAL_P(neuron_arg), (void **)&entry, &pos) == SUCCESS;
		     zend_hash_move_forward_ex(Z_ARRVAL_P(neuron_arg), &pos), c++) {
			convert_to_long_ex(entry);
			layers[c] = (unsigned int)Z_LVAL_PP(entry);
		}

		ann = fann_create_array(connection_rate, learning_rate, c, layers);
		efree(layers);

                int ret = zend_list_insert(ann, le_fann);
                object_init_ex((getThis() ? getThis() : return_value), php_fann_ce_ptr);
                add_property_resource((getThis() ? getThis() : return_value), "ann", ret);
                zend_list_addref(ret);
	}
	else if (ZEND_NUM_ARGS() == 1) {
		char *filename;
		int filename_l;

		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &filename, &filename_l) == FAILURE)
			WRONG_PARAM_COUNT;

#ifdef php_stream_open_wrapper_as_file
		FILE *fd;

		fd = php_stream_open_wrapper_as_file(filename, "r", REPORT_ERRORS, NULL);
		if ( (ann = fann_create_from_fd(fd, filename)) == NULL ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Could not create from file.");
			RETURN_FALSE;
		}
		else {
                        int ret = zend_list_insert(ann, le_fann);
                        object_init_ex(return_value, php_fann_ce_ptr);
                        add_property_resource(return_value, "ann", ret);
                        zend_list_addref(ret);
		}
		fclose(fd);
#else
		if ( (ann = fann_create_from_file(filename)) == NULL ) {
	  		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Could not create from file.");
			RETURN_FALSE;
		}	  
		else {
                        int ret = zend_list_insert(ann, le_fann);
                        object_init_ex((getThis() ? getThis() : return_value), php_fann_ce_ptr);
                        add_property_resource((getThis() ? getThis() : return_value), "ann", ret);
                        zend_list_addref(ret);
		}
#endif // php_stream_open_wrapper_as_file
	}
	else {
		WRONG_PARAM_COUNT;
		RETURN_FALSE;
	}

	PHP_FANN_ERROR_CHECK(ann);
}
/* }}} */

/* {{{ bool fann_train(resource ann, mixed data, int max_iterations, double desired_error [, int iterations_between_reports])
   Train an ANN. */
PHP_FUNCTION(fann_train)
{
	zval *fann_arg, *train_data;
	struct fann *ann;
	long max_iterations, iterations_between_reports;
	double desired_error;
	struct fann_train_data *data;

	if ( getThis() ) {
		if (zend_parse_parameters_ex(ZEND_PARSE_PARAMS_QUIET, ZEND_NUM_ARGS() TSRMLS_CC, "zld|l",
					     &train_data,
					     &max_iterations,
					     &desired_error,
					     &iterations_between_reports,
					     ZEND_PARSE_PARAMS_QUIET) == FAILURE)
			WRONG_PARAM_COUNT;

		fann_arg = getThis();
	}
	else {
		if (zend_parse_parameters_ex(ZEND_PARSE_PARAMS_QUIET, ZEND_NUM_ARGS() TSRMLS_CC, "ozld|l",
					     &fann_arg,
					     &train_data,
					     &max_iterations,
					     &desired_error,
					     &iterations_between_reports,
					     ZEND_PARSE_PARAMS_QUIET) == FAILURE)
			WRONG_PARAM_COUNT;
	}
	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

 
	if ( ZEND_NUM_ARGS() < (5 - (getThis() ? 1 : 0)) )
		iterations_between_reports = 0;

	if ( max_iterations < 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Max iterations must be positive.");
		RETURN_FALSE;
	}

	if ( desired_error < 0.0f ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Desired error must be positive.");
		RETURN_FALSE;
	}

	if ( iterations_between_reports < 0 ) {
		iterations_between_reports = 0;
		php_error_docref(NULL TSRMLS_CC, E_NOTICE, "Iterations between reports must be positive, assuming no reporting.");
	}

	switch ( Z_TYPE_P(train_data) ) {
		case IS_ARRAY:
			data = php_fann_get_train_data_from_array(INTERNAL_FUNCTION_PARAM_PASSTHRU, train_data);
                        break;
		case IS_STRING:
			data = php_fann_get_train_data_from_file(INTERNAL_FUNCTION_PARAM_PASSTHRU, Z_STRVAL_P(train_data));
                        break;
		default:
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Training data must be an array or file name.");
			RETURN_FALSE;
			break;
        }

	fann_train_on_data_callback(ann, data, max_iterations, iterations_between_reports, (float)desired_error, &php_fann_html_callback);

        // Perhaps it would be good to re-implement php_fann_get_train_data_from_file so it uses emalloc instead of relying on FANN...
        (Z_TYPE_P(train_data) == IS_ARRAY) ? php_fann_destroy_train_data(data) : fann_destroy_train(data);

	PHP_FANN_ERROR_CHECK(ann);
	RETURN_TRUE;
}
/* }}} */

/* {{{ bool fann_save(resource ann, string filename)
   Save a neural network to a file. */
PHP_FUNCTION(fann_save)
{
	zval *fann_arg;
	struct fann *ann;
	char *filename;
	int filename_l;

	if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &filename, &filename_l) == FAILURE)
			WRONG_PARAM_COUNT;

		fann_arg = getThis();
	}
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "os", &fann_arg, &filename, &filename_l) == FAILURE)
			WRONG_PARAM_COUNT;
	}

	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

#ifdef php_stream_open_wrapper_as_file
	FILE *fd = php_stream_open_wrapper_as_file(filename, "w+", REPORT_ERRORS, NULL);;
	fann_save_internal_fd(ann, fd, filename, 0);
	fclose(fd);
#else
	fann_save(ann, filename);
#endif // php_stream_open_wrapper_as_file

	PHP_FANN_ERROR_CHECK(ann);

	RETURN_TRUE;
}
/* }}} */

/* {{{ mixed fann_run(resource ann, array input)
   Run an artificial neural network. */
PHP_FUNCTION(fann_run)
{
	zval *fann_arg, *array, **elem;
	HashPosition pos;
	struct fann *ann;
	float *input, *calc_out;
	int c = 0, num_out = 0;

	if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "a", &array) == FAILURE)
			WRONG_PARAM_COUNT;
		fann_arg = getThis();
	}
	else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "oa", &fann_arg, &array) == FAILURE)
			WRONG_PARAM_COUNT;
	}

	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	input = (float *)emalloc(sizeof(float)*zend_hash_num_elements(Z_ARRVAL_P(array)));

	for (zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(array), &pos);
	     zend_hash_get_current_data_ex(Z_ARRVAL_P(array), (void **) &elem, &pos) == SUCCESS;
	     zend_hash_move_forward_ex(Z_ARRVAL_P(array), &pos)) {
		convert_to_double(*elem);
		input[c++] = Z_DVAL_PP(elem);
	}

	calc_out = fann_run(ann, input);

	num_out = fann_get_num_output(ann);
	array_init(return_value);
	for ( c=0 ; c<num_out ; c++ ) {
		add_next_index_double(return_value, calc_out[c]);
	}

	efree(input);

	PHP_FANN_ERROR_CHECK(ann);
}
/* }}} */

/* {{{ void fann_randomize_weights(resource ann [, double min [, double max]])
   Set the weights for an ANN to a random value between min and max. */
PHP_FUNCTION(fann_randomize_weights)
{
	zval *fann_arg;
	struct fann *ann;
	double min, max;

        if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|dd", &min, &max) == FAILURE)
			WRONG_PARAM_COUNT;

		fann_arg = getThis();
        }
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o|dd", &fann_arg, &min, &max) == FAILURE)
			WRONG_PARAM_COUNT;
        }

	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	switch ( ZEND_NUM_ARGS() ) {
	case 1:
		min = -0.1;
	case 2:
		max = 0.1;
		break;
	}

	fann_randomize_weights(ann, min, max);

	PHP_FANN_ERROR_CHECK(ann);

	RETURN_TRUE;
}
/* }}} */

/* {{{ void fann_init_weights(resource ann, mixed train_data)
   Initialize the weights using the Nguyen-Widrow algorithm. */
PHP_FUNCTION(fann_init_weights)
{
	zval *fann_arg, *train_data;
	struct fann *ann;
	struct fann_train_data * data;

        if ( getThis() ) {
          if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &train_data) == FAILURE )
            WRONG_PARAM_COUNT;

          fann_arg = getThis();
        }
        else {
          if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "oz", &fann_arg, &train_data) == FAILURE )
            WRONG_PARAM_COUNT;
        }

	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

        switch ( Z_TYPE_P(train_data) ) {
        case IS_ARRAY:
          data = php_fann_get_train_data_from_array(INTERNAL_FUNCTION_PARAM_PASSTHRU, train_data);
          break;
        case IS_STRING:
          data = php_fann_get_train_data_from_file(INTERNAL_FUNCTION_PARAM_PASSTHRU, Z_STRVAL_P(train_data));
          break;
        default:
          WRONG_PARAM_COUNT;
          break;
        }

	fann_init_weights(ann, data);
	PHP_FANN_ERROR_CHECK(data);
	php_fann_destroy_train_data(data);
	PHP_FANN_ERROR_CHECK(ann);
}
/* }}} */

/* {{{ void fann_set_learning_rate(resource ann, float learning_rate)
   Set the learning rate. */
PHP_FUNCTION(fann_set_learning_rate)
{
	zval *fann_arg;
	struct fann *ann;
	double learning_rate;

        if ( getThis() ) {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "d", &learning_rate) == FAILURE)
			WRONG_PARAM_COUNT;
		fann_arg = getThis();
        }
        else {
		if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "od", &fann_arg, &learning_rate) == FAILURE)
			WRONG_PARAM_COUNT;
        }
	if ( (ann = php_fann_get_ann(fann_arg TSRMLS_CC)) == NULL ) { RETURN_FALSE; }

	if ( learning_rate < 0.0 )
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Learning rate must be positive.");
	else
		fann_set_learning_rate(ann, learning_rate);

	PHP_FANN_ERROR_CHECK(ann);
}
/* }}} */

/* {{{ void fann_set_activation_function_hidden(resource ann, int activation_function)
   Set the activation function for the hidden layers. */
PHP_FUNCTION(fann_set_activation_function_hidden)
{
	php_fann_set_activation_function(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
}
/* }}} */

/* {{{ void fann_set_activation_function_output(resource ann, int activation_function)
   Set the activation function for the output. */
PHP_FUNCTION(fann_set_activation_function_output)
{
	php_fann_set_activation_function(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
}
/* }}} */

/* {{{ void fann_set_activation_hidden_steepness(resource ann, int activation_function)
   Set the steepness for the hidden layers. */
PHP_FUNCTION(fann_set_activation_hidden_steepness)
{
	php_fann_set_activation_steepness(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
}
/* }}} */

/* {{{ void fann_set_activation_output_steepness(resource ann, int activation_function)
   Set the steepness for the output layers. */
PHP_FUNCTION(fann_set_activation_output_steepness)
{
	php_fann_set_activation_steepness(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
}
/* }}} */

/* {{{ double fann_get_MSE(resource ann)
   Gets the current mean-squared error. */
PHP_FUNCTION(fann_get_MSE)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);

}
/* }}} */

/* {{{ double fann_get_learning_rate(resource ann)
   Gets the current learning rate for the ANN. */
PHP_FUNCTION(fann_get_learning_rate)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
}
/* }}} */

/* {{{ long fann_get_num_input(resource ann)
   Gets the number of input neurons in the ANN. */
PHP_FUNCTION(fann_get_num_input)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 2);
}
/* }}} */

/* {{{ long fann_get_num_output(resource ann)
   Gets the number of output neurons in the ANN. */
PHP_FUNCTION(fann_get_num_output)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 3);
}
/* }}} */

/* {{{ long fann_get_activation_function_hidden(resource ann)
   Gets the activation function for the hidden neurons. */
PHP_FUNCTION(fann_get_activation_function_hidden)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 4);
}
/* }}} */

/* {{{ long fann_get_activation_function_output(resource ann)
   Gets the activation function for the output neurons. */
PHP_FUNCTION(fann_get_activation_function_output)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 5);
}
/* }}} */

/* {{{ double fann_get_activation_hidden_steepness(resource ann)
   Get the steepness of the activation function for the hidden neurons. */
PHP_FUNCTION(fann_get_activation_hidden_steepness)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 6);
}
/* }}} */

/* {{{ double fann_get_activation_output_steepness(resource ann)
   Get the steepness of the activation function for the output neurons. */
PHP_FUNCTION(fann_get_activation_output_steepness)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 7);
}
/* }}} */

/* {{{ long fann_get_total_neurons(resource ann)
   Get the total number of neurons in the ANN. */
PHP_FUNCTION(fann_get_total_neurons)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 8);
}
/* }}} */

/* {{{ long fann_get_total_connections(resource ann)
   Get the total number of connections in the ANN. */
PHP_FUNCTION(fann_get_total_connections)
{
	php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 9);
}
/* }}} */

#ifdef PHP_FANN_OO
/* {{{ mixed fann::__get(string property)
   Get different parameters from an ANN. */
PHP_METHOD(fannOO, __get)
{
	char *var;
	int var_l;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &var, &var_l) == FAILURE)
		WRONG_PARAM_COUNT;

	if ( strcasecmp(var, "MSE") == 0 ) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
	}
	else if ( strcasecmp(var, "learningRate") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
	}
	else if ( strcasecmp(var, "numInput") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 2);
	}
	else if ( strcasecmp(var, "numOutput") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 3);
	}
	else if ( strcasecmp(var, "activationFunctionHidden") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 4);
	}
	else if ( strcasecmp(var, "activationFunctionOutput") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 5);
	}
	else if ( strcasecmp(var, "activationHiddenSteepness") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 6);
	}
	else if ( strcasecmp(var, "activationOutputSteepness") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 7);
	}
	else if ( strcasecmp(var, "totalNeurons") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 8);
	}
	else if ( strcasecmp(var, "totalConnections") == 0) {
		php_fann_get_property(INTERNAL_FUNCTION_PARAM_PASSTHRU, 9);
	}
	else { // Actually have to get something...
		zval *tmp, *property;

		if ( zend_hash_find(Z_OBJPROP_P(getThis()), var, var_l, (void *)&tmp) == FAILURE ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "Unable to find property.");
			RETURN_NULL();
		}
		else { // Property exists...
			int type;

			property = zend_list_find(Z_LVAL_P(tmp), &type);
			if ( !property ) {
				php_error_docref(NULL TSRMLS_CC, E_WARNING, "Unable to find identifier.");
				RETURN_NULL();
			}
			RETURN_ZVAL(property, 0, 0);
		}
	}
}
/* }}} */

/* {{{ void fann::__set(string property, mixed value)
   Set different parameters of an ANN. */
PHP_METHOD(fannOO, __set)
{
	zval *val, *tmp;
	char *var;
	int var_l;
	struct fann *ann;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sz", &var, &var_l, &val) == FAILURE)
		WRONG_PARAM_COUNT;

	if ( zend_hash_find(Z_OBJPROP_P(getThis()), var, var_l, (void *)&tmp) == SUCCESS ) { // Property exists
		zend_hash_index_update(Z_OBJPROP_P(getThis()), Z_LVAL_P(tmp), (void *)&val, sizeof(zval *), NULL);
	}
	else { // Property doesn't exist
		add_property_zval(getThis(), var, val);
	}

	if ( (ann = php_fann_get_ann(getThis() TSRMLS_CC)) != NULL ) {
		if ( strcasecmp(var, "learningRate") == 0) {
			convert_to_double(val);
			fann_set_learning_rate(ann, Z_DVAL_P(val));
		}
		else if ( strcasecmp(var, "activationFunctionHidden") == 0) {
			convert_to_long(val);
			fann_set_activation_function_hidden(ann, Z_LVAL_P(val));
		}
		else if ( strcasecmp(var, "activationFunctionOutput") == 0) {
			convert_to_long(val);
			fann_set_activation_function_output(ann, Z_LVAL_P(val));
		}
		else if ( strcasecmp(var, "activationHiddenSteepness") == 0) {
			convert_to_long(val);
			fann_set_activation_hidden_steepness(ann, Z_LVAL_P(val));
		}
		else if ( strcasecmp(var, "activationOutputSteepness") == 0) {
			convert_to_long(val);
			fann_set_activation_output_steepness(ann, Z_LVAL_P(val));
		}
	}
}
/* }}} */
#endif // PHP_FANN_OO
