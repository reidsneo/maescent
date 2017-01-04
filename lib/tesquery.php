<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);
$username = "root";
$password = "toor";
$hostname = "192.168.87.10";
$redalert=mysql_connect($hostname, $username, $password);
    mysql_select_db("mnp_rack");
$chk=mysql_num_rows(mysql_query("SELECT * FROM daily_alert WHERE bus_id='5421' AND msg_alert='cam101_failed-conn2srv_nok,ipcam_102_nok' AND statecheck LIKE '%2016-10-06%';"));
echo $chk;
