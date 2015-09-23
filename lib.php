<?php

	include_once 'save_in_database.php';

	//Making sense out of data
	function understanding($input, $db){
		$output = '';
		$action = '';

		//Initial check (mainly for size)
		$input_params = text_parameters($input);

		//If input is correct, eval hardcoded commands
		$hard = hardcoded($input, $db);

		if($hard != FALSE){
			$type = 'Hardcoded';
			$output .= addslashes($hard['output']);
			if($hard['out_type'] == 'append') $method = 'append';
			else $method = 'html';
			$action = "$('#".$hard['element']."').".$method."('".$output."')";
		} else {
			$output .= 'Input: <b>'.$input.'</b><br>';
			//In case we're done work already and cached it
			$meaning = check_cache_for_input($input);

			//Sanitize from common errors of transmitting data in computer systems
			$meaning = sanitize($meaning);

			//Make sure nothing in input will threaten system in any way. Output is code, ready to execute
			$input_secured = secure_data($meaning);

			//Determining data type, content type
			$input_types = typification($meaning);

			//Create simplified version in case of too complex input can slow down understanding
			$input_simplified = simplify($meaning, $input_types);

			$types = get_main_types($input_types);

			if($types === FALSE) //Can't be
				die("Unknown type of input. Something really wrong");


			$results = array();
			foreach($types as $type){ //echo $type;
				$results[] = output_mapping($input, $type);
			}
			$best_result = best_result($results);
			$output .= 'Input type: '.$best_result['type']./*print_r($types, TRUE).*/'<br><br>Output:<br><br>';
			$output .= "<b>".$best_result['output']."</b><br><br>";
			$action = "$('#output').append('".$output."');";
		}
		return array(
			'output' => $output, //Doesn't work for now, all in $action
			'action' => $action
		);
	}

	function best_result($results){
		foreach($results as $result){ //echo 'Another Result: '.$result;
			if($result['output'] != FALSE)
				return $result;
		}
	}

	function output_mapping($input, $type){
		switch($type){
			case 'Mathematical expression':
				$result_type = $type;
				include 'tools/math/evalmath.php';
				$m = new EvalMath;
				$output = $m->evaluate($input);
				break;
			default:
				$result_type = $type;
				$output = FALSE;
		}
		return array('output' => $output, 'type' => $result_type);
	}

	//Evaluate input
	function action($understanding){
		$output = $understanding['output'];
		$action = $understanding['action'];
		echo $action; //Includes output as part of js code
	}

	function get_main_types($input_types, $count = 3){
		if(count($input_types) > 0){
			if(!array_multisort($input_types, SORT_DESC))
				return FALSE;
			else {
				$main_types = array();
				for($i=0; $i<$count; $i++)
					if(isset($input_types[$i])) $main_types[] = key($input_types[$i]);
				return $main_types; //$input_types[0] = confidence
			}
		} else //No type returned, how is this possible?
			return FALSE;
	}

	//Bad, bad coding
	function hardcoded($input, $db){
		$out_type = 'append';
		$element = 'output';
		//Determine to which system will be input
		if(starts_with('google', $input)){
			$query = substr($input, 7);
			include 'tools/search/google/google.php';
			$google = google_search($query);
			foreach($google['results'] as $result)
				$output .= $result['formatted'].'<br>';
		} elseif(starts_with('play', $input)){
			$what_to_play = substr($input, 5);
			include('tools/audio/search_music_local.php');
			$music = search_music_local();
			$games = get_windows_games();
			$fuzzy_index = fuzzy_search($what_to_play, $music, $games);
			$output .= get_best_match($fuzzy_index);
		} elseif(starts_with('cmd', $input)){ //Request from user
			$command = substr($input, 4);
			$background = 0; $force_output = 1;
			$cmd = cmd($command, $background, $force_output);
			if($cmd['command_not_found'] == FALSE){
				$output = "<font color=green>";
				if(count($cmd['lines']) != 0){
					foreach($cmd['lines'] as $line)
						$output .= $line."<br>";
				} else
					$output .= 'Command executed: <b>'.$command.'</b>';
				$output .= "</font>";
			} else {
				$output = "<font color=red>".$command." is possibly not a WIN CMD command</font><br>";
			}
		} elseif(starts_with('random music', $input)){
			$out_type = 'replace';
			$element = 'audio';
			include 'tools/audio/random_audio_file.php';
			$path = random_audio_file();
			$explode = explode(".", $path);
			$extension = $explode[1];
			$localhost_path = 
			$output = "<audio controls><source src='file:///".$path."' type='audio/".$extension."'></audio>";
		} elseif(starts_with('network scan', $input)){
		} elseif(starts_with('infobar request', $input)){
			return process_infobar($db, $input);
		} else
			return FALSE; //It's not hardcoded
		return array('output' => $output.'<br>', 'element' => $element, 'out_type' => $out_type);
	}

	function secure_data($input){
		$output = $input;
		return $output;
	}

	//Get data about data
	function typification($input){ //echo 'Input: '.$input."<br>";
		$types = array();
		$regexes_json = file_get_contents('data/text/regexes.json');
		$regexes = json_decode($regexes_json, true);
		$types = type_check_regex($input, $regexes);
		//$types = type_check_regex($input, $regexes, FALSE);
		return $types;
	}

	function type_check_regex($input, $regexes, $strict = TRUE){
		if($strict == TRUE){
			$regex_function = 'preg_match_all';
			$confidence = 80;
		} else {
			$regex_function = 'preg_match';
			$confidence = 50;
		}
		$types = array();
		foreach($regexes as $regex_info){
			$regex = '/'.$regex_info[1].'/'; $regex_type = $regex_info[0];
			if($regex_function($regex, $input) != FALSE) //Can be FALSE (preg_match error) or 0 matches
				$types[] = array($regex_type => $confidence); //Type -> Confidence
		}
		return $types;
	}

	function simplify($input, $input_types){
		//Strip HTML and PHP tags
		$output = strip_tags($input);
		$output = $input;
		return $output;
	}

	function check_cache_for_input($input){
		return $input;
	}

?>