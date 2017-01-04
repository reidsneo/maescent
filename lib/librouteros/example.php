<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['id'])){
//echo $_POST['id'];
//exit;

//foreach ($busdata as $key => $val) {
    $ipbus=$_POST['ip'];
    $portbus="8728";
    $router   = $ipbus.':'.$portbus;
    $username = 'admin';
    $password = 'k0nijn';

    $args    = '';
    $uptime=0;
    $active=0;
    $download=0;
    $upload=0;
    $session="";
    $sysversion="N/A";
    $firmware="N/A";
    $networktype="N/A";
    $serialnum="N/A";
    $fp = fsockopen($ipbus,$portbus, $errno, $errstr, 5);
    if (!$fp) {
    fclose($fp);
      $isonline="OFFLINE";
        echo "Bus ID : ".$_POST['id'];
        echo "<br>Bus Status : ".$isonline;
        echo "<br>Bus Type : ".$_POST['type'];
        echo "<br>Uptime : ".$uptime;
        echo "<br>Active User : ".$active."/".count($session);
        echo "<br>Total Download : ".$download;
        echo "<br>Total Upload : ".$upload;
        echo "<br>Total Data : ".($upload+$download);
        echo "<br>Serial Number = : ".$serialnum;
        echo "<br>System Version : ".$sysversion;
        echo "<br>Current Firmware : ".$firmware."<br><br>";
    } else {
      $isonline="ONLINE";
        fclose($fp);
        require_once 'RouterOS.php';
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
    $uptime=0;
    $active=0;
    $download=0;
    $upload=0;
    $sysversion="N/A";
    $firmware="N/A";
    $networktype="N/A";
    $serialnum="N/A";
    for ($i=0; $i <(count($hotspot)-1); $i++) {
        if($hotspot[$i]['bytes-in']){
            $download=$download+$hotspot[$i]['bytes-in'];
        }
        if($hotspot[$i]['bytes-out']){
            $upload=$upload+$hotspot[$i]['bytes-out'];
        }
    }
   
    $uptime=$system[0]["uptime"];
    $firmware=$board[0]["current-firmware"];
    $serialnum=$board[0]["serial-number"];
    $sysversion=$system[0]["version"];








           // $mikrotik->send("/tool/user-manager/session/getall", $args);
           // $session = $mikrotik->read();
/*
            $mikrotik->send("/system/resource/getall", $args);
            $system = $mikrotik->read();

            $mikrotik->send("/system/routerboard/getall", $args);
            $board = $mikrotik->read();
            for ($i=0; $i <(count($session)-1); $i++) { 
               if($session[$i]['active']=="true"){
                    $active=$active+1;
                }
                if($session[$i]['download']){
                    $download=$download+$session[$i]['download'];
                }
                if($session[$i]['upload']){
                    $upload=$upload+$session[$i]['upload'];
                }
            }


            $uptime=$system[0]["uptime"];
            $firmware=$board[0]["current-firmware"];
            $serialnum=$board[0]["serial-number"];
            $sysversion=$system[0]["version"];
            /*
            echo "Bus ID : ".$_POST['id'];
            echo "<br>Bus Status : ".$isonline;
            echo "<br>Bus Type : ".$_POST['type'];
            echo "<br>Uptime : ".$uptime;
            echo "<br>Active User : ".$active."/".count($session);
            echo "<br>Total Download : ".$download;
            echo "<br>Total Upload : ".$upload;
            echo "<br>Total Data : ".($upload+$download);
            echo "<br>Serial Number = : ".$serialnum;
            echo "<br>System Version : ".$sysversion;
            echo "<br>Current Firmware : ".$firmware."<br><br>";
            */
             $databus=array('isonline'=>$isonline,'uptime'=>$uptime,'activeuser'=>count($hotspot)-1,'bytein'=>$download,'byteout'=>$upload,'totaldata'=>($upload+$download),'serialnumber'=>$serialnum,'sysversion'=>$sysversion,'firmware'=>$firmware);
    echo json_encode($databus);
        } catch (Exception $ex) {
            echo "Caught exception from router: " . $ex->getMessage() . "\n";
        }
    }


}else{

$ipbus="172.30.241.86";
$portbus="8728";
$router   = $ipbus.':'.$portbus;
$username = 'admin';
$password = 'k0nijn';

$args    = '';

$fp = fsockopen($ipbus,$portbus, $errno, $errstr, 1);
if (!$fp) {
  $isonline="OFFLINE";
    fclose($fp);
} else {
  $isonline="ONLINE";
    fclose($fp);
}

require_once 'RouterOS.php';

$mikrotik = new Lib_RouterOS();
$mikrotik->setDebug(false);

try {
    $mikrotik->connect($router);  
    $mikrotik->login($username, $password);
   // $mikrotik->send("/tool/user-manager/session/getall", $args);
 //   $session = $mikrotik->read();

    $mikrotik->send("/system/resource/getall", $args);
    $system = $mikrotik->read();

    $mikrotik->send("/system/routerboard/getall", $args);
    $board = $mikrotik->read();

    $mikrotik->send("/interface/getall", $args);
    $hotspot = $mikrotik->read();
// echo "<br><br>";
// var_dump($board);
// echo "<br><br><pre>";
// print_r($we)."</pre>";


    $uptime=0;
    $active=0;
    $download=0;
    $upload=0;
    $sysversion="N/A";
    $firmware="N/A";
    $networktype="N/A";
    $serialnum="N/A";
    $uptimeuser=array();
    $trafficin=array();
    $trafficout=array();
    $macaddr=array();
$datenow=date("Y-m-d H:i:s");
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

    echo "INSERT INTO `mum_hotspot_usage` (`bus_id`, `bus_ip`, `bus_state`, `bus_uptime`, `bus_bytein`, `bus_byteout`, `bus_totdata`, `bus_activeuser`, `bus_useruptime`, `bus_usertrfin`, `bus_usertrfout`, `bus_usermacaddr`, `bus_serialnum`, `bus_ver`, `bus_firmware`, `date`) VALUES ('2', '4', '".$isonline."', '".$uptime."', '".$download."', '".$upload."', '".$upload+$download."', '".(count($hotspot)-1)."', '".implode(",",$uptimeuser)."', '".implode(",",$trafficin)."', '".implode(",",$trafficout)."', '".implode(",",$macaddr)."', 'serial', 'ver', 'firmware', '".$datenow."');";
    $uptime=$system[0]["uptime"];
    $firmware=$board[0]["current-firmware"];
    $serialnum=$board[0]["serial-number"];
    $sysversion=$system[0]["version"];
   // echo "Bus ID : ".$busid;
    echo "Bus Status : ".$isonline;
    echo "<br>Uptime : ".$uptime;
    echo "<br>Active User : ".(count($hotspot)-1);
    echo "<br>Total Byte in : ".$download;
    echo "<br>Total Byte out : ".$upload;
    echo "<br>Total Data : ".($upload+$download);
    echo "<br>Serial Number = : ".$serialnum;
    echo "<br>System Version : ".$sysversion;
    echo "<br>Current Firmware : ".$firmware;
echo "<br><br>";


echo "<pre>";
print_r($hotspot);
"</pre>";
} catch (Exception $ex) {
   echo "Caught exception from router: " . $ex->getMessage() . "\n";
}

    
}
    
