<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors',0);
error_reporting(0);
date_default_timezone_set('Europe/Berlin');


require('konektor.php');
require('RouterAPI.php');
$API = new RouterosAPI();
$API->debug = false;
$busid="5673";
$busgroup="AML";
$busip="172.30.244.58";
$date= date("Y-m-d H:i:s");
$today= date("Y-m-d");
//172.30.242.43 sre 3388
//172.30.241.57 aml
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

			if ($API->connect($busip, 'admin', 'k0nijn')) {
			/*
			======================================================
			PHASE 1
			Modem info-channel
			======================================================
			*/

				//Get Modem & Serial Port & Info Channel
				$usbport=array();
				$modemtype=array();
				$modemch=array();
				$port=$API->comm("/port/print",false);
				//Step 1
				//Check what kind of modem are used
				foreach ($port as $key => $val) {
					if(strpos($val['name'], 'usb') !== false) {
						array_push($usbport, $val['name']);
						array_push($modemch, $val['channels']);
						if($val['channels']==3){
							array_push($modemtype, "Huawei 770W");
						}else if($val['channels']==4){
							array_push($modemtype, "Huawei 820U");
						}else if($val['channels']==6){
							array_push($modemtype, "Huawei 820W");
						}
					}
				}

				$idmodem=array();
				$modem=array();
				$ppp=$API->comm("/interface/ppp-client/print",false);
				//Step 2
				//Check how many modemx PPP client available
				foreach ($ppp as $key => $val) {
					if(strpos($val['name'], 'modem') !== false) {
						array_push($modem, $val['name']);
						array_push($idmodem, $val['.id']);
					}
				}

				//Step 2
				//Check is total serialport usb = total modem interface
				if(count($modem)==count($usbport)){
					//step 2
					//check is router contain at least 820W modem
					if(in_array_r("Huawei 820W", $modemtype)){
						//echo "this router contain Huawei 820W";
						$chksysschdl=$API->comm("/system/scheduler/getall");
						//step 3 check if "Cleanup-Modem" available, if available disable it
						if(in_array_r("cleanup-modem", $chksysschdl)){
							$sysschdlkey=$chksysschdl[recursive_array_search("cleanup-modem",$chksysschdl)]['.id'];
							$API->comm("/system/scheduler/set",array('.id' => $sysschdlkey,'disabled'=>'yes'));
						}

						//step 4 Enable all modem						
						$chkmodem=$API->comm("/interface/print");
						if(in_array_r("modem1", $chkmodem)){
							$modem1key=$chkmodem[recursive_array_search("modem1",$chkmodem)]['.id'];
							echo $modem1key;
							$API->comm("/interface/set",array('.id' => $modem1key,'disabled'=>'no'));
						}
						if(in_array_r("modem2", $chkmodem)){
							$modem2key=$chkmodem[recursive_array_search("modem2",$chkmodem)]['.id'];
							$API->comm("/interface/set",array('.id' => $modem2key,'disabled'=>'no'));		
						}
					}else{
						echo "NO Huawei 820W leave it be";
					$result="NO Huawei 820W leave it be";
					}

				}else{
					echo "Modem & Port is different leave it be";
					$result="Modem & Port is different leave it be";
				}

			/*
			======================================================
			PHASE 2
			Wi-Fi Settings
			======================================================
			*/
				$chkwlan2=$API->comm("/interface/wireless/print",array('.proplist' => 'name'));
				//Step1
				//Disable Wlan2 is available
				if(in_array_r("wlan2", $chkwlan2)){
					$wlandisable=$API->comm("/interface/wireless/set",array('.id' => 'wlan2','disabled'=>'yes'));
				}
				//Step2
				//Change Data Rates
				$setting=$API->comm("/interface/wireless/set",array('.id' => 'wlan1','rate-set'=>'configured','basic-rates-b'=>'1Mbps','basic-rates-a/g'=>'6Mbps','supported-rates-b'=>'1Mbps','supported-rates-a/g'=>'6Mbps,24Mbps','distance'=>'indoors','noise-floor-threshold'=>'-90'));
				
				//If Setup OK write to database
				if(count($setting)==0){
			    $isexist=mysql_num_rows(mysql_query("SELECT bus_id FROM `mnp_rack`.`mikrotik_wifi_improve` WHERE bus_id='".$busid."';"));

			    if(count($modem)>0){
			    	$listmodem=implode(",", $modem);
			    }else{
			    	$listmodem="";
			    }

			    if(count($modemtype)>0){
			    	$listmtype=implode(",", $modemtype);
			    }else{
			    	$listmtype="";
			    }

			    if(count($modemch)>0){
			    	$listmch=implode(",", $modemch);
			    }else{
			    	$listmch="";
			    }

			    if(count($usbport)>0){
			    	$listserial=implode(",", $usbport);
			    }else{
			    	$listserial="";
			    }
			    

				mysql_select_db("mnp_rack");
				if($isexist>0){
					echo "wifi previously set";
					$result="wifi previously set";
					mysql_query("UPDATE `mnp_rack`.`mikrotik_wifi_improve` SET `lastresultmsg` = '".$result."' , `lastcheck` = '".$date."' WHERE `bus_id` = '".$busid."';");
			    }else{
					$result="wifi performance just set";
					echo "wifi performance just set";
					mysql_query("INSERT INTO `mnp_rack`.`mikrotik_wifi_improve` (`bus_id`, `group`, `ip`, `lastresultmsg`, `isokay`, `num_modem`, `modem_channel`, `modem_type`, `modem_serial`, `date`, `lastcheck`) VALUES ('".$busid."', '".$busgroup."', '".$busip."', '".$result."', '1', '".$listmodem."', '".$listmch."', '".$listmtype."', '".$listserial."', '".$date."', '".$date."');");
			    }
				}else{
					echo "PROBLEM";
				}


			$API->disconnect();
		}
?>