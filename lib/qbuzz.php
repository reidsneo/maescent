<?php
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(0);
date_default_timezone_set('Europe/Berlin');

require('RouterAPI.php');
$API = new RouterosAPI();
$API->debug = true;
$busid="sample";
$busgroup="samplegroup";
$busip="172.16.100.116";//"10.13.201.2";
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
			$sysident=$API->comm("/system/identity/print",false);
            $busid=$sysident[0]['name'];
            $wpapass="RemotE".$busid;
            $vpnuser="QBUZZ".$busid;
			echo "<pre>";
			$setting=$API->comm("/interface/l2tp-client/add",array('add-default-route' => 'no','connect-to'=>'95.128.6.53','dial-on-demand'=>'no','disabled'=>'no','max-mru'=>'1460','name'=>'MT','password'=>$wpapass,'profile'=>'default-encryption','user'=>$vpnuser));
            if(in_array_r("failure: already have device with such name",$setting)){
                echo "Setup [Ignored] ";
            }else{
                echo "Setup [Success] ";
                $iproute=$API->comm("/ip/route/add",array('disabled' => 'no','distance'=>'1','dst-address'=>'192.168.1.0/24','gateway'=>'MT'));
            }

            $chkfirewall=$API->comm("/ip/firewall/mangle/print",false);
            if(in_array_r("MT",$chkfirewall)){
                echo "Firewall [Ignored]";
            }else{
                $firewall=$API->comm("/ip/firewall/mangle/add",array('chain'=>'forward','protocol'=>'tcp','out-interface'=>'MT','passthrough'=>'yes'));
                $firewall2=$API->comm("/ip/firewall/mangle/add",array('chain'=>'forward','protocol'=>'tcp','in-interface'=>'MT','passthrough'=>'yes'));
                echo "Firewall [Success]";
            }
            //'action'=>'change-mss'
			$API->disconnect();
		}
?>