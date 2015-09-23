<?php
class Model {

	public static $OUTPUT = 0;
	public static $LAYER = 1;
	public static $INPUT = 2;
	public static $NEURON = 3;
	public static $NETWORK = 4;
	public static $SET = 5;
	public static $PATTERN = 6;
	public static $library = array(); //hold the plugins

	function __construct() {
		// Plugins should be imported here:
		require_once(Controller::$root."proxy/navigation.class.php");
		require_once(Controller::$root."proxy/validation.class.php");
		require_once(Controller::$root."proxy/db.class.php");
		// But not past here :)
		
		$this->get("users");
		$this->nav = new Navigation;
		$this->assets = array("global.css");
		$this->val = new validation;
		$this->year = date("Y");
		
		if(isset($_SESSION['name'])) $this->user = $_SESSION['name'];
	}
	
	/**
	 * Load a proxy object from the library, or add it if doesn't
	 * exist already.
	 * @param $name name of object
	 * @return Proxy object
	 */
	public static function loadProxy($name) {
		if(!isset(self::$library[$name])) {
			require Controller::$root.'proxy/'.$name.".class.php";
			self::$library[$name] = new $name;
		}
		return self::$library[$name];
	}
	
	/**
	 * Non-static wrapper for loadProxy method
	 * @param $name name of object
	 * @return Proxy object
	 */
	public function get($name) {
		return Model::loadProxy($name);
	}
	
	/**
	 * Static redirector. If no URL provided, send to referer
	 * @param $url
	 */
	static function direct($url=null) {
		if($url === null) $url = $_SERVER['HTTP_REFERER'];
		header("Location: ".$url);
	}
	
	/**
	 * Echo links to assets
	 * @return unknown_type
	 */
	function loadAssets() {
		$output = "";
		foreach($this->assets as $file) {
			$ext = substr($file, strrpos($file, '.') + 1);
			if($ext == "js") {
				$output .= "<script type='text/javascript' src='assets/$file'></script>\n";
			} else if($ext == "css") {
				$output .= "<link href='assets/$file' type='text/css' rel='stylesheet' />\n";
			}
		}
		return $output;
	}
}
?>