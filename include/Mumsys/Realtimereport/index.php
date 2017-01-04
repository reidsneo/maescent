<?php
if (!isset($centreon)) {
	exit();
}
$busdata = array();
$busid = array();
$MNPrack = new CentreonDB("mnp_rack");
$res = $MNPrack->query("SELECT bus_id, msg_alert FROM `daily_alert` WHERE lastcheck LIKE '%".date("Y-m-d")."%' ORDER BY bus_id");
while($row = $res->fetchRow()) {
	$cntalert=explode(",", $row["msg_alert"]);
	$count=count($cntalert);
	if($count==""){
		$count=1;
	}
	array_push($busdata,"['".$row["bus_id"]."',".$count."]");
	array_push($busid,"'".$row["bus_id"]."'");
}
$res->free();
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

	function js_str($s)
	{
	    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
	}

	function js_array($array)
	{
	    $temp = array_map('js_str', $array);
	    return implode(',', $temp);
	}

	$tpl->assign('busid',str_replace('"',"",js_array(array_unique($busid))));
	$tpl->assign('busdata',str_replace('"',"",js_array(array_unique($busdata))));
	$tpl->assign('datenow',date("d/m/Y"));
	$tpl->display(realpath(dirname(__FILE__))."/dashboard.tpl");
?>
