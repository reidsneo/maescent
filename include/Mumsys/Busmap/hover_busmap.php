<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$DBStorage = new CentreonDB("centstorage");
$busid=$_GET['busid'];
$chkgen = $MNPrack->query("SELECT * from hw_general WHERE bus_id='$busid';");
$hwgen=$chkgen->fetchRow();

$chkmod = $MNPrack->query("SELECT * from hw_module WHERE bus_id='$busid';");
$chktag = $MNPrack->query("SELECT * from hw_tag WHERE bus_id='$busid';");
$getindex = $DBStorage->query("SELECT id from index_data WHERE host_name='$busid' AND service_description='ping-service';");
$getid=$getindex->fetchRow();
$indexid=$getid['id'];
$hwtag=$chktag->fetchRow();

echo "<b>Module List :</b><br>";
while ($rowmod=$chkmod->fetchRow()) {
	echo "Slot [".$rowmod['slot']."] ".str_replace("0", "", $rowmod['func_id'])." - ".str_replace("0", "", $rowmod['mod_id'])."<br>";
}
echo "</b><br>";
echo "<img src='/centreon/include/views/graphs/generateGraphs/generateImage.php?index=".$indexid."&end=".time()."&start=".(time()-(60*60*3))."' width='550px'>";
?>