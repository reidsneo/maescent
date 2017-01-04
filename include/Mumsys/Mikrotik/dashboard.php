<?php
echo '<style>.p-lg {
    padding: 30px;
}
.red-bg {
    background-color: #ed5565;
    color: #ffffff;
}.navy-bg {
    background-color: #1ab394;
    color: #ffffff;
}
.widget {    
	border-radius: 5px;
    height: 120px;
    margin-bottom: 10px;
    margin-top: 10px;
    padding: 15px 20px;
    width: 200px;
}.text-center {
    text-align: center;
}.m-xs{    font-size: 70px;
}</style>';

require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
    $isexist=$MNPrack->query("SELECT count(bus_id) as `total` FROM `mnp_rack`.`mikrotik_wifi_improve`;");
    $row =$isexist->fetchRow();
echo '<div class="widget navy-bg p-lg text-center">
                        <div class="m-b-md">
                            <i class="fa fa-bell fa-4x"></i>
                            <h1 class="m-xs">'.$row['total'].'</h1>
                            <h3 class="font-bold no-margins">
                                Wifi Improvement Service
                            </h3>
                            <small>Number of Mikrotik Configuration changed.</small>
                        </div>
                    </div>';

?>