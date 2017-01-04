<?php
require_once "/usr/share/centreon/www/include/Mumsys/common.php";
foreach ($busdata as $key => $val) {
	$busid=$val['id'];
	$busip=$val['ip'];
	$bustype=$val['type'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://192.168.87.54/centreon/lib/librouteros/example.php?id=$busid&ip=$busip&type=$bustype");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($ch);
	curl_close($ch);
	echo "http://192.168.87.54/centreon/lib/librouteros/example.php?id=$busid&ip=$busip&type=$bustype.$output";
}
$ch = curl_init();
?>