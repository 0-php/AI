<?php
class train {
	function get($id) {
		$q = db::init()->query("pattern.getSet",array("id"=>$id));
		if(!$q->rowCount()) throw new Exception("Training set not found.");
		$data = array();
		while($row = $q->fetch(PDO::FETCH_ASSOC)) $data[] = $row;
		return $data;
	}
	
    function listTrainingSets($id) {
		$q = db::init()->query("train.getAll",array("id"=>$id));
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				$tid = $row['trainsetID'];
				echo "<tr><td>".$row['label']."</a></td>";
				echo "<td><a href='nm-edit-set.php?s=$tid&n=$id' title='Manage'><img src='images/pencil.png' /></a> ";
				echo "<a href='nm-manage-set.php?action=delete&s=$tid' title='Delete'><img src='images/cross.png' /></a> ";
				echo "<a href='nm-run-set.php?s=$tid' title='Run Training Set'><img src='images/run.png' /></a> ";
				echo "<a href='nm-set-history.php?s=$tid&n=$id' title='View History'><img src='images/time.png' /></a></td></tr>";
			}
		} else {
			echo "<tr><td colspan='2'><span>No Training Sets</span></td></tr>";
		}
	}
	
	function getID($tid) {
		$q = db::init()->query("train.get",array("id"=>$tid));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		return $data['networkID'];
	}
	
	public function validate($id) {
		$q = db::init()->query("train.validate",array("id"=>$id,"user"=>$_SESSION['id']));
		return !!$q->rowCount(); //convert to boolean
	}
	
}
?>