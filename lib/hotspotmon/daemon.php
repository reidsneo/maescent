<?php
require_once "thread_class.php";
$pathToPhp = "/usr/bin/php";

require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$MNPrack = new CentreonDB("mnp_rack");
$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
    if($row['nm_setting']=="hotspotcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="hotspotinterval") $hotspotinterval=$row['val_setting'];
}

echo "--------------------------\n";
echo "      Hotspot Daemon    \n";
echo "  Interval is ".$hotspotinterval." Seconds\n";
echo "--------------------------\n";
while (true) {
    $datenow=date("Y-m-d H:i:s");
    $hotspotmon=$MNPrack->getOne("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotcron';");
    $hotspotinterval=$MNPrack->getOne("SELECT val_setting FROM mum_setting WHERE nm_setting='hotspotinterval';");
    if($hotspotmon==1){
        $busq=$MNPrack->query("SELECT * FROM mum_busdata WHERE bus_type='Connexxtion';");// only for Connexxtion bus
        $i=0;
        $busdat=array();
        while ($busdt =$busq->fetchRow()){
            $i=$i+1;
            $busid=$busdt['bus_id'];
            $busip=$busdt['bus_ip'];
            $busty=$busdt['bus_type'];
            ${'obj'.$i}= new Threader("$pathToPhp -e ../hotspotmon/CurlAction.php $busid $busip $busty",null,$busid);
            array_push($busdat,array('busid' => $busid,'busip' => $busip,'busty' => $busty));
        }
        $i=0;
        foreach ($busdat as $key => $val) {
            $i=$i+1;
            echo "[Bus ID ".${'obj'.$i}->threadName."] => " . ${'obj'.$i}->listen()."\n";
        }
    }else{
        echo "Hotspot Daemon is \033[31mDISABLED \033[0m\n";
    }
echo "interval :".$hotspotinterval."------------------------------------------------- ".$datenow."\n";
sleep($hotspotinterval);
}

?>