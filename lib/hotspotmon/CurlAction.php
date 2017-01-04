<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");

$ipbus=$argv[2];
$portbus="8728";
$router   = $ipbus.':'.$portbus;
$username = 'admin';
$password = 'k0nijn';

$args    = '';
$uptime=0;
$active=0;
$download=0;
$upload=0;
$sysversion="N/A";
$firmware="N/A";
$networktype="N/A";
$serialnum="N/A";
$datenow=date("Y-m-d H:i:s");


$fp = @fsockopen($ipbus,$portbus, $errno, $errstr, 1);
if (!$fp) {
  $isonline="OFFLINE";
    @fclose($fp);
    $databus=array('busid'=>$argv[1],'bustype'=>$argv[3],'isonline'=>$isonline,'uptime'=>$uptime,'activeuser'=>0,'bytein'=>$download,'byteout'=>$upload,'totaldata'=>($upload+$download),'serialnumber'=>$serialnum,'sysversion'=>$sysversion,'firmware'=>$firmware);
    echo json_encode($databus);
    $isoffbus=$MNPrack->getOne("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotofflinebus';");
    if($isoffbus>0){
         $res=$MNPrack->query("INSERT INTO `mum_hotspot_usage` (`bus_id`, `bus_ip`, `bus_state`, `bus_uptime`, `bus_bytein`, `bus_byteout`, `bus_totdata`, `bus_activeuser`, `bus_useruptime`, `bus_usertrfin`, `bus_usertrfout`, `bus_usermacaddr`, `bus_serialnum`, `bus_ver`, `bus_firmware`, `date`) VALUES ('".$argv[1]."', '".$argv[2]."', '".$isonline."', 'N/A', 'N/A', 'N/A', 'N/A', '0', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '".$datenow."');");
     }

} else {
  $isonline="ONLINE";
  fclose($fp);

  require_once '../RouterOS.php';
  $mikrotik = new Lib_RouterOS();
  $mikrotik->setDebug(false);

  try {
    $mikrotik->connect($router);  
    $mikrotik->login($username, $password);
    
    $mikrotik->send("/system/resource/getall", $args);
    $system = $mikrotik->read();

    $mikrotik->send("/system/routerboard/getall", $args);
    $board = $mikrotik->read();

    $mikrotik->send("/ip/hotspot/host/getall", $args);
    $hotspot = $mikrotik->read();
    $uptimeuser=array();
    $trafficin=array();
    $trafficout=array();
    $macaddr=array();
    for ($i=0; $i <(count($hotspot)-1); $i++) {
        if($hotspot[$i]['bytes-in']){
            $download=$download+$hotspot[$i]['bytes-in'];
            array_push($trafficin,$hotspot[$i]['bytes-in']);
        }
        if($hotspot[$i]['bytes-out']){
            $upload=$upload+$hotspot[$i]['bytes-out'];
            array_push($trafficout,$hotspot[$i]['bytes-out']);
        }
        if($hotspot[$i]['mac-address']){
            array_push($macaddr,$hotspot[$i]['mac-address']);
        }
        if($hotspot[$i]['uptime']){
            array_push($uptimeuser,$hotspot[$i]['uptime']);
        }
    }

    $uptime=$system[0]["uptime"];
    $firmware=$board[0]["current-firmware"];
    $serialnum=$board[0]["serial-number"];
    $sysversion=$system[0]["version"];

    $databus=array('busid'=>$argv[1],'bustype'=>$argv[3],'isonline'=>$isonline,'uptime'=>$uptime,'activeuser'=>count($hotspot)-1,'bytein'=>$download,'byteout'=>$upload,'totaldata'=>($upload+$download),'serialnumber'=>$serialnum,'sysversion'=>$sysversion,'firmware'=>$firmware);
    echo json_encode($databus);
    $res=$MNPrack->query("INSERT INTO `mum_hotspot_usage` (`bus_id`, `bus_ip`, `bus_state`, `bus_uptime`, `bus_bytein`, `bus_byteout`, `bus_totdata`, `bus_activeuser`, `bus_useruptime`, `bus_usertrfin`, `bus_usertrfout`, `bus_usermacaddr`, `bus_serialnum`, `bus_ver`, `bus_firmware`, `date`) VALUES ('".$argv[1]."', '".$argv[2]."', '".$isonline."', '".$uptime."', '".$download."', '".$upload."', '".($upload+$download)."', '".(count($hotspot)-1)."', '".implode(",",$uptimeuser)."', '".implode(",",$trafficin)."', '".implode(",",$trafficout)."', '".implode(",",$macaddr)."', '".$serialnum."', '".$sysversion."', '".$firmware."', '".$datenow."');");
    } catch (Exception $ex) {
     echo "Caught exception from router: " . $ex->getMessage() . "\n";
    }
}
