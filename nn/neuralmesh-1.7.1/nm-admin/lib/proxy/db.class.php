<?php
class db extends PDO {
	
	private static $instance = null;
	private static $queries = array();
	
	public static function init() {
		if(self::$instance === null) 
			self::$instance = new db(DB_CONN,DB_USER,DB_PASS);
		return self::$instance;
	}

    /**
     * Create a DB connection
     * @param string $connectionString
     * @param string $username
     * @param string $password 
     */
    public function __construct($connectionString, $username=null, $password=null)
    {
        parent::__construct($connectionString, $username, $password);
        /**
         * This sets up PDO to throw exceptions on errors, instead of spewing
         * PHP warnings and errors, so we have something to catch() if or when
         * something goes wrong.
         */
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function query($name, array $parameters = array()) {
    	$meta = explode(".",$name);
		
		//Load from XML if query doesn't exist
		if(!isset(self::$queries[$name])) {
			$queriesXML = simplexml_load_file(Controller::$root."data/".$meta[0].".xml");
			foreach($queriesXML as $query) {
				$key = $meta[0].".".(string)$query['name'];
				self::$queries[$key] = (string)$query;
			}
		}
		
		$sql = self::$queries[$name] or die("Error: query <var>$name</var> is not found!");
    	
    	$statement = parent::prepare($sql);
        $statement->execute($parameters);
        return $statement;
    }
}
?>