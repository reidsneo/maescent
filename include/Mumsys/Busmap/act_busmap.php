<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$busmap = array();
$MNPrack = new CentreonDB("mnp_rack");
$DBStorage = new CentreonDB("centstorage");

if(isset($_GET['method'])){
	if($_GET['method']=="getbusdata"){
		if(isset($_GET['act']) AND ($_GET['act']=="singlebus")){
			$defid=$_GET['hostid'];
			$singleq=" AND h.`name`='".$defid."'";
		}
		$qdtbus = $DBStorage->getAll("SELECT h.`host_id`,h.name AS hostname,hg.`hg_id` AS groupid,hg.`hg_name` AS hostgroup,h.address,h.alias FROM `centreon_storage`.`hosts` h, `centreon`.`hostgroup` hg,`centreon`.`hostgroup_relation` hgid WHERE hgid.host_host_id=h.`host_id` AND hgid.`hostgroup_hg_id`=hg.`hg_id` AND h.name!='Centreon-Server' AND h.name!='Cabang1' AND h.name!='Cabang2' AND h.name!='localrak-xxx19' AND h.name!='localrak-xxx242' AND h.name!='Arriva_Server' AND hg.`hg_id`='".$_GET['group_id']."'".$singleq.";");
		$i=-1;
		foreach ($qdtbus as $key => $bus) {
			$i=$i+1;
			$chkbus = $MNPrack->query("SELECT * from mum_busmap WHERE id_bus='".$bus['hostname']."' AND id_group='".$bus['groupid']."';");
			if($chkbus->numRows()=='0'){
				$MNPrack->query("INSERT INTO `mnp_rack`.`mum_busmap` (`id_bus`, `id_group`, `x`, `y`) VALUES ('".$bus['hostname']."', '".$bus['groupid']."', '1502', '66');");

			}else{
				while($row=$chkbus->fetchRow()){
					$x=$row['x'];
					$y=$row['y'];
					$montype=$row['type'];
					if($x==""){
						$x=1502;
					}
					if($y==""){
						$y=66;
					}
					$config=unserialize($row['config']);
				}
				$qswbus = $MNPrack->query("SELECT * from hw_general WHERE bus_id='".$bus['hostname']."';");
				$swbus = $qswbus->fetchRow();
			}
			if($bus['alias']=="maestronic-EBS" OR $bus['alias']=="maestronic-connexxion" OR $bus['alias']=="Poller2 Server" OR $bus['alias']=="Poller1 Server" OR $bus['alias']=="Monitoring Server" OR $bus['alias']=="Feeder Server") $bus['alias']="";
			if($bus['alias']!=""){
		        $dtnetwork=explode(" ", $bus['alias']);
		        $bnykdata=count($dtnetwork);
		        $networkdat =array();
		        for ($i = 0; $i < $bnykdata / 2; $i++){
		            $in = $i * 2;
		            $networkdat[$dtnetwork[$in]] = $dtnetwork[$in + 1];
		        }
		    }
		    if (array_key_exists('-a', $networkdat)) {$aton=$networkdat['-a'];}else{$aton=0;}
		    if (array_key_exists('-t', $networkdat)) {$tunnel=$networkdat['-t'];}else{$tunnel=0;}
		    if($montype!=0 AND $montype!=""){
		    	$data=$MNPrack->query("SELECT * from `mnp_rack`.`mum_bus_type` WHERE id_type='".$montype."';");
		    	$dtimg=$data->fetchRow();
		    	echo '<div id="'.$bus['hostname'].'" ty="'.$dtimg['nm_type'].'" idty="'.$dtimg['id_type'].'" hostnm="'.$bus['hostname'].'" hostid="'.$bus['host_id'].'" net="'.$bus['address'].'" class="bggray draggable ui-draggable tip  context-menu-one" style="position:absolute; left: '.$x.'px; top: '.$y.'px;"><div class="nm" class="mnpver">'.$bus['hostname'].'</div><center style="margin-left:5px;"><img src="/centreon/img/typeicon/'.$dtimg['img_name'].'" class="imgicon"></center><div class="al-'.$bus['hostname'].'"><span class="tipdata">
					<div class="tiphead">Bus '.$bus['hostname'].'</div>
					<div class="tipbody"></div>
				</span></div></div>';
		    }else{
		    	echo '<div id="'.$bus['hostname'].'" ty="Bus Router" idty="0" net="'.$bus['address'].'" aton="'.$aton.'" tunnel="'.$tunnel.'" hostnm="'.$bus['hostname'].'" hostid="'.$bus['host_id'].'" ssh="'.$config[r][ssh][u]."|".$config[r][ssh][pw]."|".$config[r][ssh][p].'" 
				ftp="'.$config[r][ftp][u]."|".$config[r][ftp][pw]."|".$config[r][ftp][p].'" 
				wb="'.$config[r][wb][u]."|".$config[r][wb][pw].'"
				class="bggray draggable ui-draggable tip  context-menu-one" style="position:absolute; left: '.$x.'px; top: '.$y.'px;">
				<div class="nm" class="mnpver">'.$bus['hostname'].'</div>';
				if($swbus['mnp_ver']!=""){
					echo '<div class="mnpver">CPU SW<br><b>'.$swbus['mnp_ver'].'</b></div>';
				}
				echo '
				<img class="wifiicon" width="20px" class="wifiicon">
				<div class="at bggray" 
				ssh="'.$config[a][ssh][u]."|".$config[a][ssh][pw]."|".$config[a][ssh][p].'" 
				ftp="'.$config[a][ftp][u]."|".$config[a][ftp][pw]."|".$config[a][ftp][p].'" 
				wb="'.$config[a][wb][u]."|".$config[a][wb][pw].'">A</div>
				<div class="tu bggray" 
				ssh="'.$config[t][ssh][u]."|".$config[t][ssh][pw]."|".$config[t][ssh][p].'" 
				ftp="'.$config[t][ftp][u]."|".$config[t][ftp][pw]."|".$config[t][ftp][p].'" 
				wb="'.$config[t][wb][u]."|".$config[t][wb][pw].'">T</div>
				<div class="al-'.$bus['hostname'].'"></div>
				<span class="tipdata">
					<div class="tiphead">Bus '.$bus['hostname'].'</div>
					<div class="tipbody"></div>
				</span>
				</div>';
		    }
			


		}
	}else if($_GET['method']=="savepos"){
		$busid=0;$groupid=0;$x=0;$y=0;
		$busid=$_GET['id'];
		$groupid=$_GET['groupid'];
		$x=$_GET['x'];
		$y=$_GET['y'];
		$MNPrack->query("UPDATE `mnp_rack`.`mum_busmap` SET `x` = '".$x."' , `y` = '".$y."' WHERE `id_bus` = '".$busid."' AND `id_group` = '".$groupid."';");
	}else if($_GET['method']=="isalivecheck"){
		$groupid=$_GET['groupid'];
		//$chkisalive=$DBStorage->getAll("(SELECT DISTINCT h.state, h.last_check, h.address, h.name,(SELECT COUNT(*) FROM `mnp_rack`.daily_alert WHERE bus_id=h.name AND statecheck LIKE '%".date("Y-m-d")."%') AS totalalert FROM  `centreon`.`hostgroup` hg,`centreon`.`hostgroup_relation` hgid, `centreon_storage`.`instances` i, `centreon_storage`.`hosts` h LEFT JOIN `centreon_storage`.`hosts_hosts_parents` hph ON hph.parent_id = h.host_id LEFT JOIN `centreon_storage`.`customvariables` cv ON (cv.host_id = h.host_id AND cv.service_id IS NULL AND cv.name = 'CRITICALITY_LEVEL') WHERE h.name NOT LIKE '_Module_%' AND h.instance_id = i.instance_id AND h.state = 0 AND h.enabled = 1 AND hgid.host_host_id=h.`host_id` AND hgid.`hostgroup_hg_id`=hg.`hg_id` AND hg.`hg_id`='".$groupid."') UNION ALL (SELECT DISTINCT h.state, h.last_check, h.address, h.name,(SELECT COUNT(*) FROM `mnp_rack`.daily_alert WHERE bus_id=h.name AND statecheck LIKE '%".date("Y-m-d")."%') AS totalalert FROM  `centreon`.`hostgroup` hg,`centreon`.`hostgroup_relation` hgid, `centreon_storage`.`instances` i, `centreon_storage`.`hosts` h LEFT JOIN `centreon_storage`.`hosts_hosts_parents` hph ON hph.parent_id = h.host_id LEFT JOIN `centreon_storage`.`customvariables` cv ON (cv.host_id = h.host_id AND cv.service_id IS NULL AND cv.name = 'CRITICALITY_LEVEL') WHERE h.name NOT LIKE '_Module_%' AND h.instance_id = i.instance_id AND h.state = 1 AND h.enabled = 1 AND hgid.host_host_id=h.`host_id` AND hgid.`hostgroup_hg_id`=hg.`hg_id` AND hg.`hg_id`='".$groupid."')");
		//echo json_encode($chkisalive);
		$chkisalert=$MNPrack->getAll("SELECT b.id_bus,(SELECT COUNT(id_alert) FROM `mnp_rack`.daily_alert WHERE bus_id=id_bus AND statecheck LIKE '%".date("Y-m-d")."%') as cntalert FROM mum_busmap b WHERE b.id_group='".$groupid."';");
		echo json_encode($chkisalert);
	}else if($_GET['method']=="ismodulealive"){
		$groupid=$_GET['groupid'];
		$chkismodalive=$MNPrack->getAll("SELECT bus_id,rut_state,aton_state,tun_state FROM daily_duration_public WHERE `date`='".date("Y-m-d")."';");
		echo json_encode($chkismodalive);
	}else if($_GET['method']=="iswifimon"){
		$groupid=$_GET['groupid'];
		$chkiswifimon=$MNPrack->getAll("SELECT DISTINCT b.id_bus FROM mum_busmap b,mum_hotspot_usage h WHERE id_group='".$groupid."' AND h.`bus_id`=b.`id_bus` AND `date` LIKE '%".date("Y-m-d")."%';");
		echo json_encode($chkiswifimon);
	}else if($_GET['method']=="lastdailyalert"){
		$busid=$_GET['busid'];
		$lastalert=$MNPrack->getAll("SELECT * FROM daily_alert WHERE bus_id='".$busid."' ORDER BY id_alert DESC LIMIT 1;");
		echo json_encode($lastalert);
	}else if($_GET['method']=="hardwareinfo"){
		$busid=$_GET['busid'];
		$geninfo=$MNPrack->getAll("SELECT * FROM hw_general WHERE bus_id='".$busid."';");
		$modinfo=$MNPrack->getAll("SELECT * FROM hw_module WHERE bus_id='".$busid."';");
		$taginfo=$MNPrack->getAll("SELECT * FROM hw_tag WHERE bus_id='".$busid."';");
		$combine = array('general' =>$geninfo,'modinfo' =>$modinfo,'taginfo' =>$taginfo);
		echo json_encode($combine);
	}else if($_GET['method']=="hardwareinfodebug"){
		$busid=$_GET['busid'];
		$geninfo=$MNPrack->getAll("SELECT * FROM hw_general WHERE bus_id='".$busid."';");
		$modinfo=$MNPrack->getAll("SELECT * FROM hw_module WHERE bus_id='".$busid."';");
		$taginfo=$MNPrack->getAll("SELECT * FROM hw_tag WHERE bus_id='".$busid."';");
		$combine = array('general' =>$geninfo,'modinfo' =>$modinfo,'taginfo' =>$taginfo);
		echo "<pre>";
		print_r($combine);
	}else if($_GET['method']=="sortgridasc"){		
		$group=$_GET['groupid'];
		$limitdown=15;
		$xoffset=96;//87
		$yoffset=95;//80
		$down=0;
		$orix=20;//20
		$oriy=81;//71
		$defy=81;
		$MNPrack = new CentreonDB("mnp_rack");
		$loadbusmap=$MNPrack->query("SELECT id_bus,x,y FROM mum_busmap WHERE id_group='".$group."';");
		while($row=$loadbusmap->fetchRow()){
				if($down==$limitdown){
					$down=0;
					$oriy=$defy;
					$orix=($orix+$xoffset);
				}
				if($down>0){
					$oriy=$oriy+$yoffset;
				}
				$MNPrack->query("UPDATE `mnp_rack`.`mum_busmap` SET `x` = '$orix' , `y` = '$oriy' WHERE `id_bus` = '".$row['id_bus']."';");
				$down++;
		}
	}else if($_GET['method']=="loadcfg"){
		$busid=$_GET['busid'];
		$typeid=$_GET['type'];
		$cfgtype=$_GET['cfgtype'];
		$bussql=$MNPrack->query("SELECT config FROM mum_busmap WHERE id_bus='$busid';");
		$buscfg=$bussql->fetchRow();
		$buscfg=unserialize($buscfg['config']);
		$buscfg=$buscfg[$typeid];
		$buscfg=$buscfg[$cfgtype];
		echo json_encode($buscfg);

	}else if($_GET['method']=="loadcfgdbg"){
		$busid=$_GET['busid'];
		$typeid=$_GET['type'];
		$cfgtype=$_GET['cfgtype'];
		$bussql=$MNPrack->query("SELECT config FROM mum_busmap WHERE id_bus='$busid';");
		$buscfg=$bussql->fetchRow();
		$buscfg=unserialize($buscfg['config']);
		//$buscfg=$buscfg[$typeid];
		//$buscfg=$buscfg[$cfgtype];
		echo "<pre>";
		//$buscfg['r']=array('ssh' =>array('u' =>'guest','pw' =>'gu35t','p' =>'2222'),'ftp' =>array('u' =>'guest','pw' =>'gu35t','p' =>'21'),'wb' =>array('u' =>'admin','pw' =>'k0nijn'));
		//$buscfg['a']=array('ssh' =>array('u' =>'guest','pw' =>'gu35t','p' =>'2222'),'ftp' =>array('u' =>'guest','pw' =>'gu35t','p' =>'21'),'wb' =>array('u' =>'admin','pw' =>'k0nijn'));
		//$buscfg['t']=array('ssh' =>array('u' =>'guest','pw' =>'gu35t','p' =>'2222'),'ftp' =>array('u' =>'guest','pw' =>'gu35t','p' =>'21'),'wb' =>array('u' =>'admin','pw' =>'k0nijn'));
		print_r($buscfg);//json_encode($buscfg);
		//echo serialize($buscfg);

	}else if($_GET['method']=="savecfg"){
		$busid=$_GET['busid'];
		$typeid=$_GET['type'];
		$cfgtype=$_GET['cfgtype'];
		$user=$_GET['uname'];
		$pass=$_GET['pwd'];
		$port=$_GET['port'];
		$bussql=$MNPrack->query("SELECT config FROM mum_busmap WHERE id_bus='$busid';");
		$buscfg=$bussql->fetchRow();
		$buscfg=unserialize($buscfg['config']);
		if($cfgtype=="ssh" OR $cfgtype=="ftp"){
			$buscfg[$typeid][$cfgtype]['u']=$user;
			$buscfg[$typeid][$cfgtype]['pw']=$pass;
			$buscfg[$typeid][$cfgtype]['p']=$port;
		}else if($cfgtype=="wb"){
			$buscfg[$typeid][$cfgtype]['u']=$user;
			$buscfg[$typeid][$cfgtype]['pw']=$pass;
		};
		echo print_r($buscfg);
		$savecfg=serialize($buscfg);
		$MNPrack->query("UPDATE `mnp_rack`.`mum_busmap` SET `config` = '".$savecfg."' WHERE id_bus='".$busid."';");
		//echo "UPDATE `mnp_rack`.`mum_busmap` SET `config` = '".$savecfg."' WHERE id_bus='".$busid."';";
	}else if($_GET['method']=="listtype"){
		$typesql=$MNPrack->getAll("SELECT * FROM mum_bus_type;");
		echo json_encode($typesql);
	}else if($_GET['method']=="savetype"){
		$groupid=$_GET['groupid'];
		$busid=$_GET['busid'];
		$type=$_GET['type'];
		$typesql=$MNPrack->query("UPDATE `mnp_rack`.`mum_busmap` SET `type` = '$type' WHERE `id_bus` = '$busid' AND `id_group` = '$groupid';");
		echo "UPDATE `mnp_rack`.`mum_busmap` SET `type` = '$type' WHERE `id_bus` = '$busid' AND `id_group` = '$groupid';";
	}
}else{


}


?>