<?php

class talkingMachine
{
	//config vars
	var	$MAX_SYLLABLE = 5;
	var	$N_PASS = 1000;
	var	$LENGTH_PASS = 8;
	var	$MAX_WORDS = 1000;
	//var	$charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ,.!?\'";
	var	$charset = '';
	var	$no_pass = " ,.!?\':[]();";
	var	$WEIGHT_USER = 200;

	//work vars
	var	$stats_array = array();
	var	$f_in = '';
	var	$hnd_in = '';
	var	$f_out = '';
	var	$hnd_out = '';
	var	$n_rand = 0;

	//output vars
	var	$list_pass = array();
	var	$speech = '';

	//input vars
	var	$argc;
	var	$argv;

	function talkingMachine( $_argc, $_argv)
	{
		$this->argc = $_argc;
		$this->argv = $_argv;

		echo $this->_presentation();

		if ( $this->argc < 2)
		{
			echo $this->_help();
			exit;
		}

		switch ( $this->argv[1])
		{
			case '-train':
				$this->_training();
				break;
			case '-pass':
				$this->_generatePass();
				break;
			case '-talk':
				$this->_talk();
				break;
			case '-dialog':
				$this->_dialog();
				break;
			default:
				echo $this->_help();
				exit;
		}
	}

	function _presentation()
	{
		$txt = "\n\n";
		$txt .=" _______      _  _     _   	            \n";
		$txt .="|__   __|    | || |   (_)              	\n";
		$txt .="   | |  __ _ | || | __ _  _ __    __ _ \n";
		$txt .="   | | / _` || || |/ /| || '_ \  / _` |\n";
		$txt .="   | || (_| || ||   < | || | | || (_| |\n";
		$txt .="   |_| \__,_||_||_|\_\|_||_| |_| \__, |\n";
		$txt .="                                  __/ |\n";
		$txt .="                                 |___/ \n";
		$txt .=" __  __               _      _             \n"; 
		$txt .="|  \/  |             | |    (_)             \n";
		$txt .="| \  / |  __ _   ___ | |__   _  _ __    ___ \n";
		$txt .="| |\/| | / _` | / __|| '_ \ | || '_ \  / _ \ \n";
		$txt .="| |  | || (_| || (__ | | | || || | | ||  __/\n";
		$txt .="|_|  |_| \__,_| \___||_| |_||_||_| |_| \___|\n";
		$txt .="---------------------------------------------\n";
		$txt .="Francisco J.Arias 2007\n";

		return $txt;
	}

	function _help()
	{
		$txt = "Use:\n";
		$txt .= "1.In order to train Bayesian Machine from a (plain) text file in any language:\n";
		$txt .= ">php TM.php -train TRAINING_TEXT_FILE_INPUT STATS_FILE_OUTPUT [STATS_FILE_TO_MERGE]\n";
		$txt .= "\n";
		$txt .= "2.In order to make the machine to generate passwords from a file of statistics generated before:\n";
		$txt .= ">php TM.php -pass STATS_FILE PASSWORDS_FILE_OUTPUT [PASSWORD LEGNTH=8] [NUMBER OF PASSWORDS=1000]\n";
		$txt .= "\n";
		$txt .= "3.In order to make the machine to talk and to imitate a language:\n";
		$txt .= ">php TM.php -talk STATS_FILE SPEECH_FILE_OUTPUT [NUMBER OF WORDS=1000]\n";
		$txt .= "\n";
		$txt .= "4.In order to have a chat with the machine:\n";
		$txt .= ">php TM.php -dialog STATS_FILE";
		
		return $txt;
	}

	function _training()
	{
		if (( $this->argc != 5) && ( $this->argc != 4))
		{
			echo $this->_help();
			exit;
		}

		//merging existing statistics file
		if ( $this->argc == 5)
		{
			//read stats array file
			$this->f_in = $this->argv[4];
			if (!file_exists( $this->f_in))
			{
				echo "ERROR: Stats file input ".$this->f_in." does not exists.";
				exit;
			}
			
			//parse stats input file (load stats array)
			$this->_parseStatsFile();
		}

		$this->f_in = $this->argv[2];
		if (!file_exists( $this->f_in))
		{
			echo "ERROR: Training text file input ".$this->f_in." does not exists.";
			exit;
		}

		//parse training text input file (create stats array)
		$this->_parseTrainingFile();

		//sort and count totals
		$this->_formatStatsArray();

		//save statistics array
		$this->f_out = $this->argv[3];
		$this->_saveStatsArray();
	}

