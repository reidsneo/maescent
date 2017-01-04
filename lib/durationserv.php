<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

if(isset($argv[1]))
  $idbus = $argv[1];
if(isset($argv[2]))
  $currouter = $argv[2]; 
if(isset($argv[3]))
  $curaton = $argv[3];
if(isset($argv[4]))
  $curtunnel = $argv[4];
if(isset($curaton)){
	if($curaton=="na"){
		$curaton="";
	}
}
if(isset($curtunnel)){
	if($curtunnel=="na"){
		$curtunnel="";
	}
}
	//FORMAT

	//file.php busid routerresult atonresult tunnelresult
	
	//require_once "centreonDuration.class.php";
	$currouter=$_GET['currouter'];
	$curaton=$_GET['curaton'];
	$curtunnel=$_GET['curtunnel'];
	$idbus=1111;
	$username = "root";
	$password = "toor";
	$hostname = "192.168.87.71";
	$output="";
	$date= date("Y-m-d H:i:s");
	$today= date("Y-m-d");
	$yesterday=date('Y-m-d', strtotime("-1 days"));
	$tommorow=date('Y-m-d', strtotime("+1 days"));
	$timenow=time();
	$redalert=@mysql_connect($hostname, $username, $password);
	mysql_select_db("mnp_rack");

