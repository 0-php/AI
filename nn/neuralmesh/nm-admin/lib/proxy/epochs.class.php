<?php
class epochs {
	function clearHistory($id,$user) {
		Model::loadProxy("network");
		if(network::validate($id))
			db::init()->query("epoch.clear",array("id"=>$_GET['n']));
	}
	
	function saveEpoch($nid,$epoch,$start_mse,$end_mse,$time,$sid=NULL) {
		db::init()->query("epoch.store",array("id"=>$nid,"iterations"=>$epoch,"startmse"=>$start_mse,"endmse"=>$end_mse,"time"=>$time,"train"=>$sid));
	}
	
	function listHistory($id) {
		$q = db::init()->query("epoch.get",array("id"=>$id));
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr><td>".$row['iterations']."</td>";
				echo "<td>".$row['startMSE']."</td>";
				echo "<td>".$row['endMSE']."</td>";
				echo "<td>".date("j/m/Y g:i:s a",strtotime($row['epochDate']))."</td>";
				echo "<td>".$row['execTime']."</td></tr>";
			}
		} else {
			echo "<tr><td colspan='5'><span>No History</span></td></tr>";
		}
	}
	
	function listAllHistory($id) {
		$q = db::init()->query("epoch.getall",array("id"=>$id));
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr><td>".$row['iterations']."</td>";
				echo "<td>".$row['startMSE']."</td>";
				echo "<td>".$row['endMSE']."</td>";
				echo "<td>".date("j/m/Y g:i:s a",strtotime($row['epochDate']))."</td>";
				echo "<td>".$row['execTime']."</td>";
				echo "<td>".($row['trainsetID'] == null ? "n" : "y")."</td></tr>";
			}
		} else {
			echo "<tr><td colspan='6'><span>No History</span></td></tr>";
		}
	}
}