	function _generatePass()
	{
		if ( $this->argc < 4)
		{
			echo $this->_help();
			exit;
		}

		$this->f_in = $this->argv[2];
		if (!file_exists( $this->f_in))
		{
			echo "ERROR: Stats file input ".$this->f_in." does not exists.";
			exit;
		}

		//parse stats input file (load stats array)
		$this->_parseStatsFile();

		if ( isset($this->argv[4]))
			$this->LENGTH_PASS = (int)$this->argv[4];

		if ( isset($this->argv[5]))
			$this->N_PASS = (int)$this->argv[5];

		//calculate maximun syllable length
		$this->MAX_SYLLABLE = count( $this->stats_array);

		//generate passwords
		$this->_passwordList();

		//save passwords list
		$this->f_out = $this->argv[3];
		$this->_savePassList();
	}

	function _talk()
	{
		if ( $this->argc < 4)
		{
			echo $this->_help();
			exit;
		}

		$this->f_in = $this->argv[2];
		if (!file_exists( $this->f_in))
		{
			echo "ERROR: Stats file input ".$this->f_in." does not exists.";
			exit;
		}

		//parse stats input file (load stats array)
		$this->_parseStatsFile();

		if ( isset($this->argv[4]))
			$this->MAX_WORDS = (int)$this->argv[4];


		//calculate maximun syllable length
		$this->MAX_SYLLABLE = count( $this->stats_array);

		//generate speech
		$this->_speech();

		//save speech
		$this->f_out = $this->argv[3];
		$this->_saveSpeech();
	}

	function _dialog()
	{
		if ( $this->argc < 3)
		{
			echo $this->_help();
			exit;
		}

		$this->f_in = $this->argv[2];
		if (!file_exists( $this->f_in))
		{
			echo "ERROR: Stats file input ".$this->f_in." does not exists.";
			exit;
		}

		//parse stats input file (load stats array)
		$this->_parseStatsFile();

		echo "\n'.' to exit.\n";

		$_end = false;
		while (!$_end)
		{
			echo "H> ";
			$input = $this->_getInput();
			switch( $input)
			{
				case '.':
					$_end = true;
					break;
				case '.save':
					$this->f_out = $this->f_in;
					$this->_saveStatsArray();
					echo "\n Stats array saved...\n";
					break;
				default:
					$this->_parsePhrase( $input);
					$this->MAX_WORDS = 10;
					echo 'TM> '.$this->_speech();
					echo $this->speech."\n";
			}
		}		
	}

	function _getInput($length='255')
	{
		if (!isset ($GLOBALS['StdinPointer']))
			$GLOBALS['StdinPointer'] = fopen ("php://stdin","r");

		$line = fgets ($GLOBALS['StdinPointer'],$length);

		return trim( $line);
	}


	function _parsePhrase( $_input)
	{
		for( $i=0; $i< $this->WEIGHT_USER; $i++)
		{
			$syllable = '';
			$c_old = '';

			for( $ii=0; $ii<strlen( $_input); $ii++)
			{
				$c = $_input{$ii};
				if( strpos( $this->charset, $c)===false)
				{
					//$syllable = '';
					$this->charset .= $c;
				}

				if ( strlen( $syllable) < $this->MAX_SYLLABLE)
					$syllable .= $c;
				else
				{
					$this->_addMatch( $c_old);
					$syllable = $c_old.$c;
				}
				$this->_addMatch( $syllable);
				$c_old = $c;
			}
		}

		$this->_formatStatsArray();
	}

