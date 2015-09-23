<?php

class NN_Batch {

	var $opts;
	var $nns = array();
	var $nn_opts = array();

	function NN_Batch($opts){
		if(is_string($opts))
			$this->opts = $opts = (array) json_decode($opts);
		
		$this->getNNClassName($opts['type']);
		include(NN_CLASSES_PATH.$this->className.".class.php");
		echo "Neural network type: ".$this->typeName."<br>";
		
		if(isset($opts['path']))
			$nn_opts['path'] = $opts['path'];
		if(isset($opts['times']) && $opts['times'] > 1){
			for($i=1; $i<=$opts['times']; $i++){
				$nn_opts['show_info'] = FALSE;
				$this->nns[] = new $this->className($nn_opts);
			}
		} else {
			$this->opts['times'] = 1;
			$this->nns[] = new $this->className($nn_opts);
		}
		if(isset($opts['accuracy']))
			$this->accuracy = $opts['accuracy'];
		else
			$this->accuracy = 99.5;
		$this->displayResults();
	}
	
	function displayResults(){
		if(isset($this->nns[0]->RMSerror_history))
			$this->displayResultsRMS();
		else
			$this->displayResultsHamming();
	}
	
	function displayResultsHamming(){
		$nn = $this->nns[0];
		$outputs = $nn->output_history;
		$outputs_count = count($outputs)-1;
		//echo "Random pattern index: <b>".$nn->randomPatternIndex."</b><br>";
		echo "Answer: <font size=5px>".$nn->answer."</font><br>";
		echo "Loops: <b>".$outputs_count."</b><br><br><br>";
		for($i=1; $i<=$outputs_count; $i++){
			echo "Loop <b>".$i."</b><br>";
			for($j=0; $j<=count($outputs[$i])-1; $j++){
				if($outputs[$i][$j] != 0)
					echo $j.": ".$outputs[$i][$j]."<br>";
			}
			echo "<br>";
		}
	}
	
	function displayResultsRMS(){
		$nns_count = count($this->nns);
		if($nns_count > 10){
			$accurates = 0;
			for($i=0; $i<=$nns_count-1; $i++){
				$last_rms_history = array_pop($this->nns[$i]->RMSerror_history);
				$RMSerror = $last_rms_history['rmserror'];
				if($this->is_accurate($RMSerror))
					$accurates++;
			}
			$fraction = $nns_count / $accurates;
			$percent = ($accurates / $nns_count) * 100;
			if($fraction == 1)
				$color = '#00BF00';
			elseif($fraction > 0.8)
				$color = '#8BBF00';
			elseif($fraction > 0.5)
				$color = '#BFBF00';
			elseif($fraction > 0.2)
				$color = '#BF7300';
			elseif($fraction > 0)
				$color = '#BF3F00';
			else
				$color = '#BF0000';
			echo "<div style='color:$color; font-size:18px' align='center'>
				<span style='font-size:30px'><b>".round($percent, 2)." %</b></span><br>
				<b>".$accurates."</b> / ".$nns_count."<div>";
		} else {
			for($i=0; $i<=$nns_count-1; $i++){
				$last_rms_history = array_pop($this->nns[$i]->RMSerror_history);
				$RMSerror = $last_rms_history['rmserror'];
				if($this->is_accurate($RMSerror) == TRUE)
					$status = "<font color='green'><b>SUCCESS!</b></font>";
				else
					$status = "<font color='red'><b>FAIL</b></font>";
				echo "Net <b>".($i + 1)."</b>: ".$status."<br>RMS Error: ".$RMSerror."<br>";
			}
		}
	}
	
	function is_accurate($RMSError){
		if((1 - $RMSError) * 100 > $this->accuracy)
			return TRUE;
		else
			return FALSE;
	}
	
	function getNNClassName($type){
		switch($type){
			case 'mlp':
				$this->className = 'MultiLayerPerceptron';
				$this->typeName = 'Multi-Layer Perceptron';
				break;
			case 'hamming':
				$this->className = 'Hamming';
				$this->typeName = 'Hamming Neural Network with MAXNET network';
		}
	}

}

?>