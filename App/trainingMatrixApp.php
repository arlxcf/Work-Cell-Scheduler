<?php
// TrainingMatrix Copyright 2014 by WebIS Spring 2014 License Apache 2.0
namespace WCS;
require_once 'Work-Cell-Scheduler/Config/global.php';
require_once 'Work-Cell-Scheduler/App/trainingApp2.php';
class TrainingMatrix{

	/**
	 * Database handle
	 * @var \mysqli
	 */
	private $db=NULL;
	/**
	 * Training statement
	 * @var \mysqli_stmt
	 */
	private $training_stmt=NULL;
	/**
	 * Training CRW that the staement binds to.
	 * @var unknown
	 */
	private $training_worker;
	private $training_subcell;
	private $training_training;

	function __construct(){
		$this->db = @new \mysqli(\WCS\Config::$dbhost,\WCS\Config::$dbuser,\WCS\Config::$dbpassword,\WCS\Config::$dbdatabase);
		if($this->db->connect_error){
			throw new \Exception("Error unable to connect to database: ".$this->db->connect_error);
		}
	}

	public function getWorkers() {
		$stmt=$this->db->prepare("SELECT DISTINCT worker FROM TrainingMatrix ORDER BY worker");
		if($stmt===FALSE){
			die("WCS/TrainingMatrix.getWorkers> prepare:".$this->db->error);
			return FALSE;
		}
		if($stmt->bind_result($worker)===FALSE){
			die("WCS/TrainingMatrix.getWorkers> bind:".$this->db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("WCS/TrainingMatrix.getWorkers> execute:".$this->db->error);
			return FALSE;
		}
		$workers=array();
		while($stmt->fetch()){
			$workers[]=$worker;
		}
		return $workers;
	}

	public function getSubcells() {
		$stmt=$this->db->prepare("SELECT DISTINCT subcell FROM TrainingMatrix ORDER BY subcell");
		if($stmt===FALSE){
			die("WCS/TrainingMatrix.getSubcells> prepare:".$this->db->error);
			return FALSE;
		}
		if($stmt->bind_result($subcell)===FALSE){
			die("WCS/TrainingMatrix.getSubcells> bind:".$this->db->error);
			return FALSE;
		}
		if($stmt->execute()===FALSE){
			die("WCS/TrainingMatrix.getSubcells> execute:".$this->db->error);
			return FALSE;
		}
		$subcells=array();
		while($stmt->fetch()){
			$subcells[]=$subcell;
		}
		return $subcells;
	}

	function training(){
		if($this->training_stmt!=NULL){
			return $this->training_stmt;
		}
		$stmt=$this->db->prepare("SELECT training FROM TrainingMatrix WHERE worker=? AND subcell=?");
		if($stmt===FALSE){
			die("WCS/TrainingMatrix.training> prepare:".$this->db->error);
			return FALSE;
		}
		if($stmt->bind_param('ss',$this->training_worker,$this->training_subcell)===FALSE){
			die("WCS/TrainingMatrix.training> bind_param:".$this->db->error);
			return FALSE;
		}
		if($stmt->bind_result($this->training_training)===FALSE){
			die("WCS/TrainingMatrix.training> bind_result:".$this->db->error);
			return FALSE;
		}
		$this->training_stmt=$stmt;
		return $stmt;
	}

	public function getTraining($worker,$subcell){
		$this->training_worker=$worker;
		$this->training_subcell=$subcell;
		$stmt=$this->training();
		if($stmt->execute()===FALSE){
			die("WCS/TrainingMatrix.getTraining> execute:".$this->db->error);
			return FALSE;
		}
		if($stmt->fetch()){
			return $this->training_training;
		}
		return 0;
	}

	function __destruct() {
		if($this->db!=NULL){
			$this->db->close();
		}
	}

}

?>
