<?php
class users {
	
	private $whitelist = "index";
	private $secure_folder = "nm-admin";
	
	//Constructor deals with session management
	function __construct() {
		session_start();
		$this->check();
		//restrict users from seeing other networks
		if(isset($_GET['n']) && !Model::loadProxy("network")->validate($_GET['n'])) throw new Exception("Network not found!");
	}
	
	function check() {
		$current = $_SERVER['SCRIPT_FILENAME'];
		if(strpos($current,$this->secure_folder) !== false) {
			$whitelist = explode(",",$this->whitelist);
			$flag = true; //assume we have to check
			foreach($whitelist as $page) {
				if(strpos($current,$page.".php")) { $flag = false; break; }
			}
			if($flag && !isset($_SESSION['id'])) {
				Model::direct("index.php");
				die();
			}
		}
	}
	
	function login($user,$pass) {
		$q = db::init()->query("users.login",array("user"=>$user,"pass"=>$pass));
		if($q->rowCount()) {
			$data = $q->fetch(PDO::FETCH_NUM);
			$_SESSION['id'] = $data[0];
			$_SESSION['name'] = $user;
		}
		return ($q->rowCount());
	}
	
	function create($name,$pass,$network) {
		$id = self::find($name);
		if($id === false) {
			db::init()->query("users.add",array("name"=>$name,"pass"=>$pass));
			$id = db::init()->lastInsertId();
		}
		Model::loadProxy("usernetwork")->link($id,$network);
		
	}
	
	public function remove($id) {
		db::init()->query("users.remove",array("id"=>$id));
	}
	
	public function show($network) {
		$q = db::init()->query("users.list",array("n"=>$network,"user"=>$_SESSION['id']));
		if($q->rowCount()) {
			while($row = $q->fetch(PDO::FETCH_ASSOC)) {
				echo "<tr><td>".$row['userName']."</td>";
				echo "<td><a href='nm-user-manage.php?action=delete&u={$row['userID']}&n=$network'>
				<img src='images/cross.png'/></a></td></tr>";
			}
		} else {
			echo "<tr><td colspan='2'>No users found!</td></tr>";
		}
	}
	
	static function find($user,$hash=false) {
		$name = ($hash) ? "users.hashfind" : "users.find";
		$q = db::init()->query($name,array("name"=>$user));
		if($q->rowCount()) {
			$data = $q->fetch(PDO::FETCH_NUM);
			return $data[0];	
		}
		return false;
	}
}
?>