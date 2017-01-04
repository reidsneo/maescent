<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$loadbus=$MNPrack->query("SELECT DISTINCT bus_id FROM mum_hotspot_usage;");
while($row=$loadbus->fetchRow()){
	$lastbus=$MNPrack->query("SELECT id_mon FROM mum_hotspot_usage WHERE bus_id='".$row['bus_id']."' AND `date` LIKE '%2016-12-01%' ORDER BY `date` DESC LIMIT 1");
	while($rlast=$lastbus->fetchRow()){
		echo "UPDATE `mnp_rack`.`mum_hotspot_usage` SET `flag` = null WHERE `id_mon` = '".$rlast['id_mon']."';";
	}
}
?>