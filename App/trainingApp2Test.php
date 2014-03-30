<?php
// Person Test Copyright 2014 by WebIS Spring 2014 License Apache 2.0
require_once 'Work-Cell-Scheduler/TDD/validator.php';
include 'Work-Cell-Scheduler/Config/local.php';
require_once 'trainingApp2.php';

class TrainingApp2TestCase extends WebIS\Validator {

	protected static $__CLASS__=__CLASS__;

	function testTraining() {
		$p=new \WCS\Training();
		$this->assertTrue($p->setTraining("0.95"),"numbers");
		$this->assertFalse($p->setTraining("point nine five"),"text");
		$this->assertEquals("{training: 0.95 training: 0.95}",$p->display(),"display only training rating");
		$this->assertFalse($p->setTraining(".9xcccd"),"unvalid characters");
		$this->assertFalse($p->setTraining("  "),"blanks");
		$this->assertEquals("0.95",$p->getTraining());
		$this->assertTrue($p->setWorker("AndrewL"),"worker set");
		$this->assertTrue($p->setSubcell("1010"),"subcell set");
		// echo $p->display();
		$this->assertTrue($p->delete(),"cant delete");
		$this->assertTrue($p->write(),"cant write");
		$this->assertTrue($p->read(),"Cannot read training from database");
		$this->assertEquals("0.95",$p->getTraining(),"0.95 does not match");
	}

	/**
	 * @depends testTraining
	 */
	function testTrainingApp2(){
		$t=new \WCS\Training();
		$this->assertTrue($t->setTraining("0.95"),"set training in training app");
		$a=new \WCS\TrainingApp();
		$this->assertTrue($a->add($t),"Unable to add training to edit app");
		$this->assertContains("0.95",$a->edit("worker.php"),"Edit app does not edit");

		$a=new \WCS\TrainingApp();
		$this->assertFalse($a->load(),"Should not load empty training");
		$_REQUEST['action']='Load';
		$_REQUEST['worker']='AndrewL';
		$_REQUEST['subcell']='1010';
		$_REQUEST['training']='0.95';
		$this->assertTrue($a->load(),"training:0.95 is not in the database");
		$this->assertContains("0.95",$t->getTraining(),"Training object does not contain training");
		$_REQUEST['action']='Add/Edit';
		$_REQUEST['worker']='AndrewL';
		$_REQUEST['subcell']='1010';
		$_REQUEST['training']='0.99';
		$this->assertTrue($a->save(),"Cannot save");
	}
	
	/**
	 * @depends testTrainingApp2
	 */
	function testWorkerWeb(){
		$this->assertValidHTML('Web/worker.php');
	}
	
}

if (!defined('PHPUnit_MAIN_METHOD')) {
	TrainingApp2TestCase::main();
}
?>