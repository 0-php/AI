<?php
define('DATA_FOLDER', 'D:/web/www/lh/tests/fuzzy/data/');

class Tests {

	//$tests - data with all tests in array with two arrays, input and output with questions and answers respectively
	//$tests_percent - percent of tests, that will be used as real testing, other data will be used in training
	function Tests($tests, $tests_percent = 30){
		switch($tests){
			case 'geometry':
				$data = $this->LoadJSONTest(DATA_FOLDER.'shapes/lines_squares.json');
				break;
			case 'geometry_2':
				$data = $this->LoadJSONTest(DATA_FOLDER.'shapes/lines_squares_triangles.json');
				break;
		}
		//Making actual test and train data
		$this->train_data = array();
		$this->test_data = array();
		
		$tests_count = ceil((count($data['input']) / 100) * $tests_percent);
		
		$test_keys = array_rand($data['input'], $tests_count);
		
		foreach($test_keys as $key){
			$this->test_data['input'][] = $data['input'][$key];
			$this->test_data['output'][] = $data['output'][$key];
			unset($data['input'][$key], $data['output'][$key]);
		}
		$this->train_data = $data;
	}
	
	function LoadJSONTest($filename){
		return (array) json_decode(file_get_contents($filename));
	}

}

?>