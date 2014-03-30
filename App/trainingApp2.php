<?php
// TrainingMatrix Copyright 2014 by WebIS Spring 2014 License Apache 2.0
namespace WCS;
require_once 'Work-Cell-Scheduler/Config/global.php';

class TrainingApp {
	private $training=NULL;
	private $subcell=NULL;
	private $worker=NULL;

	function add(Training $training){
		$this->training=$training;
		return TRUE;
	}

	function process($page){
		$this->load();
		$this->save();
		echo $this->edit($page);
	}

	function get(){
		if($this->training===NULL){
			$this->training=new Training();
		}
		if(!$this->training->setTraining($_REQUEST['training'])){
			//print ":PersonApp.process: unable to set person |".$_REQUEST['person']."|";
			return FALSE;
		}
		if(isset($_REQUEST['subcell']) and !$this->training->setSubcell($_REQUEST['subcell'])){
			//print ":PersonApp.process: unable to set person |".$_REQUEST['name']."|");
			return FALSE;
		}
		if(isset($_REQUEST['worker']) and !$this->training->setWorker($_REQUEST['worker'])){
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
		if($this->training->read()===FALSE){
			return FALSE;
		}
		return TRUE;
	}

	function save(){
		if($this->training===NULL){
			$this->training=new Training();
		}
		if(!isset($_REQUEST['action'])){
			return FALSE;
		}
		if($_REQUEST['action']!='Add/Edit'){
			return FALSE;
		}
		$this->get();
		if($this->training->delete()===FALSE){
			print ":TrainingApp.save: unable to delete()";
			return FALSE;
		}
		if($this->training->write()===FALSE){
			print ":TrainingApp.save: unable to write()";
			return FALSE;
		}
		return TRUE;
	}

	function edit($action){
		$worker=htmlspecialchars($this->training->getWorker());
		$subcell=htmlspecialchars($this->training->getSubcell());
		$training=htmlspecialchars($this->training->getTraining());
		return <<<HTML
		<form action="$action" method="GET">
		<table border="1">
		  <tr><td>Worker</td>  <td><input type="text" name="worker"   value="$worker"></td></tr>
    	  <tr><td>Subcell</td>    <td><input type="text" name="subcell"     value="$subcell"></td></tr>
    	  <tr><td>Training</td><td><input type="text" name="training" value="$training"></td></tr>
    	</table>
		<input type="submit" name="action" value="Add/Edit">
		</form>
HTML;
	}
}

class Training {
	private $worker=NULL;
	private $subcell=NULL;
	private $training=NULL;

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
		$str="{training: $this->training";
		if(!is_null($this->training)){
			$str.=" training: $this->training";
		}
		return $str.'}';
	}

	/**
	 * Set worker
	 * @param string $worker
	 * @return bool worker set.
	 */
	function setWorker($worker){
		if(preg_match('/^[a-zA-Z0-9]+$/',$worker)){
			$this->worker=$worker;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set subcell 
	 * @param integer $subcell of Worker.
	 */
	function setSubcell($subcell){
		if(preg_match('/^\D+$/',$subcell)){
			return FALSE;
		}
		$this->subcell=$subcell;
		return TRUE;
	}
	
	/**
	 * set training
	 * @param double $training of worker in subcess
	 */
	function setTraining($training){
		if(preg_match('/^-?(?:\d+|\d*\.\d+)$/',$training)){
			$this->training=$training;
			return TRUE;
		}
		return FALSE;
	}

	function getWorker(){
		return $this->worker;
	}

	function getSubcell(){
		return $this->subcell;
	}
	function getTraining(){
		return $this->training;
	}

	function write(){
		$stmt=self::$db->prepare("INSERT INTO TrainingMatrix (worker,subcell,training) VALUES (?,?,?)");
		if($stmt===FALSE){
			die("Training.write: unable to create statement " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param("sid",$this->worker,$this->subcell,$this->training)===FALSE){
			die("Training.write: unable to bind " . self::$db->error);
		}
		if($stmt->execute()===FALSE){
			if($stmt->errno==1062){ // Duplicate Entry
				$stmt->close();
				self::$db->close();
				return FALSE;
			}
			die("Training.write: unable to execute". self::$db->errno. self::$db->error);
			return FALSE;
		}
		$stmt->close();
		return TRUE;
	}

	/**
	 * Remove Training
	 * @return bool TRUE on success (even if record did not exist);
	 */
	function delete(){
		$stmt=self::$db->prepare("DELETE FROM TrainingMatrix WHERE worker=? AND subcell=?");
		if($stmt===FALSE){
			die("WCS/Training.delete> stmt:".self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param('si',$this->worker,$this->subcell)===FALSE){
			die("WCS/Training.delete> bind_param:".self::$db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("WCS/Training.delete> execute:".self::$db->errno." ".self::$db->error);
			return FALSE;
		}
		return TRUE;
	}

	function read() {
		$stmt=self::$db->prepare("SELECT training FROM TrainingMatrix WHERE worker=? AND subcell=?");
		if($stmt===FALSE){
			die("Training.get: unable to create statement " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_param("si",$this->worker,$this->subcell)===FALSE){
			die("Training.get: unable to bind_param " . self::$db->error);
			return FALSE;
		}
		if($stmt->bind_result($this->training)===FALSE){
			die("Worker.get: unable to bind_result " . self::$db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("Training.get: unable to execute self::$db->errno self::$db->error");
			return FALSE;
		}
		if($stmt->fetch()==FALSE){
			$stmt->close();
			return FALSE;
		}
		return TRUE;
	}

}


?>
