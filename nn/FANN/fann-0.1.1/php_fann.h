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

/* $Id: php_fann.h,v 1.8 2004/06/01 07:29:21 tadpole9 Exp $ */

#ifndef PHP_FANN_H
#define PHP_FANN_H

extern zend_module_entry fann_module_entry;
#define phpext_fann_ptr &fann_module_entry

#if PHP_MAJOR_VERSION >= 5
#define PHP_FANN_OO 1
#endif

#ifdef PHP_WIN32
#define PHP_FANN_API __declspec(dllexport)
#else
#define PHP_FANN_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(fann);
PHP_MSHUTDOWN_FUNCTION(fann);
PHP_RINIT_FUNCTION(fann);
PHP_RSHUTDOWN_FUNCTION(fann);
PHP_MINFO_FUNCTION(fann);

PHP_FUNCTION(fann_create);
PHP_FUNCTION(fann_train);
PHP_FUNCTION(fann_save);
PHP_FUNCTION(fann_run);
PHP_FUNCTION(fann_randomize_weights);
PHP_FUNCTION(fann_init_weights);

PHP_FUNCTION(fann_set_learning_rate);
PHP_FUNCTION(fann_set_activation_function_hidden);
PHP_FUNCTION(fann_set_activation_function_output);
PHP_FUNCTION(fann_set_activation_hidden_steepness);
PHP_FUNCTION(fann_set_activation_output_steepness);

PHP_FUNCTION(fann_get_MSE);
PHP_FUNCTION(fann_get_learning_rate);
PHP_FUNCTION(fann_get_num_input);
PHP_FUNCTION(fann_get_num_output);
PHP_FUNCTION(fann_get_activation_function_hidden);
PHP_FUNCTION(fann_get_activation_function_output);
PHP_FUNCTION(fann_get_activation_hidden_steepness);
PHP_FUNCTION(fann_get_activation_output_steepness);
PHP_FUNCTION(fann_get_total_neurons);
PHP_FUNCTION(fann_get_total_connections);

#ifdef PHP_FANN_OO
PHP_METHOD(fannOO, __set);
PHP_METHOD(fannOO, __get);
#endif

#ifdef ZTS
#define FANN_G(v) TSRMG(fann_globals_id, zend_fann_globals *, v)
#else
#define FANN_G(v) (fann_globals.v)
#endif

#endif


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
