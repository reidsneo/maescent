<?php
require_once realpath(dirname(__FILE__).'/../common.php');
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$tpl->assign('nagios_servers', "ewr");
$tpl->assign('msg_interval', "rew");
$tpl->display(realpath(dirname(__FILE__))."/tiket.tpl");
?>