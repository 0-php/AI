<?php
class patterns {
	
	function listPatterns($id) {
		$q = db::init()->query("pattern.getAll",array("id"=>$id));
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				$id = $row['patternID'];
				echo "<tr><td>".$row['pattern']."</td><td><img src='images/arrow.gif'/></td><td>".$row['output']."</td>";
				echo "<td><a href='nm-manage-set.php?action=remove&s=$id'><img src='images/cross.png' /></a></td>";
			}
		} else {
			echo "<tr><td colspan='4'><span>No Patterns</span></td></tr>";
		}
	}
	
}