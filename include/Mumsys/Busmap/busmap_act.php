<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
foreach ($_POST['dataarr'] as $key => $val) {
	$busdt=explode("|",$val);
	$busid=$busdt[0];
	$busx=$busdt[1];
	$busy=$busdt[2];
	$cntbus=$MNPrack->query("SELECT * FROM mum_busdata WHERE `bus_id` = '$busid';");
	if($cntbus->numRows()>0){
		if($busx!=0 AND $busy!=0){
			$res=$MNPrack->query("UPDATE `mum_busdata` SET `bus_x` = '$busx' , `bus_y` = '$busy' WHERE `bus_id` = '$busid';");
		}
	}else{
		//$res=$pearDB->query("INSERT INTO `mum_busmap` (`bus_id`, `bus_x`, `bus_y`, `state`, `lastupd`) VALUES ('$busid', '$busx', '$busy', '', '');");
	}
}

/*
$cntbus=$pearDB->query("SELECT * FROM mum_busmap WHERE `bus_id` = '$_POST[id]';");
	if($cntbus->numRows()>0){
		$busval=$cntbus->fetchRow();
		if($busval['x']!=$_POST['x'] OR $busval['y']!=$_POST['y']){
			echo "<br>bus ".$_POST['id']."X->".$_POST['x']."Y->".$_POST['y'];
			$res=$pearDB->query("UPDATE `mum_busmap` SET `x` = '$_POST[x]' , `y` = '$_POST[y]' WHERE `bus_id` = '$_POST[id]';");
		}else{
			echo "<br>bus ".$_POST['id']."X->tetap"."Y->tetap";
		}
	}else{
		$res=$pearDB->query("INSERT INTO `mum_busmap` (`bus_id`, `x`, `y`, `state`, `lastupd`) VALUES ('$_POST[id]', '$_POST[x]', '$_POST[y]', '', '');");
	}
*/
?>