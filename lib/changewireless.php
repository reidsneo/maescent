<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "root";
$password = "toor";
$hostname = "192.168.87.71"; 

$argv[2]="172.30.236.210";//"172.30.241.14";//"172.30.244.59";
$argv[3]="AML";
$argv[1]="1026";//"1024";

$ipbus=$argv[2];
$portbus="8728";
$router   = $ipbus.':'.$portbus;
$username = 'admin';
$password = 'k0nijn';

$args    = '';
$datenow=date("Y-m-d H:i:s");
$fp = @fsockopen($ipbus,$portbus, $errno, $errstr, 1);
if (!$fp) {
  $isonline="OFFLINE";
    @fclose($fp);
} else {
  $isonline="ONLINE";
  fclose($fp);

  require_once 'RouterOS.php';
  $mikrotik = new Lib_RouterOS();
  $mikrotik->setDebug(false);

  try {
   $mikrotik->connect($router);  
    $mikrotik->login($username, $password);
    //disable wlan2
    $mikrotik->send("/interface/wireless/set",array('.id' => 'wlan2','disabled'=>'yes'));
    //setting wlan1
    $mikrotik->send("/interface/wireless/set",array('.id' => 'wlan1','rate-set'=>'configured','basic-rates-b'=>'1Mbps','basic-rates-a/g'=>'6Mbps','supported-rates-b'=>'1Mbps','supported-rates-a/g'=>'6Mbps','distance'=>'indoors','noise-floor-threshold'=>'-90'));
    $wew = $mikrotik->read();
    if(count($wew['!done'])>0){

    }


    return 0;
    } catch (Exception $ex) {
     echo "Caught exception from router: " . $ex->getMessage() . "\n";
    return 1;
    }
}