	function _parseTrainingFile()
	{
		$this->hnd_in = @fopen( $this->f_in, "r");
		if ($this->hnd_in == false)
		{
			echo "ERROR: File input ".$this->f_in." can't be opened for reading.";
			exit;
		}

		$fsz = filesize(  $this->f_in);
		$n_chars = 0;

		$syllable = '';
		$c_old = '';

		while( ( $c = fgetc( $this->hnd_in)) != false)
		{
			$n_chars++;
			echo "Parsing ".$n_chars." of ".$fsz." bytes training file...\r";

			if (( $c=="\n") || ($c=="\r") || ($c=="\""))
			{
				$syllable = '';
			}
			else
			{
				if (( strpos( $this->charset, $c)===false))
				{
					//$syllable = '';
					$this->charset .= $c;
				}

				if ( strlen( $syllable) < $this->MAX_SYLLABLE)
					$syllable .= $c;
				else
				{
					$this->_addMatch( $c_old);
					$syllable = $c_old.$c;
				}
				$this->_addMatch( $syllable);
				$c_old = $c;
			}
		}

		fclose( $this->hnd_in);
	}

	function _addMatch( $_syllable)
	{
		$l = strlen( $_syllable);
		$prefix = substr( $_syllable, 0, $l-1);
		$end = substr( $_syllable, $l-1);

		if ( !isset( $this->stats_array[$l][$prefix][$end]))
			$this->stats_array[$l][$prefix][$end] = 1;
		else
			$this->stats_array[$l][$prefix][$end]++;

		for($i=0;$i<strlen($this->charset);$i++)
		{
			$v = $this->charset{$i};
			if ( $v != $end)
				if ( isset( $this->stats_array[$l][$prefix][$v]))
				{
					if ( $this->stats_array[$l][$prefix][$v] >= $this->stats_array[$l][$prefix][$end])
						$this->stats_array[$l][$prefix][$v]++;
				}
		}
	}

	function _formatStatsArray()
	{
		//First, sorts the array from min to max counts on all syllable lengths
		for( $n=1; $n<=$this->MAX_SYLLABLE; $n++)
		{
			foreach( $this->stats_array[$n] as $prefix => $ends_array)
			{
				//sort ends_array
				asort( $ends_array);
				$this->stats_array[$n][$prefix] = $ends_array;
			}
		}

		//Second, maximun counts on all syllable lengths

		for( $n=1; $n<=$this->MAX_SYLLABLE; $n++)
			foreach( $this->stats_array[$n] as $k2 => $v2)
			{
				$this->stats_array[$n][$k2]['max_count'] = 0;
				foreach( $this->stats_array[$n][$k2] as $k3 => $v3)
				{
					if ( $k3 != 'max_count')
					{
						if ( $v3 >= $this->stats_array[$n][$k2]['max_count'])
							$this->stats_array[$n][$k2]['max_count'] = $v3;
					}
				}
			 }
	}

	function _saveStatsArray()
	{
		$this->hnd_out = @fopen( $this->f_out, "w");
		if ($this->hnd_out == false)
		{
			echo "ERROR: File output ".$this->f_out." can't be created for writing.";
			exit;
		}
		fwrite( $this->hnd_out, $this->_p_array($this->stats_array));
		fclose( $this->hnd_out);
	}

	//Function adapted, original by MaierMan, http://www.zend.com/code/search_code_author.php?author=MaierMan
	function _p_array($_array)
	{ 
		$constructor = "Array("; 

		while( list( $key, $value) = each( $_array)) 
		{ 
			// Insert key 
			if ( is_int( $key)) 
				$constructor .= (string)$key."=>"; 
			else 
				$constructor .= "'".addslashes( $key)."'=>"; 

			//Insert value
			if ( is_string( $value)) 
				$constructor.="'".addslashes( $value)."',"; 
			elseif ( is_int( $value))
				$constructor.= (string)$value.","; 
			// Array (recurse) 
			elseif ( is_array( $value)) 
				$constructor .= $this->_p_array($value).","; 
		} 
         
		// Finish constructor 
		$constructor = substr($constructor, 0, -1) . ")"; 

		return $constructor;     
	}

