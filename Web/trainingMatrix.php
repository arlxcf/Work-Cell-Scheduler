<!DOCTYPE html>
<html>
<head>
<!-- Person Edit Copyright 2014 by WebIS Spring 2014 class License Apache 2.0 -->
<meta charset="UTF-8">
<title>WCS</title>
</head>
<body>
<?php 
require_once 'Work-Cell-Scheduler/App/trainingApp2.php';
$b=new \WCS\TrainingApp();
$b->process("worker.php");
?>
<p>Training Matrix
<table border='1'>

<tr><th></th>
<?php 
require_once 'Work-Cell-Scheduler/App/trainingMatrixApp.php';
$t=new WCS\TrainingMatrix();
foreach($t->getSubcells() as $s){
	echo "<th>$s\n";
}
?>
</tr>
<?php 
foreach($t->getWorkers() as $w){
	echo "<tr><th>$w</th>";
	foreach($t->getSubcells() as $s) {
		echo "<td>".$t->getTraining($w,$s)."</td>";
	}		
	echo "\n";
}
?>

</table>
</body>
</html>