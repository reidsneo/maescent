<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$method=$_GET['method'];
$tommorow=date('Y-m-d', strtotime("+1 days"));
if($method=="crontask"){
	$byteinmax=$MNPrack->getOne("SELECT bus_bytein FROM mum_hotspot_usage WHERE bus_bytein!='N/A' ORDER BY id_mon DESC LIMIT 1;");
	if($byteinmax==""){$byteinmax=0;}
	$byteoutmax=$MNPrack->getOne("SELECT bus_byteout FROM mum_hotspot_usage WHERE bus_byteout!='N/A' ORDER BY id_mon DESC LIMIT 1;");
	if($byteoutmax==""){$byteoutmax=0;}
	$totdata=$MNPrack->getOne("SELECT bus_totdata FROM mum_hotspot_usage WHERE bus_totdata!='N/A' ORDER BY id_mon DESC LIMIT 1;");
	if($totdata==""){$totdata=0;}
	$hotspotinterval=$MNPrack->getOne("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotinterval';");
	echo $byteinmax.",".$byteoutmax.",".$totdata.",".$hotspotinterval;
}else{
	$sql="SELECT bus_id,rut_updur,rut_downdur,aton_updur,aton_downdur,tun_updur,tun_downdur,wifi_updur,`date` FROM daily_duration_public WHERE date NOT LIKE '%".$tommorow."%' ORDER BY bus_id";
	$res=$MNPrack->query($sql);

	//echo "<pre>";
	echo '{
	    "data": [';
	    $arr = array();
	    $i=0;
	    $maxrow=$res->numRows();
	while ($row =$res->fetchRow()) {
		$i=$i+1;

	   echo json_encode($row);
		if($i!=$maxrow){
			echo ",";
		}
	}
	echo ' ]
	}';
	//echo "</pre>";
}
?>