<?php
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require('RouterAPI.php');
$API = new RouterosAPI();
$API->debug = false;
$busid="sample";
$busgroup="samplegroup";
$busip="172.30.241.72";//3984
$date= date("Y-m-d H:i:s");
$today= date("Y-m-d");
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
			$chksysschdl=$API->comm("/system/scheduler/getall");
            //disable cleanup-modem
            if(in_array_r("cleanup-modem", $chksysschdl)){
                $sysschdlkey=$chksysschdl[recursive_array_search("cleanup-modem",$chksysschdl)]['.id'];
                $API->comm("/system/scheduler/set",array('.id' => $sysschdlkey,'disabled'=>'yes'));
            }

            //check how many modemx available and remove it
            $idmodem=array();
            $modem=array();
            $ppp=$API->comm("/interface/ppp-client/print",false);
            foreach ($ppp as $key => $val) {
                if(strpos($val['name'], 'modem') !== false) {
                    array_push($modem, $val['name']);
                    array_push($idmodem, $val['.id']);
                    $API->comm("/interface/ppp-client/remove",array('.id' => $val['.id']));
                }
            }

            $usbport=array();
            $modemtype=array();
            $modemch=array();
            $modem820=0;
            $port=$API->comm("/port/print",false);
            foreach ($port as $key => $val) {
                if(strpos($val['name'], 'usb') !== false) {
                    array_push($usbport, $val['name']);
                    array_push($modemch, $val['channels']);
                    if($val['channels']==3){
                        array_push($modemtype, "Huawei 770W");
                    }else if($val['channels']==4){
                        array_push($modemtype, "Huawei 820U");
                        $modem820=$modem820+1;
                    }else if($val['channels']==6){
                        array_push($modemtype, "Huawei 820W");
                        $modem820=$modem820+1;
                    }
                }
            }
            foreach ($usbport as $key => $val) {
                if($val=="usb2"){
                    $usb2=array();
                    $usb2['name']=$modemch[$key];
                    $usb2['ch']=$modemch[$key];
                    $usb2['type']=$modemtype[$key];
                }else if($val=="usb3"){
                    $usb3=array();
                    $usb3['name']=$modemch[$key];
                    $usb3['ch']=$modemch[$key];
                    $usb3['type']=$modemtype[$key];
                }
            }

            //Conditional Check
            if($usb2['type']=="Huawei 820W" && $usb3['type']=="Huawei 770W"){
                //if you detect 1 820W (USB2) and 1 770W (USB3) you need to add modem1 on USB2
                $result="add modem1 on USB2";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if($usb2['type']=="Huawei 770W" && $usb3['type']=="Huawei 820W"){
                //In case you detect 1 770W (USB2) and 1 820W (USB3) you need to add modem1 on USB3
                $result="add modem1 on USB3";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if($usb2['type']=="Huawei 820W" && $usb3['type']=="Huawei 820W"){
                //modem contain Huawei 820W,Huawei 820W add modem1 and modem2
                $result="add modem1 and modem2";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem2','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if(($usb2['type']=="Huawei 820W" || $usb2['type']=="Huawei 820U") && ($usb3['type']=="Huawei 820W" || $usb3['type']=="Huawei 820U")){
                //In case you detect 1 820W or 820U (USB2) and 1 820W or 820U (USB3) you need to add modem1 on USB2 and modem2 on USB3
                $result="add modem1 on USB2 and modem2 on USB3";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem2','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }
			$API->disconnect();
		}
?>