//}











/*

$ipbus="172.16.2.173";
$portbus="8764";
$router   = $ipbus.':'.$portbus;
$username = 'admin';
$password = 'k0nijn';

$args    = '';

$fp = fsockopen($ipbus,$portbus, $errno, $errstr, 5);
if (!$fp) {
  $isonline="OFFLINE";
} else {
  $isonline="ONLINE";
    fclose($fp);
}

require_once 'RouterOS.php';

$mikrotik = new Lib_RouterOS();
$mikrotik->setDebug(false);

try {
    $mikrotik->connect($router);
    $mikrotik->login($username, $password);
    $mikrotik->send("/tool/user-manager/session/getall", $args);
    $session = $mikrotik->read();

    $mikrotik->send("/system/resource/getall", $args);
    $system = $mikrotik->read();

    $mikrotik->send("/system/routerboard/getall", $args);
    $board = $mikrotik->read();

    $uptime=0;
    $active=0;
    $download=0;
    $upload=0;
    $sysversion="N/A";
    $firmware="N/A";
    $networktype="N/A";
    $serialnum="N/A";
    for ($i=0; $i <(count($session)-1); $i++) { 
       if($session[$i]['active']=="true"){
            $active=$active+1;
        }
        if($session[$i]['download']){
            $download=$download+$session[$i]['download'];
        }
        if($session[$i]['upload']){
            $upload=$upload+$session[$i]['upload'];
        }
    }


    $uptime=$system[0]["uptime"];
    $firmware=$board[0]["current-firmware"];
    $serialnum=$board[0]["serial-number"];
    $sysversion=$system[0]["version"];
    echo "Bus ID : ".$busid;
    echo "Bus Status : ".$isonline;
    echo "Uptime : ".$uptime;
    echo "<br>Active User : ".$active."/".count($session);
    echo "<br>Total Download : ".$download;
    echo "<br>Total Upload : ".$upload;
    echo "<br>Total Data : ".($upload+$download);
    echo "<br>Serial Number = : ".$serialnum;
    echo "<br>System Version : ".$sysversion;
    echo "<br>Current Firmware : ".$firmware;
} catch (Exception $ex) {
    echo "Caught exception from router: " . $ex->getMessage() . "\n";
}
*/