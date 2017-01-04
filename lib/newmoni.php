<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "root";
$password = "toor";
$hostname = "192.168.87.71"; 
$dbhandle=mysql_connect($hostname, $username, $password);
mysql_select_db("mnp_rack");

$iscron=mysql_fetch_array(mysql_query("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotcron';"));
if($iscron['val_setting']==0){
die("Hotspotmon Disabled");
return 1;
}

$argv[2]="172.30.244.59";//"172.30.240.188";
$argv[3]="AML";
$argv[1]="3843";

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

function timegap($time){
if (preg_match('/^(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?(?:(?<minutes>\d+)m\s*)?(?:(?<seconds>\d+)s\s*)?$/', $time, $matches)) {
        $time=sprintf(
            '%02s:%02s:%02s',
            (!empty($matches['hours'])   ? $matches['hours']   : '00'),
            (!empty($matches['minutes']) ? $matches['minutes'] : '00'),
            (!empty($matches['seconds']) ? $matches['seconds'] : '00')
        );
        if(!empty($matches['days'])){
          $day=$matches['days'];
        }
        if(!empty($matches['hours'])){
          $hour=$matches['hours'];
        }
        if(!empty($matches['minutes'])){
          $min=$matches['minutes'];
        }
        if(!empty($matches['seconds'])){
          $sec=$matches['seconds'];
        }
        return array('time' => $time,'day'=>$day);
    }else{
      if (preg_match('/^[0-9]{0,2}+d/', $time, $matches)) {
        $day=$matches[0];
      }
      $sub=str_replace($day,"",$time);
      if(preg_match('/(^[0-9]{2})(:{1})([0-9]{2})(:{1})([0-9]{2})$/',$sub,$match)){
        $time=$match[0];
        $normalgap=1;
      }
      if($normalgap==1){
        return array('time' => $time,'day'=>str_replace("d", "",$day));
      }
    }
}

function calctime($timegap){
  if(count($timegap['day'])>0){
    $hour=$timegap['day']*24;
    $parts = explode(':', $timegap['time']);
    $sumtime=($hour+$parts[0]);
    if(strlen($sumtime)<2){
      $sumtime="0".$sumtime;
    }
    return $sumtime.":".$parts[1].":".$parts[2];
  }else{
    return $timegap['time'];
  }
}

