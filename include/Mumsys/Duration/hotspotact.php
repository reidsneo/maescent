<?php
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
    if($row['nm_setting']=="hotspotcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="hotspotcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="hotspotofflinebus") $hotspotofflinebus=$row['val_setting'];
}
$method=$_POST['method'];
if($method=="hotmonitor"){
	if($hotspotmon==1){
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '0' WHERE `nm_setting` = 'hotspotcron';");
		echo "disabled";
	}else{
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '1' WHERE `nm_setting` = 'hotspotcron';");
		echo "enabled";
	}


}else if($method=="edtintval"){
	$edtintval=intval($_POST['value']);
	$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$edtintval' WHERE `nm_setting` = 'hotspotinterval';");
	echo "saved";


}else if($method=="hotoffbus"){
	if($hotspotofflinebus==1){
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '0' WHERE `nm_setting` = 'hotspotofflinebus';");
		echo "disabled";
	}else{
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '1' WHERE `nm_setting` = 'hotspotofflinebus';");
		echo "enabled";
	}
}

?>