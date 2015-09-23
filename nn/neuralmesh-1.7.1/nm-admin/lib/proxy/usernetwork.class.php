<?php
class usernetwork {
	public function link($user,$network) {
		db::init()->query("users.link",array("user"=>$user,"network"=>$network));
	}
	
	public function delink($user,$network) {
		db::init()->query("users.delink",array("user"=>$user,"network"=>$network));
		$q = db::init()->query("users.linked",array("user"=>$user));
		$data = $q->fetch(PDO::FETCH_NUM);
		return $data[0];
	}
}