$fp = @fsockopen($ipbus,$portbus, $errno, $errstr, 1);
if (!$fp) {
  $isonline="OFFLINE";
    @fclose($fp);
    $databus=array('busid'=>$argv[1],'bustype'=>$argv[3],'isonline'=>$isonline,'uptime'=>$uptime,'activeuser'=>0,'bytein'=>$download,'byteout'=>$upload,'totaldata'=>($upload+$download),'serialnumber'=>$serialnum,'sysversion'=>$sysversion,'firmware'=>$firmware);
    echo json_encode($databus);
    $isoffbus=mysql_fetch_array(mysql_query("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotofflinebus';"));
    if($isoffbus['val_setting']>0){
       //  mysql_query("INSERT INTO `mnp_rack`.`mum_hotspot_usage` (`bus_id`, `bus_ip`, `bus_state`, `bus_uptime`, `bus_bytein`, `bus_byteout`, `bus_totdata`, `bus_activeuser`, `bus_useruptime`, `bus_usertrfin`, `bus_usertrfout`, `bus_usermacaddr`, `bus_serialnum`, `bus_ver`, `bus_firmware`, `bus_boardname`, `isinternet`, `ismt`, `date`) VALUES ('".$argv[1]."', '".$argv[2]."', 'OFFLINE', '".$uptime."', '0', '0', '0', '0', '0', '0', '0 ', '', '', '', '', '', '', '', '".$datenow."');");
     }

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

    $mikrotik->send("/interface/ppp-client/getall", $args);
    $interfaceinet = $mikrotik->read();

    $mikrotik->send("/interface/getall", $args);
    $interfacemt = $mikrotik->read();

    $mikrotik->send("/ip/hotspot/active/getall", $args);
    $active = $mikrotik->read();

    $mikrotik->send("/ip/hotspot/user/getall", $args);
    $hotspot = $mikrotik->read();

    $mikrotik->send("/interface/wireless/registration-table/getall", $args);
    $wlan = $mikrotik->read();


    $uptimeuser=array();
    $trafficin=array();
    $trafficout=array();
    $macaddr=array();
    $activeuser=array();
    $hotspotuser=array();
    $isinternet="false";
    $ismt="false";

    for ($i=0; $i <(count($active)-1); $i++) {
        if($active[$i]['mac-address']){
            $activeuser[$i]['mac-address']=$active[$i]['mac-address'];
        }
        if($active[$i]['uptime']){
            $activeuser[$i]['uptime']=$active[$i]['uptime'];
        }
    } 
    
    for ($i=0; $i <(count($hotspot)-1); $i++) {
        if($hotspot[$i]['bytes-in']){
            $download=$download+$hotspot[$i]['bytes-in'];
            $hotspotuser[$i]['bytes-in']=$hotspot[$i]['bytes-in'];
        }
        if($hotspot[$i]['bytes-out']){
            $upload=$upload+$hotspot[$i]['bytes-out'];
            $hotspotuser[$i]['bytes-out']=$hotspot[$i]['bytes-out'];
        }
        if($hotspot[$i]['mac-address']){
            $hotspotuser[$i]['mac-address']=$hotspot[$i]['mac-address'];
        }
        if($hotspot[$i]['uptime']){
            $hotspotuser[$i]['uptime']=$hotspot[$i]['uptime'];
        }
    }

    for ($i=0; $i <(count($interfaceinet)-1); $i++) {
        if($interfaceinet[$i]['user']=="internet"){
            $isinternet=$interfaceinet[$i]['running'];}
    }

     for ($i=0; $i <(count($interfacemt)-1); $i++) {
        if($interfacemt[$i]['type']=="l2tp-out"){
            $ismt=$interfacemt[$i]['running'];}
    }

var_dump($hotspot);

    $uptime=calctime(timegap($system[0]["uptime"]));//$system[0]["uptime"];
    $firmware=$board[0]["current-firmware"];
    $serialnum=$board[0]["serial-number"];
    $sysversion=$system[0]["version"];
    $boardname=$system[0]["board-name"];
    $databus=array('busid'=>"ZZZZ",'bustype'=>"AML",'isonline'=>$isonline,'uptime'=>$uptime,'bytein'=>$download,'byteout'=>$upload,'totaldata'=>($upload+$download),'activeuser'=>count($active)-1,
        'activeuserdetail'=>$activeuser,
        'usagedetail'=>$hotspotuser,
        'serialnumber'=>$serialnum,'sysversion'=>$sysversion,'firmware'=>$firmware,'boardname'=>$boardname,'isinternet'=>$isinternet,'ismt'=>$ismt);
   $busmoni=json_encode($databus);

   $h=0;
   $hotup="";
   $hotin="";
   $hotout="";
   $hotmac="";
   $chot=count($hotspotuser);
   foreach ($hotspotuser as $key => $hotuser) {
    $h++;
    if(!isset($hotuser['bytes-in'])){$hotuser['bytes-in']=0;}
    if($hotuser['bytes-in']==""){$hotuser['bytes-in']=0;}
    if(!isset($hotuser['bytes-out'])){$hotuser['bytes-out']=0;}
    if($hotuser['bytes-out']==""){$hotuser['bytes-out']=0;}
       $hotup=$hotup.$hotuser['uptime'];
       $hotin=$hotin.$hotuser['bytes-in'];
       $hotout=$hotout.$hotuser['bytes-out'];
       $hotmac=$hotmac.$hotuser['mac-address'];
       if($h!=$chot){
        $hotup=$hotup.",";
        $hotin=$hotin.",";
        $hotout=$hotout.",";
        $hotmac=$hotmac.",";
       }
   }

   $cact=count($activeuser);
   $a=0;
   $actup="";$actmac="";
   foreach ($activeuser as $key => $actuser) {
    $a++;
       $actup=$actup.$actuser['uptime'];
       $actmac=$actmac.$actuser['mac-address'];
       if($a!=$cact){
        $actup=$actup.",";
        $actmac=$actmac.",";
       }
   }

   $prevtime=mysql_fetch_array(mysql_query("SELECT id_mon,bus_uptime FROM `mnp_rack`.`mum_hotspot_usage` WHERE bus_id='".$argv[1]."' AND date LIKE '%".date("Y-m-d")."%' ORDER BY DATE DESC LIMIT 1;"));

   if($prevtime['bus_uptime']==""){
    $prevup="00:00:00";
   }else{
    $prevup=$prevtime['bus_uptime'];
   }
   $previdmon=$prevtime['id_mon'];
   echo "<hr>";

   $splitprevup = str_replace(":","",$prevup);
   $splitnowup = str_replace(":","",$uptime);
   if($splitprevup < $splitnowup)
   {
    $islow="true";
  }else{
    $islow="false";
    //mysql_query("UPDATE `mnp_rack`.`mum_hotspot_usage` SET `flag` = 'RES' WHERE `id_mon` = '".$previdmon."';");
  }
   echo "<pre>";
   print_r($databus);
   echo "</pre>";
  //  mysql_query("INSERT INTO `mnp_rack`.`mum_hotspot_usage` (`bus_id`, `bus_ip`, `bus_state`, `bus_uptime`, `bus_bytein`, `bus_byteout`, `bus_totdata`, `bus_activeuser`,`bus_activeup`,`bus_activemac`,`bus_useruptime`, `bus_usertrfin`, `bus_usertrfout`, `bus_usermacaddr`, `bus_serialnum`, `bus_ver`, `bus_firmware`, `bus_boardname`, `isinternet`, `ismt`, `date`) VALUES ('".$argv[1]."', '".$argv[2]."', 'ONLINE', '".$uptime."', '".$download."', '".$upload."', '".($upload+$download)."', '".(count($active)-1)."', '".$actup."', '".$actmac."', '".$hotup."', '".$hotin."', '".$hotout."', '".$hotmac."', '".$serialnum."', '".$sysversion."', '".$firmware."', '".$boardname."', '".$isinternet."', '".$ismt."', '".$datenow."');");

    return 0;
    } catch (Exception $ex) {
     echo "Caught exception from router: " . $ex->getMessage() . "\n";
    return 1;
    }
}
mysql_close($dbhandle);
