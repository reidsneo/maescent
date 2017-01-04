<?php
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");

$group=67;
$limitdown=18;
$xoffset=87;
$yoffset=80;
$down=0;
$orix=20;
$oriy=71;
$defy=71;

$MNPrack = new CentreonDB("mnp_rack");
$loadbusmap=$MNPrack->query("SELECT id_bus,x,y FROM mum_busmap WHERE id_group='".$group."';");
while($row=$loadbusmap->fetchRow()){

		if($down==$limitdown){
			echo "====================================<br>";
			$down=0;
			$oriy=$defy;
			$orix=($orix+$xoffset);
		}
		if($down>0){
			$oriy=$oriy+$yoffset;
		}
		echo $row['id_bus']." ".$orix." ".$oriy."<br>";
		$MNPrack->query("UPDATE `mnp_rack`.`mum_busmap` SET `x` = '$orix' , `y` = '$oriy' WHERE `id_bus` = '".$row['id_bus']."';");

		//echo "UPDATE `mnp_rack`.`mum_busmap` SET `x` = '$orix' , `y` = '$oriy' WHERE `id_bus` = '".$row['id_bus']."' AND `id_group` = '76';<br>";
	$down++;
}
?>