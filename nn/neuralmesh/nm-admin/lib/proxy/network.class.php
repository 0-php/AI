<?php
class network {

	public $nn; //network object
    
    function get($id) {
		$q = db::init()->query("networks.get",array("id"=>$id,"user"=>$_SESSION['id']));
		if(!$q->rowCount()) throw new Exception("Network not found.");
		$data = $q->fetch(PDO::FETCH_ASSOC);
		$this->nn = $this->decode($data['snapshot']);
		unset($data['snapshot']);
		return $data;
	}
	
	function getAuth($auth) {
		$q = db::init()->query("networks.auth",array("auth"=>$auth));
		if(!$q->rowCount()) throw new Exception("Authkey invalid.");
		$data = $q->fetch(PDO::FETCH_ASSOC);
		$this->nn = $this->decode($data['snapshot']);
		unset($data['snapshot']);
		return $data;
	}
	
	function listNetworks() {
		$q = db::init()->query("networks.getAll",array("id"=>$_SESSION['id']));
		$output = "";
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				$id = $row['networkID'];
				$output .= "<tr><td><a href='nm-network.php?n=".$id."' title='Manage Network'>".$row['networkName']."</a></td>";
				$output .= "<td><a href='nm-edit-network.php?n=$id' title='Settings'><img src='images/cog.png' /></a> ";
				$output .= "<a href='nm-manage-network.php?action=".Model::$NETWORK."&n=$id' title='Delete Network'><img src='images/cross.png' /></a>";
				$output .= "</td></tr>";
			}
		} else {
			$output .= "<tr><td colspan='2'><span>No Networks!</td></tr>";
		}
		return $output;
	}
	
	function add($name,$authkey,$type,$lr="DEFAULT",$momentum="DEFAULT",$mse=NULL,$epoch=NULL) {
		db::init()->query("networks.add",array("label"=>$name, "lr"=>$lr, "mse"=>$mse,
										  "epoch"=>$epoch, "rate"=>$momentum, 
										  "key"=>$authkey,"type"=>$type));
		return db::init()->lastInsertId();
	}
	
	/**
	 * Serialized a network and compresses if available
	 * @param $nn
	 * @return serialized string
	 */
	private function encode($nn) {
		if(function_exists("gzcompress")) {
			return /*gzcompress(*/serialize($nn)/*)*/; // MySQL not that want gzcompressed data
		}
		return serialize($nn);
	}
	
	/**
	 * Takes serialized string and decodes it
	 * @param $nn
	 * @return unknown_type
	 */
	private function decode($nn) {
		if(function_exists("gzuncompress")) {
			return unserialize(/*gzuncompress(*/$nn/*)*/); // MySQL not that want gzcompressed data
		}
		return unserialize($nn);
	}
	
	function save($nn,$id) {
		$data = $this->encode($nn);
		if(strlen($data)/1024 > 1024) { //if data is greater than 1mb
			echo "More than 1MB<br>";
			$chunks = str_split($data,500);
			foreach($chunks as $chunk)
				db::init()->query("networks.stream",array("chunk"=>$chunk,"id"=>$id));
		} else {
			db::init()->query("networks.update",array("data"=>$data,"id"=>$id));
		}
	}
	
	function updateNetwork($id,$name,$lr,$mse,$epoch,$momentum) {
		db::init()->query("networks.updateAll",array("id"=>$id,
											 "label"=>$name,
											 "lr"=>$lr,
											 "mse"=>$mse,
											 "epoch"=>$epoch,
											 "momentum"=>$momentum));
	}
	
	public function validate($id) {
		$q = db::init()->query("networks.validate",array("id"=>$id,"user"=>$_SESSION['id']));
		return ($q->rowCount());
	}
	
	function getStats($user) {
		$q = db::init()->query("networks.stats", array("id"=>$user));
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	function delete($id) {
		db::init()->query("networks.remove",array("id"=>$id));
	}
	
	function buildTree($id,$nn) {
		$uuid = 0;
		$output = "<ul>";
		$lcount = count($nn->layer);
		for($l=0;$l<$lcount;$l++) {
			$output .= "<li class='layer'><a href='javascript:void(0);' onclick=\"toggle('ul".($uuid+1)."',this);\">-</a> Layer ".($l+1);
			$ncount = count($nn->layer[$l]->neuron); 
			for($n=0;$n<$ncount;$n++) {
				$uuid++;
				if($n==0) $output .= "<ul id='ul$uuid'>";
				$output .= "<li class='neuron'><a href='javascript:void(0);' onclick=\"toggle('ul".($uuid+1)."',this);\">+</a> Neuron ".($n+1);
				$scount = count($nn->layer[$l]->neuron[$n]->synapse);
				for($s=0;$s<$scount;$s++) {
					$uuid++;
					if($s==0) $output .= "<ul id='ul$uuid' style='display:none'>";
					$output .= "<li class='synapse'>Synapse ".($s+1)."</li>";
					if($s==$scount-1) $output .= "</ul>";
					
				}
				$output .= "</li>";
				if($n==$ncount-1) $output .= "</ul>";
			}
			$output .= "</li>";
		}
		$output .= "</ul>";
		db::init()->query("cache.save",array("id"=>$id."tree","network"=>$id,"data"=>$output));
		return $output;
	}
}
?>