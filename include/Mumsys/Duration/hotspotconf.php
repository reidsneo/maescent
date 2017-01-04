<?php

$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
    if($row['nm_setting']=="hotspotcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="hotspotinterval") $hotspotinterval=$row['val_setting'];
    if($row['nm_setting']=="hotspotofflinebus") $saveofflinebus=$row['val_setting'];
}
	$tpl->assign('hotspotmon', $hotspotmon);
	$tpl->assign('hotspotinterval',$hotspotinterval);
	$tpl->assign('saveofflinebus',$saveofflinebus);
	$tpl->display(realpath(dirname(__FILE__))."/hotspotconf.tpl");


?>