<?php
require_once realpath(dirname(__FILE__).'/../common.php');
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
    if($row['nm_setting']=="hotspotcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="hotspotinterval") $hotspotinterval=$row['val_setting'];
    if($row['nm_setting']=="hotspotofflinebus") $saveofflinebus=$row['val_setting'];
}
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$tpl->assign('hotspotcron', $hotspotmon);
$tpl->assign('hotspotinterval', $hotspotinterval);
$tpl->assign('datenow',date("d/m/Y"));
$tpl->display(realpath(dirname(__FILE__))."/dashboard.tpl");
?>