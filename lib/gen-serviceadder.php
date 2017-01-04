<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$busmap = array();
$DB = new CentreonDB("centreon");
$group=$_GET['gid'];
$service=$_GET['service'];
$chkhost=$DB->query("SELECT SQL_CALC_FOUND_ROWS DISTINCT h.host_id, h.host_name AS hostname,hg.`hg_id` AS groupid,hg.`hg_name` AS hostgroup,host_address AS address, host_alias AS alias FROM `centreon`.host h, `centreon`.hostgroup_relation hr,`centreon`.hostgroup hg WHERE host_register = '1' AND h.host_id = hr.host_host_id AND hr.`hostgroup_hg_id`=hg.`hg_id` AND hr.hostgroup_hg_id = '$group' ORDER BY h.host_name;");
while($row=$chkhost->fetchRow()){
	echo "SERVICE;ADD;".$row['hostname'].";ping-service;Ping-LAN<br>";
}
