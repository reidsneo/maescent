<?php
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$tpl->assign('hotspotcron', $hotspotmon);
$tpl->assign('datehotspot', $datepick);
$tpl->assign('hotspotinterval', $hotspotinterval);
$tpl->assign('datenow',date("d/m/Y"));
$tpl->display(realpath(dirname(__FILE__))."/listwifi.tpl");
?>