function sum_duration($first,$seconds){
		$times = array($first,$seconds);
		$sum = $arrayName = array('h'=>0,'m'=>0,'s'=>0 );
		foreach( $times as $time ) {
		    @list($h,$m,$s) = explode(':',$time);
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


	$qisexist=mysql_fetch_array(mysql_query("SELECT count(bus_id) as `isexistdur` FROM daily_duration_public WHERE bus_id='$idbus' AND `date`='$tommorow';"));
	if($qisexist['isexistdur']==0){
		/*mysql_query("
			INSERT INTO `mnp_rack`.`daily_duration_public` (`bus_id`, `rut_updur`, `rut_downdur`, `rut_state`,
						`aton_updur`, `aton_downdur`, `aton_state`,
						`tun_updur`, `tun_downdur`, `tun_state`, `wifi_updur`, `first_check`, `last_check`, `date`) 
				 VALUES('$idbus', '0', '0', '',
				  '0', '0', '',
				  '0', '0', '','0', '$date', '$date', '$tommorow');");*/
	}
	//check data inside today duration fetch aton & router duration from db
	$chkdurasi=mysql_fetch_array(mysql_query("SELECT * from daily_duration_public WHERE bus_id='$idbus' AND `date`='$today';"));
	$rutupduration=$chkdurasi['rut_updur'];
	$rutdownduration=$chkdurasi['rut_downdur'];
	$atonupduration=$chkdurasi['aton_updur'];
	$atondownduration=$chkdurasi['aton_downdur'];
	$tunnelupduration=$chkdurasi['tun_updur'];
	$tunneldownduration=$chkdurasi['tun_downdur'];

	if($currouter!=$chkdurasi['rut_state']){
		//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_state` = '$currouter' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}
	if($curaton!=$chkdurasi['aton_state']){
		//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_state` = '$curaton' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}
	if($curtunnel!=$chkdurasi['tun_state']){
		//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_state` = '$curtunnel' WHERE `bus_id` = '$idbus' AND `date`='$today';");
	}

		echo 'R';
		if($chkdurasi['rut_state']==""){
			if($currouter=="OK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_updur` = '".sum_duration($uptimeinterval,$rutupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo ' duration '.sum_duration($uptimeinterval,$rutupduration);
			}elseif($currouter=="NOK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_downdur` = '".sum_duration($uptimeinterval,$rutdownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo ' duration '.sum_duration($uptimeinterval,$rutdownduration);
			}
		}else{
			if($currouter!=$chkdurasi['rut_state']){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($currouter=="OK"){
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_downdur` = '".sum_duration($uptimeinterval,$rutdownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo sum_duration($uptimeinterval,$rutdownduration);
				}else{
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_updur` = '".sum_duration($uptimeinterval,$rutupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo sum_duration($uptimeinterval,$rutupduration);
				}
			}else{
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($currouter=="OK"){
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_updur` = '".sum_duration($uptimeinterval,$rutupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo sum_duration($uptimeinterval,$rutupduration);
				}else{
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `rut_downdur` = '".sum_duration($uptimeinterval,$rutdownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$rutdownduration);
				}
			}
		}

		echo ',A';
		if($chkdurasi['rut_state']==""){
			if($curaton=="OK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_updur` = '".sum_duration($uptimeinterval,$atonupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo " ".sum_duration($uptimeinterval,$atonupduration);
			}elseif($curaton=="NOK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_downdur` = '".sum_duration($uptimeinterval,$atondownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo " ".sum_duration($uptimeinterval,$atondownduration);
			}
		}else{
			if($curaton!=$chkdurasi['aton_state']){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($curaton=="OK"){
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_downdur` = '".sum_duration($uptimeinterval,$atondownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$atondownduration);
				}else{
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_updur` = '".sum_duration($uptimeinterval,$atonupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$atonupduration);
				}
			}else{
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($curaton=="OK"){
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_updur` = '".sum_duration($uptimeinterval,$atonupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$atonupduration);
				}else{
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `aton_downdur` = '".sum_duration($uptimeinterval,$atondownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$atondownduration);
				}
			}
		}
		
		echo ',T';
		if($chkdurasi['rut_state']==""){
			if($curtunnel=="OK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_updur` = '".sum_duration($uptimeinterval,$tunnelupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo " ".sum_duration($uptimeinterval,$tunnelupduration);
			}elseif($curtunnel=="NOK"){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_downdur` = '".sum_duration($uptimeinterval,$tunneldownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
				echo " ".sum_duration($uptimeinterval,$tunneldownduration);
			}
		}else{
			if($curtunnel!=$chkdurasi['tun_state']){
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($curtunnel=="OK"){
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_downdur` = '".sum_duration($uptimeinterval,$tunneldownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$tunneldownduration);
				}else{
					echo "f";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_updur` = '".sum_duration($uptimeinterval,$tunnelupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$tunnelupduration);
				}
			}else{
				$c = new DateTime($chkdurasi['last_check']); 
				$d = new DateTime(date("H:i:s"));
				$sessionuptime = $c->diff($d);
				$uptimeinterval=$sessionuptime->format("%H:%I:%s");
				if($curtunnel=="OK"){
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_updur` = '".sum_duration($uptimeinterval,$tunnelupduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$tunnelupduration);
				}else{
					echo "n";
					//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `tun_downdur` = '".sum_duration($uptimeinterval,$tunneldownduration)."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
					echo " ".sum_duration($uptimeinterval,$tunneldownduration);
				}
			}
		}

		//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `last_check` = '$date' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		$chkwifi=mysql_fetch_array(mysql_query("SELECT bus_uptime,`date` FROM `mnp_rack`.`mum_hotspot_usage` WHERE bus_id='$idbus' AND flag='RES' AND `date` LIKE '%".$today."%' ORDER BY `DATE` DESC LIMIT 1"));
		if($chkwifi['bus_uptime']==""){
			$chkwifi['bus_uptime']="00:00:00";
		}
		//mysql_query("UPDATE `mnp_rack`.`daily_duration_public` SET `wifi_updur` = '".$chkwifi['bus_uptime']."' WHERE `bus_id` = '$idbus' AND `date`='$today';");
		echo ',wifi: '.$chkwifi['bus_uptime'];

		$lastq=mysql_fetch_array(mysql_query("SELECT id_mon FROM mum_hotspot_usage WHERE bus_id='9223' AND `date` LIKE '%2016-11-29%' ORDER BY `date` DESC LIMIT 1"));
		$idlast=$lastq['id_mon'];
		echo "UPDATE `mnp_rack`.`daily_duration_public` SET `flag` = 'RES' WHERE `id_mon`='$idlast';";
?>