	function _parseStatsFile()
	{
		$this->hnd_in = @fopen( $this->f_in, "r");
		if ($this->hnd_in == false)
		{
			echo "ERROR: File input ".$this->f_in." can't be opened for reading.";
			exit;
		}

		$array = fgets( $this->hnd_in);

		eval('$this->stats_array='.$array.';');
		fclose( $this->hnd_in);

		if (!$this->_validStatsArray( $this->stats_array))
		{
			echo "ERROR: Stats file input ".$this->f_in." is not a valid php stats array.\n";
			exit;
		}
	}

	function _validStatsArray( $_stats_arr)
	{
		return is_array( $_stats_arr);
	}

	function _passwordList()
	{
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

		for( $i=0; $i<$this->N_PASS; $i++)
		{
			echo "Generating ".$i." passwords of ".$this->N_PASS."...\r";
			$syllable = '';
			$password = '';
			$n = 0;
			$c_old = '';
			for( $ii=0; $ii<$this->LENGTH_PASS; $ii++)
			{
				if ($n == $this->MAX_SYLLABLE)
				{
					$syllable = $c_old;
					$n = 2;
				}
				else
					$n++;

				if ( !isset( $this->stats_array[$n][$syllable]))
				{
					$syllable = '';
					$n = 1;
				}

				//Verifies if this syllable has as predecessors only characters excluded for passwords
				$useful = false;
				foreach ( $this->stats_array[$n][$syllable] as $k => $v)
				{
					if ( ( strpos( $this->no_pass, $k) === false) && ( $k != 'max_count'))
					{
						$useful = true;
						break;
					}
				}
				if (!$useful)
				{
					$syllable = '';
					$n = 1;
				}

				$found = false;
				while (	!$found)
				{
					//echo $dice.' '.$syllable;
					$dice = $this->_getRand( 0, $this->stats_array[$n][$syllable]['max_count']-1);
					foreach( $this->stats_array[$n][$syllable] as $k => $v)
						if (( $k != 'max_count')&&(strpos( $this->no_pass, $k) === false))
							if ( $dice < $v)
							{
								$password .= $k;
								$syllable .= $k;
								$c_old = $k;
								$found = true;
								break;
							}
				}
			}

			$this->list_pass[] = $password;
		}
	}

	function _savePassList()
	{
		$this->hnd_out = @fopen( $this->f_out, "w");
		if ($this->hnd_out == false)
		{
			echo "ERROR: File output ".$this->f_out." can't be created for writing.";
			exit;
		}
		for( $i=0; $i < $this->N_PASS; $i++)
			fwrite( $this->hnd_out, $this->list_pass[$i]."\n");

		fclose( $this->hnd_out);
	}

	function _speech()
	{
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

		$syllable = '';
		$this->speech = '';
		$n = 0;
		$c_old = '';

		$n_words = 0;
		while( $n_words < $this->MAX_WORDS)
		{
			echo "Generating speech of ".$n_words." words of ".$this->MAX_WORDS."...\r";

			if ($n == $this->MAX_SYLLABLE)
			{
				$syllable = $c_old;
				$n = 2;
			}
			else
				$n++;

			if ( !isset( $this->stats_array[$n][$syllable]))
			{
				$syllable = '';
				$n = 1;
			}
			$dice = $this->_getRand( 0, $this->stats_array[$n][$syllable]['max_count']-1);
			foreach( $this->stats_array[$n][$syllable] as $k => $v)
				if ( $k != 'max_count')
					if ( $dice < $v)
					{
						$this->speech .= $k;
						$syllable .= $k;
						$c_old = $k;
						break;
					}
			if ( $c_old == ' ')
				$n_words++;
		}
	}

	function _getRand( $_min, $_max)
	{
		return mt_rand( $_min, $_max);
	}

	function _saveSpeech()
	{
		$this->hnd_out = @fopen( $this->f_out, "w");
		if ($this->hnd_out == false)
		{
			echo "ERROR: File output ".$this->f_out." can't be created for writing.";
			exit;
		}

		fwrite( $this->hnd_out, $this->speech."\n");

		fclose( $this->hnd_out);
	}

	function setCharset( $_cs, $_np)
	{
		$this->charset = $_cs;
		$this->no_pass = $_np;
	}
}

$_ge = new  talkingMachine( $argc, $argv);

?>