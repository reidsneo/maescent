<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
	$sql="SELECT * FROM mikrotik_wifi_improve";
	$res=$MNPrack->query($sql);

	//echo "<pre>";
	echo '{
	    "data": [';
	    $arr = array();
	    $i=0;
	    $maxrow=$res->numRows();
	while ($row =$res->fetchRow()) {
		$i=$i+1;

	   echo json_encode($row);
		if($i!=$maxrow){
			echo ",";
		}
	}
	echo ' ]
	}';
?>