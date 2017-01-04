<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$argv[2]="172.30.241.57";//"172.30.241.14";//"172.30.244.59";
$argv[3]="AML";
$argv[1]="3969";//"1024";

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
  $mikrotik->setDebug(true);

  try {
   $mikrotik->connect($router);  
    $mikrotik->login($username, $password);
    //disable wlan2
    $mikrotik->send("/interface/wireless/print",false);//array('name' => 'wlan1')
    $mikrotik->send(":put [get wlan1 value-name=rate-set]",false);
    $wew = $mikrotik->read();
    echo "<pre>";
    var_dump($wew);
    if(count($wew['!done'])>0){

    }


    return 0;
    } catch (Exception $ex) {
     echo "Caught exception from router: " . $ex->getMessage() . "\n";
    return 1;
    }
}