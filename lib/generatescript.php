<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$busmap = array();
$DB = new CentreonDB("centreon");
$chkhost=$DB->query("SELECT host_name FROM `host`");
echo "{OBJECT_TYPE};{COMMAND};{PARAMETERS}";
while($row=$chkhost->fetchRow()){
	echo "SERVICE;ADD;".$row['host_name'].";ping-service;Ping-LAN<br>";
}
