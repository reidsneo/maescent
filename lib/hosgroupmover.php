<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$DB = new CentreonDB("centreon");
$file = fopen("sretozeob.txt", "r");
	$i=0;
while(!feof($file)){
    $line = fgets($file);
	$chkhost=$DB->query("SELECT h.host_id, h.host_name AS hostname,hg.`hg_id` AS groupid,hg.`hg_name` AS hostgroup,host_address AS address, host_alias AS alias,ns.name FROM `centreon`.host h, `centreon`.hostgroup_relation hr,`centreon`.hostgroup hg,`centreon`.`nagios_server` ns,`centreon`.`ns_host_relation` nsr WHERE h.host_name='".trim($line)."' AND host_register = '1' AND h.host_id = hr.host_host_id AND hr.`hostgroup_hg_id`=hg.`hg_id` AND ns.`id`=nsr.`nagios_server_id` AND h.`host_id`=nsr.`host_host_id` AND hg.`hg_id`='80' ORDER BY h.host_name;");
	//echo "<br>".$chkhost->numRows()."---";
	while($row=$chkhost->fetchRow()){
		$hostid=$row['host_id'];
	}
	if($chkhost->numRows()>0){
		//echo $line."  ".$hostid." ada<br>";
		echo "UPDATE `centreon`.`hostgroup_relation` SET `hostgroup_hg_id` = '80' WHERE `host_host_id` = '".$hostid."';<br>";
		$i=$i+1;
	}else{
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