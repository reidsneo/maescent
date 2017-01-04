<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$method=$_GET['method'];
	$sql="SELECT * FROM daily_alert WHERE lastcheck LIKE '%".date("Y-m-d")."%' ORDER BY bus_id DESC";
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