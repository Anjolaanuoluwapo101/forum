<?php

//$globals = $GLOBALS['ini_config'] ;
//print_r($globals);
class ConnectDb {
	private static $instance = null;
	private $conn;
	private $host = "localhost";
	private $user= "root";
	private $pass= "";
	private $name=  "forumDB";
	private function __construct() {
		$this->conn = new PDO("mysql:host={$this->host};dbname={$this->name}", $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",PDO::ATTR_PERSISTENT => true));
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->conn->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL);
	}
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new ConnectDb();
		//	echo "New connection estasblished"."<br>";
		}
	//	echo "Connection still open";
		return self::$instance;
	}
	public function getConnection() {
		return $this->conn;
	}
}
	$instance = ConnectDb::getInstance();
	$dbh = $instance->getConnection();

?>