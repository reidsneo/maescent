<?php
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$state=$_GET['state'];
$res=$MNPrack->query("SELECT * FROM mum_hotspot_usage");

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
//echo "</pre>";
?>