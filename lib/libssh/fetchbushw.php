<?php
$busip=$_GET['busip'];
include('Net/SSH2.php');
$ssh = new Net_SSH2($busip,'2222');
if (!$ssh->login('guest', 'gu35t')) {
    exit('Failed fetch bus hardware');
}
function parsexml($dat){
    	$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $dat);
		$xml = simplexml_load_string($xml);
		$json = json_encode($xml);
		$responseArray = json_decode($json,true);
		return $responseArray;
}
$master=$ssh->exec('cat /opt/mnp/config/.masterConfig.xml');
$mnpversion=$ssh->exec('cat /opt/mnp/config/.mnp-version');
$modules=$ssh->exec('ls /mnt/maestronicOS/porteus/modules');
echo "MNP Ver : <b>".substr($mnpversion,8)."</b><br>";
preg_match_all('/-(.*?).xzm/s', $modules, $matches);
echo "Modules : (".count($matches[1]).")<b>";
foreach ($matches[1] as $key => $val) {
	echo "<br>".$val;
}
echo "</b><br>";
/*
echo "<pre>";
print_r(parsexml($master));
echo "</pre>";
function packet_handler($str)
{
    echo $str;
}

$ssh->exec('ping 172.30.241.167', 'packet_handler');
*/
?>