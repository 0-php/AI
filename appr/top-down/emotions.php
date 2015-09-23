<?php

include 'emotion.php';

class Emotions { //By Formula of Pavel Simonov (soviet psychophysiologist)

	var $needs

	var $emotions = {
		'happiness',
	}

	function Emotions($needs){
		foreach ($needs as $need) {
			if($need in $emotions['needs']){
				$emotion_idx = count($emotions);
				$emotion = $emotions[$emotion_idx] = new Emotion();
				$emotion.generate_by_needs();
				$emotion.affect_physiology();
			}
		}
	}

}

?>