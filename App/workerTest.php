<?php
// Person Test Copyright 2014 by WebIS Spring 2014 License Apache 2.0
require_once 'Work-Cell-Scheduler/TDD/validator.php';
include 'Work-Cell-Scheduler/Config/local.php';
require_once 'workerApp.php';

class WorkerTestCase extends WebIS\Validator {

	protected static $__CLASS__=__CLASS__;

	function testWorker() {
		$p=new \WCS\Worker();
		$this->assertTrue($p->setWorker("DrMiddelkoop"));
		$this->assertFalse($p->setWorker("Dr.Middelkoop"));
		$this->assertEquals("{worker: DrMiddelkoop}",$p->display(),"display only worker");
		$this->assertTrue($p->setName("Dr. Middelkoop"));
		$this->assertFalse($p->setName(""));
		$this->assertFalse($p->setName(" "));
		$this->assertEquals("Dr. Middelkoop",$p->getName());
		$this->assertEquals("{worker: DrMiddelkoop name: Dr. Middelkoop}",$p->display(),"adding name to object");
		// echo $p->display();
		$this->assertTrue($p->delete());
		$this->assertTrue($p->write());
		$this->assertTrue($p->setName("None"));
		$this->assertEquals("None",$p->getName());
		$this->assertTrue($p->read(),"Cannot read worker from database");
		$this->assertEquals("Dr. Middelkoop",$p->getName(),"Dr. Middelkoop name does not match");
	}

	/**
	 * @depends testWorker
	 */
	function testWorkerApp(){
		$p=new \WCS\Worker();
		$this->assertTrue($p->setName("Dr. Middelkoop"));
		$a=new \WCS\WorkerApp();
		$this->assertTrue($a->add($p),"Unable to add person to edit app");
		$this->assertContains("Dr. Middelkoop",$a->edit("worker.php"),"Edit app does not edit");

		$a=new \WCS\WorkerApp();
		$this->assertFalse($a->load(),"Should not load empty worker");
		$_REQUEST['action']='Load';
		$_REQUEST['worker']='DrMiddelkoop';
		$this->assertTrue($a->load(),"worker:DrMiddelkoop is not in the database");
		$this->assertContains("Dr. Middelkoop",$p->getName(),"Worker object does not contain name");
		$_REQUEST['action']='Update';
		$_REQUEST['worker']='DrMiddelkoop';
		$_REQUEST['name']='Dr. Middelkoop';
		$this->assertTrue($a->save(),"Cannot save");
	}
	
	/**
	 * @depends testWorkerApp
	 */
	function testWorkerWeb(){
		$this->assertValidHTML('Web/worker.php');
		$this->assertValidHTML('Web/worker.php','Dr. Middelkoop',
				array('action'=>'Load','worker'=>'DrMiddelkoop'),
				"unable to load from page");
		$this->assertValidHTML('Web/worker.php','Dr. Timothy Middelkoop',
				array('action'=>'Update','worker'=>'DrMiddelkoop','name'=>'Dr. Timothy Middelkoop'),
				"unable to save from page");
	}
	
}

if (!defined('PHPUnit_MAIN_METHOD')) {
	WorkerTestCase::main();
}
?>