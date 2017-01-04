<?php
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
        if($row['nm_setting']=="emailcron") $emailcron=$row['val_setting'];
        if($row['nm_setting']=="emailcheckinterval") $emailcheckinterval=$row['val_setting'];
        if($row['nm_setting']=="emailnotifytime") $emailnotifytime=$row['val_setting'];
        if($row['nm_setting']=="emailtarget") $emailtarget=$row['val_setting'];
        if($row['nm_setting']=="emailcc") $emailcc=$row['val_setting'];
        if($row['nm_setting']=="emailbotuser") $emailbotuser=$row['val_setting'];
        if($row['nm_setting']=="emailbotpass") $emailbotpass=$row['val_setting'];
        if($row['nm_setting']=="emailtargetname") $emailtargetname=$row['val_setting'];
}
$method=$_POST['method'];
if($method=="emailcronmon"){
	if($emailcron==1){
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '0' WHERE `nm_setting` = 'emailcron';");
		echo "disabled";
	}else{
		$res=$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '1' WHERE `nm_setting` = 'emailcron';");
		echo "enabled";
	}


}else if($method=="saveconf"){
	$emailcheckinterval=intval($_POST['emailcheckinterval']);
	$emailnotifytime=$_POST['emailnotifytime'];
	$emailtargetname=$_POST['emailtargetname'];
	$emailtarget=$_POST['emailtarget'];
	$emailcc=$_POST['emailcc'];
	$emailbotuser=$_POST['emailbotuser'];
	$emailbotpass=$_POST['emailbotpass'];
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailcheckinterval' WHERE `nm_setting` = 'emailcheckinterval';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailnotifytime' WHERE `nm_setting` = 'emailnotifytime';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailtargetname' WHERE `nm_setting` = 'emailtargetname';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailtarget' WHERE `nm_setting` = 'emailtarget';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailcc' WHERE `nm_setting` = 'emailcc';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailbotuser' WHERE `nm_setting` = 'emailbotuser';");
	$MNPrack->query("UPDATE `mum_setting` SET `val_setting` = '$emailbotpass' WHERE `nm_setting` = 'emailbotpass';");
	echo "saved";
}

?>