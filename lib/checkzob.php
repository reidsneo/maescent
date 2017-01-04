<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$DB = new CentreonDB("mnp_rack");
$file = fopen("centreonzob.txt", "r");
	$i=0;
while(!feof($file)){
    $line = fgets($file);
	$chkhost=$DB->query("SELECT id_bus,id_group FROM `mum_busmap` WHERE id_bus='".trim($line)."' AND id_group='72';");
	//echo "<br>".$chkhost->numRows()."---";
	if($chkhost->numRows()>0){
		echo $line." ada<br>";
	}else{
		$i=$i+1;
		echo $line." lom<br>";
	}
	/*
    while($row=$chkhost->fetchRow()){
		echo $row['id_bus'].$chkhost->numRows()."<br>";
	}
	*/
}
	echo "<br>dsadsa".$i;
fclose($file);

 ?>