<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require('RouterAPI.php');
$API = new RouterosAPI();
$API->debug = true;
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

if ($API->connect('172.30.244.86', 'admin', 'k0nijn')) {
	/*
	$API->write("/interface/wireless/print",false);
	$API->write("=advanced=",false);
	$API->write("=.proplist=rate-set",true);
	$READ = $API->read(true);
	//echo $READ[0]['rate-set'];
	$rate=$API->comm("/interface/wireless/print",array('.proplist' => 'rate-set','advanced'=>'advanced'));
	//var_dump($rate[0]['rate-set']);

	/*change*/
	/*
	$chkwlan2=$API->comm("/interface/wireless/print",array('.proplist' => 'name'));
	if(in_array_r("wlan2", $chkwlan2)){
		//$wlandisable=$API->comm("/interface/wireless/set",array('.id' => 'wlan2','disabled'=>'yes'));
	}
	$chksysschdl=$API->comm("/system/scheduler/getall");
	if(in_array_r("cleanup-modem", $chksysschdl)){
		$sysschdlkey=$chksysschdl[recursive_array_search("cleanup-modem",$chksysschdl)]['.id'];
		//$API->comm("/system/scheduler/set",array('.id' => $sysschdlkey,'disabled'=>'yes'));
	}
	$chkmodem=$API->comm("/interface/print",array('.proplist' => 'name'));
	if(in_array_r("modem1", $chkmodem)){
		$API->comm("/interface/wireless/set",array('name' => 'modem1','disabled'=>'yes'));
	}
	if(in_array_r("modem2", $chkmodem)){
		$API->comm("/interface/wireless/set",array('name' => 'modem2','disabled'=>'yes'));		
	}
	//
	*/
	//$setting=$API->comm("/interface/wireless/set",array('.id' => 'wlan1','rate-set'=>'configured','basic-rates-b'=>'1Mbps','basic-rates-a/g'=>'6Mbps','supported-rates-b'=>'1Mbps','supported-rates-a/g'=>'6Mbps,24Mbps','distance'=>'indoors','noise-floor-threshold'=>'-90'));
	

	//Get Modem & Serial Port & Info Channel
	$usbport=array();
	$modemtype=array();
	$port=$API->comm("/port/print",false);
	//Step 1
	//Check what kind of modem are used
	foreach ($port as $key => $val) {
		if(strpos($val['name'], 'usb') !== false) {
			array_push($usbport, $val['name']);
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
		//step 3
		//check is router contain at least 820W modem
		if(in_array_r("Huawei 820W", $modemtype)){
			//echo "this router contain Huawei 820W";

		}else{
			echo "NO Huawei 820W leave it be";
		}

	}else{
		echo "different leave it be";
	}


	//echo "<pre>";
	//var_dump($rate);

	/*
	if(count($setting)==0){
		echo "OKAY";
		INSERT INTO `mnp_rack`.`mirkotik_wifi_improve` (`bus_id`, `group`, `ip`, `lastresultmsg`, `isokay`, `date`, `lastcheck`) VALUES ('bus', 'grou[p', 'ip', 'lastresult', 'okay', '2016-12-20 15:54:42', '2016-12-20 15:54:45');
	}else{
		echo "PROBLEM";
	}
	*/
	$API->disconnect();
}
?>