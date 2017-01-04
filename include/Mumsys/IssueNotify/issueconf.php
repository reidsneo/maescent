<?php

$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
        if($row['nm_setting']=="emailcron") $emailcron=$row['val_setting'];
        if($row['nm_setting']=="emailcheckinterval") $emailcheckinterval=$row['val_setting'];
        if($row['nm_setting']=="emailnotifytime") $emailnotifytime=$row['val_setting'];
        if($row['nm_setting']=="emailtarget") $emailtarget=$row['val_setting'];
        if($row['nm_setting']=="emailcc") $emailcc=$row['val_setting'];
        if($row['nm_setting']=="emailbotuser") $emailbotuser=$row['val_setting'];
        if($row['nm_setting']=="emailbotpass") $emailbotpass=$row['val_setting'];
        if($row['nm_setting']=="emailtargetname") $emailtargetname=$row['val_setting'];
}
	$tpl->assign('emailcron', $emailcron);
	$tpl->assign('emailcheckinterval',$emailcheckinterval);
	$tpl->assign('emailnotifytime',$emailnotifytime);
	$tpl->assign('emailtarget',$emailtarget);
	$tpl->assign('emailcc',$emailcc);
	$tpl->assign('emailbotuser',$emailbotuser);
	$tpl->assign('emailbotpass',$emailbotpass);
	$tpl->assign('emailtargetname',$emailtargetname);
	$tpl->display(realpath(dirname(__FILE__))."/issueconf.tpl");


?>