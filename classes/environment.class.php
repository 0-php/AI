<?php
include('agent.class.php');

class Environment {
	$agents = array();
	$receptionField = 0; //Radius around agent, in which it can interact with other agents, 0 = infinity
	
	function Environment(){
	
	}

	function createAgents($num){
		for($i=0; $i<$num; $i++){
			$this->agents[] = new Agent();
		}
	}

}

?>