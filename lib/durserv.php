<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);
date_default_timezone_set('Europe/Berlin');

if(isset($argv[1]))
  $idbus = $argv[1];
if(isset($argv[2]))
  $currouter = $argv[2]; 
if(isset($argv[3]))
  $curaton = $argv[3];
if(isset($argv[4]))
  $curtunnel = $argv[4];

if($curaton=="na"){
	$curaton="";
}
if($curtunnel=="na"){
	$curtunnel="";
}
	//FORMAT

	//file.php busid routerresult atonresult tunnelresult
	
	//require_once "centreonDuration.class.php";
	$currouter=$_GET['currouter'];
	$curaton=$_GET['curaton'];
	$curtunnel=$_GET['curtunnel'];
	$idbus=$_GET['idbus'];
	$username = "root";
	$password = "toor";
	$hostname = "192.168.87.10";
	$output="";
	$date= date("Y-m-d H:i:s");
	$today= date("Y-m-d");
	$timenow=time();
	$redalert=mysql_connect($hostname, $username, $password);
	mysql_select_db("mnp_rack");

function sum_duration($first,$seconds){
		$times = array($first,$seconds);
		$sum = $arrayName = array('h'=>0,'m'=>0,'s'=>0 );
		foreach( $times as $time ) {
		    list($h,$m,$s) = explode(':',$time);
		    $sum['h'] += $h;
		    $sum['m'] += $m;
		    $sum['s'] += $s;
		}
		$sum['m'] += floor($sum['s']/60);
		$sum['h'] += floor($sum['m']/60);
		$sum['s'] = $sum['s']%60;
		$sum['m'] = $sum['m']%60;
		return implode(':',$sum);
}


	$qisexist=mysql_fetch_array(mysql_query("SELECT count(bus_id) as `isexistdur` FROM daily_duration WHERE bus_id='$idbus' AND `date`='$today';"));
	if($qisexist['isexistdur']==0){
		//create new daily duration if there is no data today
		//mysql_query("INSERT INTO `mnp_rack`.`daily_duration` (`bus_id`, `first_check`, `last_check`, `aton_updur`, `aton_downdur`, `aton_state`, `aton_lastdown`, `aton_lastup`, `tun_updur`, `tun_downdur`, `tun_state`, `tun_lastdown`, `tun_lastup`, `wifi_updur`, `wifi_state`, `date`) VALUES ('$idbus', '$date', '$date', '0', '0', '', '$date', '$date', '0', '0', '', '$date', '$date', '0', '0', '$today');");
		mysql_query("
			INSERT INTO `mnp_rack`.`daily_duration` (`bus_id`, `rut_updur`, `rut_downdur`, `rut_state`,
						`rut_lastdown`, `rut_lastup`, `aton_updur`, `aton_downdur`, `aton_state`, `aton_lastdown`,
						`aton_lastup`, `tun_updur`, `tun_downdur`, `tun_state`, `tun_lastdown`, `tun_lastup`, `wifi_updur`, `date`) 
				 VALUES('$idbus', '0', '0', '',
				  '$date', '$date', '0', '0', '','$date',
				  '$date', '0', '0', '', '$date', '$date', '0', '$today');");
	}
	
	//check data inside today duration fetch aton & router duration from db
	$chkdurasi=mysql_fetch_array(mysql_query("SELECT * from daily_duration WHERE bus_id='$idbus' AND `date`='$today';"));
	$rutupduration=$chkdurasi['rut_updur'];
	$rutdownduration=$chkdurasi['rut_downdur'];
	$atonupduration=$chkdurasi['aton_updur'];
	$atondownduration=$chkdurasi['aton_downdur'];
	$tunnelupduration=$chkdurasi['tun_updur'];
	$tunneldownduration=$chkdurasi['tun_downdur'];

	//router handler
	$chkrouter=mysql_fetch_array(mysql_query("SELECT rut_state,date_chk FROM dur_router_log WHERE id_bus='$idbus' ORDER BY date_chk DESC LIMIT 1"));
	if($currouter!=$chkrouter['rut_state']){
		mysql_query("INSERT INTO `mnp_rack`.`dur_router_log` (`id_bus`, `rut_state`, `date_chk`) VALUES ('$idbus', '$currouter', '$date');");
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `rut_state` = '$currouter' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}else{
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `rut_lastup` = '$date',`rut_lastdown` = '$date' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}

	//aton handler
	$chkaton=mysql_fetch_array(mysql_query("SELECT aton_state,date_chk FROM dur_aton_log WHERE id_bus='$idbus' ORDER BY date_chk DESC LIMIT 1"));
	if($curaton!=$chkaton['aton_state']){
		mysql_query("INSERT INTO `mnp_rack`.`dur_aton_log` (`id_bus`, `aton_state`, `date_chk`) VALUES ('$idbus', '$curaton', '$date');");
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `aton_state` = '$curaton' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}else{
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `aton_lastup` = '$date',`aton_lastdown` = '$date' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}

	//tunnel handler
	$chktunnel=mysql_fetch_array(mysql_query("SELECT tunnel_state,date_chk FROM dur_tunnel_log WHERE id_bus='$idbus' ORDER BY date_chk DESC LIMIT 1"));
	if($curtunnel!=$chktunnel['tunnel_state']){
		mysql_query("INSERT INTO `mnp_rack`.`dur_tunnel_log` (`id_bus`, `tunnel_state`, `date_chk`) VALUES ('$idbus', '$curtunnel', '$date');");
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `tun_state` = '$curtunnel' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}else{
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `tun_lastup` = '$date',`tun_lastdown` = '$date' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}



echo '{
	"router": [{"state": "'.$currouter.'",';
	if($currouter=="OK"){
		$c = new DateTime($chkdurasi['rut_lastup']); 
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$rutupduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `rut_updur` = '".sum_duration($uptimeinterval,$rutupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$rutupduration).'"';
	}elseif($currouter=="NOK"){
		$c = new DateTime($chkdurasi['rut_lastdown']); 
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$rutdownduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `rut_downdur` = '".sum_duration($uptimeinterval,$rutdownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$rutdownduration).'"';
	}
		echo '}],';




if($curaton!=""){
	echo '
	"aton": [{"state": "'.$curaton.'",';

	if($curaton=="OK"){
		$c = new DateTime($chkdurasi['aton_lastup']); 
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$atonupduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `aton_updur` = '".sum_duration($uptimeinterval,$atonupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$atonupduration).'"';
	}elseif($curaton=="NOK"){
		$c = new DateTime($chkdurasi['aton_lastdown']); 
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$atondownduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `aton_downdur` = '".sum_duration($uptimeinterval,$atondownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$atondownduration).'"';
	}
		echo '}],';

}

if($curtunnel!=""){
echo '
	"tunnel": [{"state": "'.$curtunnel.'",';

	if($curtunnel=="OK"){
		$c = new DateTime($chkdurasi['tun_lastup']); 
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$tunnelupduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `tun_updur` = '".sum_duration($uptimeinterval,$tunnelupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$tunnelupduration).'"';
	}elseif($curtunnel=="NOK"){
		$c = new DateTime($chkdurasi['tun_lastdown']);
		$d = new DateTime(date("H:i:s"));
		$sessionuptime = $c->diff($d);
		$uptimeinterval=$sessionuptime->format("%H:%I:%s");
		//echo "<br>".$uptimeinterval;
		//echo "<br>".$tunneldownduration."<br>";
		mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `tun_downdur` = '".sum_duration($uptimeinterval,$tunneldownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo '"duration": "'.sum_duration($uptimeinterval,$tunneldownduration).'"';
	}

	echo '}],';
}

	$chkwifi=mysql_fetch_array(mysql_query("SELECT bus_uptime FROM mum_hotspot_usage WHERE bus_id='$idbus' AND `date` LIKE '%".$today."%' ORDER BY `date` DESC,bus_uptime DESC LIMIT 1;"));

	if($chkwifi['bus_uptime']==""){
		$chkwifi['bus_uptime']="00:00:00";
	}
	mysql_query("UPDATE `mnp_rack`.`daily_duration` SET `wifi_updur` = '".$chkwifi['bus_uptime']."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
echo '
	"wifi": [{"uptime": "'.$chkwifi['bus_uptime'].'"}]
}';

?>