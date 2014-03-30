<?php
// Person App Copyright 2014 by WebIS Spring 2014 License Apache 2.0
namespace WCS;
require_once 'Work-Cell-Scheduler/Config/global.php';

class WorkerApp {
	private $worker=NULL;
	
	function add(Worker $worker){
		$this->worker=$worker;
		return TRUE;
	}

	function process2($page){
		$this->load();
		$this->save();
		echo $this->edit($page);
	}
	
	function get(){
		if($this->worker===NULL){
			$this->worker=new Worker();
		}
		if(!$this->worker->setWorker($_REQUEST['worker'])){
			//print ":PersonApp.process: unable to set person |".$_REQUEST['person']."|");
			return FALSE;
		}
		if(isset($_REQUEST['name']) and !$this->worker->setName($_REQUEST['name'])){
			//print ":PersonApp.process: unable to set person |".$_REQUEST['name']."|");
			return FALSE;
		}
	}
	
	function load(){
		if(!isset($_REQUEST['action'])){
			return FALSE;
		}
		if($_REQUEST['action']!='Load'){
			return FALSE;
		}
		$this->get();
		if($this->worker->read()===FALSE){
			return FALSE;
		}
		return TRUE;
	}
	
	function save(){
		if($this->worker===NULL){
			$this->worker=new Worker();
		}
		if(!isset($_REQUEST['action'])){
			return FALSE;
		}
		if($_REQUEST['action']!='Update'){
			return FALSE;
		}
		$this->get();
		if($this->worker->delete()===FALSE){
			print ":WorkerApp.save: unable to delete()";
			return FALSE;
		}
		if($this->worker->write()===FALSE){
			print ":WorkerApp.save: unable to write()";
			return FALSE;
		}
		return TRUE;
	}
	
	function edit($action){
		$worker=htmlspecialchars($this->worker->getWorker());
		$name=htmlspecialchars($this->worker->getName());
		return <<<HTML
		<form action="$action" method="GET">
		<table border="1">
		  <tr><td>Worker</td><td><input type="text" name="worker" value="$worker"></td></tr>
    	  <tr><td>Name</td>  <td><input type="text" name="name"   value="$name"></td></tr>
    	</table>
		<input type="submit" name="action" value="Update">
		<input type="submit" name="action" value="Load">
		</form>
HTML;
	}
}


class Worker {
	private $worker=NULL;
	private $name=NULL;
	
	/**
	 * Database Handle
	 * @var \mysqli
	 */
	static $db=NULL;
	
	function __construct(){
		if(!is_null(self::$db)){
			return;
		}
		self::$db = @new \mysqli(\WCS\Config::$dbhost,\WCS\Config::$dbuser,\WCS\Config::$dbpassword,\WCS\Config::$dbdatabase);
		if(self::$db->connect_error){
			throw new \Exception("Error unable to connect to database: ".self::$db->connect_error);
		}
	}
	
	function display(){
		$str="{worker: $this->worker";
		if(!is_null($this->name)){
			$str.=" name: $this->name";
		}
		return $str.'}';
	}
	
	/**
	 * Set worker
	 * @param string $worker Alphanumeric username [a-zA-Z0-9]
	 * @return bool person set.
	 */
	function setWorker($worker){
		//print ":Person.setPerson: |$person|".gettype($person);
		if(preg_match('/^[a-zA-Z0-9]+$/',$worker)){
			$this->worker=$worker;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Set Worker name
	 * @param string $name of Worker.
	 */
	function setName($name){
		if(preg_match('/^\s*$/',$name)){
			return FALSE;
		}
		$this->name=$name;
		return TRUE;
	}

	function getName(){
		return $this->name;
	}
	
	function getWorker(){
		return $this->worker;
	}
	
	function write(){
		$stmt=self::$db->prepare("INSERT INTO Worker (worker,name) VALUES (?,?)");
		if($stmt===FALSE){
			die("Worker.write: unable to create statement " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param("ss",$this->worker,$this->name)===FALSE){
			die("Worker.write: unable to bind " . self::$db->error);
		}
		if($stmt->execute()===FALSE){
			if($stmt->errno==1062){ // Duplicate Entry
				$stmt->close();
				self::$db->close();
				return FALSE;
			}
			die("Worker.write: unable to execute self::$db->errno self::$db->error");
			return FALSE;
		}
		$stmt->close();
		return TRUE;
	}
	
	/**
	 * Remove Worker
	 * @return bool TRUE on success (even if record did not exist);
	 */
	function delete(){
		$stmt=self::$db->prepare("DELETE FROM Worker WHERE worker=?");
		if($stmt===FALSE){
			die("WCS/Worker.delete> stmt:".self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param('s',$this->worker)===FALSE){
			die("WCS/Worker.delete> bind_param:".self::$db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("WCS/Worker.delete> execute:".self::$db->errno." ".self::$db->error);
			return FALSE;
		}
		return TRUE;
	}
	
	function read() {
		$stmt=self::$db->prepare("SELECT name FROM Worker WHERE worker=?");
		if($stmt===FALSE){
			die("Worker.get: unable to create statement " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param("s",$this->worker)===FALSE){
			die("Worker.get: unable to bind_param " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_result($this->name)===FALSE){
			die("Worker.get: unable to bind_result " . self::$db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("Worker.get: unable to execute self::$db->errno self::$db->error");
			return FALSE;
		}
		if($stmt->fetch()==FALSE){
			$stmt->close();
			return FALSE;
		}
		// print "Person.get: ".$this->display();
		return TRUE;
	}
	
}

?>
