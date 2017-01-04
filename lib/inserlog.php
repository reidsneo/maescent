<?php

require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$MNPrack->query("INSERT INTO `mnp_rack`.`daily_alert` (`bus_id`, `alert`, `date`) VALUES ('123', 'dsfdsfdsfds', '2016-09-26 14:11:59'); ");

?>