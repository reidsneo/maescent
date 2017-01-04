<?php
require_once "/usr/share/centreon/www/include/common/common-Func.php";
require_once("/usr/share/centreon/www/class/centreonDB.class.php");
$DBStorage = new CentreonDB("centreon");
$MNPrack = new CentreonDB("mnp_rack");

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

if(isset($_POST['method'])){
	$method=$_POST['method'];
}else{
	$method="default";
}
if($method=="selbusid"){
$hostgroup=$_POST['hgroup'];
	$busconcess=$DBStorage->getAll("SELECT SQL_CALC_FOUND_ROWS DISTINCT h.host_name AS hostname,hg.`hg_name` AS hostgroup,host_address AS address FROM `centreon`.host h, `centreon`.hostgroup_relation hr,`centreon`.hostgroup hg,`centreon`.`nagios_server` ns,`centreon`.`ns_host_relation` nsr WHERE host_register = '1' AND h.host_id = hr.host_host_id AND hr.`hostgroup_hg_id`=hg.`hg_id` AND ns.`id`=nsr.`nagios_server_id` AND h.`host_id`=nsr.`host_host_id` AND hg.`hg_name`='".$hostgroup."' ORDER BY h.host_name;");
	foreach ($busconcess as $key => $val) {
		echo "<option value='".$val['address']."'>".$val['hostname']."</option>";
	}
}else if($method=="exescan"){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors',1);
	error_reporting(0);
	$busid=$_POST['busid'];
	$busip=$_POST['busip'];
	$hostgroup=$_POST['hgroup'];
	date_default_timezone_set('Europe/Berlin');
	require_once('konektor.php');
	require_once('Net/SSH2.php');
	$datenow=date("Y-m-d H:m:s");
	$ssh = new Net_SSH2($busip,'2222');
	if (!$ssh->login('guest', 'gu35t')) {
		$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`tool_hardwarecheck` (`groupid`, `busid`, `status`, `date`) VALUES ('".$hostgroup."', '".$busid."', 'NOK', '".$datenow."');");
	    die("NOK\n");
	}
	function parsexml($dat){
	    	$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $dat);
			$xml = simplexml_load_string($xml);
			$json = json_encode($xml);
			$responseArray = json_decode($json,true);
			return $responseArray;
	}
	$uname=$ssh->exec('uname -a');
	$uname = explode('#', $uname);
	$uname = $uname[0];
	$busid=$ssh->exec('cat /opt/mnp/config/.busid.bid');
	$busid=($busid*1);
	$master=$ssh->exec('cat /opt/mnp/config/.masterConfig.xml');
	$master=parsexml($master);

	$mnpversion=$ssh->exec('cat /opt/mnp/config/.mnp-version');
	$mnpversion=str_replace("version MNP.AIO.A.", "", $mnpversion);
	$hardisk=$ssh->exec('df -h');
	$i2c=$ssh->exec('cat /opt/mnp/LOG/I2C/i2c_log.xml');
	$i2c = preg_replace('/[^[:print:]]/', '', $i2c);
	$i2c=parsexml($i2c);
	$modules=$ssh->exec('ls /mnt/maestronicOS/porteus/modules');

	$appinfobin=$ssh->exec('ls -ln /opt/mnp/bin |grep "\->" | sed -e"s/ \+/ /g" | cut -d" " -f 9-');
	$appinfosek=$ssh->exec('ls -ln /opt/mnp/sekerip |grep "\->" | sed -e"s/ \+/ /g" | cut -d" " -f 9-');
	$mod="";
	preg_match_all('/-(.*?).xzm/s', $modules, $matches);
	foreach ($matches[1] as $key => $val) {
		$mod=$mod.$val.",";
	}
	$mod=substr($mod,0,strlen($mod)-1);

	$dat=explode("\n",trim($appinfobin));
	$appinfobin="";
	foreach ($dat as $key => $val) {
		$key=explode("->",$val);
		$appinfobin=$appinfobin.str_replace("/opt/mnp/bin/","",$key[1]).",";
	}

	$dat=explode("\n",trim($appinfosek));
	$appinfosek="";
	foreach ($dat as $key => $val) {
		$key=explode("->",$val);
		$appinfosek=$appinfosek.$key[1].",";
	}
	$cameralist=$master['mnp']['camera_rec'];
	$cameralist=explode(";",$cameralist);
	$cameracnt=count($cameralist);

	$tftlist=$master['mnp']['tft_cfg'];
	$tftlist=explode(";",$tftlist);
	$tftlistdat="";
	foreach ($tftlist as $key => $val) {
		$tftflg=explode("=",$val);
		if($tftflg[1]=="ON"){
			$tftlistdat=$tftlistdat.$tftflg[0].",";
		}
	}
	$tftlistdat=substr($tftlistdat,0,strlen($tftlistdat)-1);


	$dattfton=0;
	foreach ($tftlist as $key => $val) {
		$tfton=explode("=",$val);
		if($tfton[1]=="ON"){
			$dattfton=($dattfton+1);
		}
	}

	$tftypedat="";
	$tfttype=$master['mnp']['tft_type'];
	$tfttype=explode(";",$tfttype);
	for ($it=0; $it < $dattfton ; $it++) {
		$tftypelst=explode("=",$tfttype[$it]);
		$tftypedat=$tftypedat.$tftypelst[1].",";
	}
	$tftypedat=substr($tftypedat,0,strlen($tftypedat)-1);
	function notarray($val){
		if(!is_array($val)){
			return $val;
		}else{
			return "";
		}
	}

	$confcamrec=$master['mnp']['camera_rec'];
	$confcamstrlive=$master['mnp']['camera_stream_livetft'];
	$confcamremote=$master['mnp']['camera_stream_liveremote'];
	$confslidetoplay=$master['mnp']['slidetoplay'];
	$conflayout=$master['mnp']['layout'];
	$confbrand=$master['mnp']['Brand'];
	$confpftsts=$master['mnp']['pft_sts'];
	$confpavport=$master['mnp']['pft_pav_port'];
	$confreclength=$master['mnp']['record_length'];
	$confntpaddr=$master['mnp']['ntp_addr'];
	$conffbcap=$master['mnp']['fb_capture'];
	$confsnmpsrv=$master['mnp']['snmp_server'];


	$sqlquery=mysql_query("UPDATE `mnp_rack`.`hw_module` SET `active` = '0' WHERE `bus_id` = '".$busid."';");
	foreach ($i2c as $slot => $val){
		if (strpos($slot, 'RackID') !== false) {
			foreach ($i2c[$slot] as $key => $moddtl) {			
				if($key=="Solution_ID"){
					$tagsolid=notarray($moddtl);
				}else if($key=="Function_ID"){
					$tagfuncid=notarray($moddtl);
				}else if($key=="Module_ID"){
					$tagmodid=notarray($moddtl);
				}else if($key=="HW_Version"){
					$taghwver=notarray($moddtl);
				}else if($key=="SN"){
					$tagserialno=notarray($moddtl);
				}else if($key=="Proj_ID"){
					$tagprojid=notarray($moddtl);
				}else if($key=="Prod_Date"){
					$tagproddate=notarray($moddtl);
				}else if($key=="Lotno"){
					$taglotno=notarray($moddtl);
				}else if($key=="Age_Hour"){
					$taglotage=notarray($moddtl);
				}else if($key=="Fqc_Manager"){
					$tagfcqman=notarray($moddtl);
				}else if($key=="Fqc_Date"){
					$tagfcqdate=notarray($moddtl);
				}else if($key=="Oper_Hour"){
					$tagoperhr=notarray($moddtl);
				}else if($key=="Oper_ID"){
					$tagoperid=notarray($moddtl);
				}else if($key=="Concess"){
					$tagconcess=notarray($moddtl);
				}else if($key=="Plate"){
					$tagplate=notarray($moddtl);
				}else if($key=="Commdt"){
					$tagcommdt=notarray($moddtl);
				}else if($key=="Commby"){
					$tagcommby=notarray($moddtl);
				}
			}
		}

		if (strpos($slot, 'Slot') !== false) {
	    	if(!is_array($val['Solution_ID'])){
	    		$i=0;
	    		$bnyk=count($i2c[$slot]);
				$solid="";
				$funcid="";
				$modid="";
				$hwver="";
				$serialno="";
				$swA="";
				$swB="";
				$swC="";
				$projid="";
				$prodate="";
				$lotno="";
				$agingh="";
				$fqcman="";
				$fqcdate="";
				$temp="";
				$eeprom="";
				$iodev="";
				$dipswitch="";
				$v12c="";
				$v12v="";
				$v3v="";
				$v33v="";
				$v5="";
				$v24v="";
				$v24c="";
				$alrm="";
				$mcuv="";
				$cputft="";
				$tftcfg="";
				$tftstat="";
	    		foreach ($i2c[$slot] as $key => $moddtl) {
	    			$i++;	
	    			if($key=="Solution_ID"){
	    				$solid=notarray($moddtl);
	    			}else if($key=="Function_ID"){
	    				$funcid=notarray($moddtl);
	    			}else if($key=="Module_ID"){
	    				$modid=notarray($moddtl);
	    			}else if($key=="HW_Version"){
	    				$hwver=notarray($moddtl);
	    			}else if($key=="Serial_No"){
	    				$serialno=notarray($moddtl);
	    			}else if($key=="SW_Version_A"){
	    				$swA=notarray($moddtl);
	    			}else if($key=="SW_Version_B"){
	    				$swB=notarray($moddtl);
	    			}else if($key=="SW_Version_C"){
	    				$swC=notarray($moddtl);
	    			}else if($key=="Project_ID"){
	    				$projid=notarray($moddtl);
	    			}else if($key=="Prod_Date"){
	    				$prodate=notarray($moddtl);
	    			}else if($key=="Lotno"){
	    				$lotno=notarray($moddtl);
	    			}else if($key=="Aging_Hour"){
	    				$agingh=notarray($moddtl);
	    			}else if($key=="FQC_Manage"){
	    				$fqcman=notarray($moddtl);
	    			}else if($key=="FQC_Date"){
	    				$fqcdate=notarray($moddtl);
	    			}else if($key=="Temp"){
	    				$temp=notarray($moddtl);
	    			}else if($key=="Eeprom"){
	    				$eeprom=notarray($moddtl);
	    			}else if($key=="IO_Device"){
	    				$iodev=notarray($moddtl);
	    			}else if($key=="Dip_Switch"){
	    				$dipswitch=notarray($moddtl);
	    			}else if($key=="v12v_current"){
	    				$v12c=notarray($moddtl);
	    			}else if($key=="v12v_voltage"){
	    				$v12v=notarray($moddtl);
	    			}else if($key=="volt_3v"){
	    				$v3v=notarray($moddtl);
	    			}else if($key=="volt_3_3v"){
	    				$v33v=notarray($moddtl);
	    			}else if($key=="volt_5v"){
	    				$v5=notarray($moddtl);
	    			}else if($key=="in_24v_voltage"){
	    				$v24v=notarray($moddtl);
	    			}else if($key=="in_24v_current"){
	    				$v24c=notarray($moddtl);
	    			}else if($key=="ALARM"){
	    				$alrm=notarray($moddtl);
	    			}else if($key=="MCU_VER"){
	    				$mcuv=notarray($moddtl);
	    			}else if($key=="CPU_TFT"){
	    				$cputft=notarray($moddtl);
	    			}else if($key=="TFT_CFG"){
	    				$tftcfg=notarray($moddtl);
	    			}else if($key=="TFT_STATUS"){
	    				$tftstat=notarray($moddtl);
	    			}
	    			
	    		}
	    			$ismod=mysql_fetch_array(mysql_query("SELECT COUNT(bus_id) AS `chkmod` FROM `mnp_rack`.`hw_module` WHERE slot='".substr($slot,4)."' AND bus_id='".$busid."';"));
		    		if($ismod['chkmod']>0){
		    			$sqlquery=mysql_query("UPDATE `mnp_rack`.`hw_module` SET `sol_id` = '".$solid."' , `func_id` = '".$funcid."' , `mod_id` = '".$modid."' , `hw_ver` = '".$hwver."' , `sn` = '".$serialno."' , `sw_verA` = '".$swA."' , `sw_verB` = '".$swB."' , `sw_verC` = '".$swC."' , `proj_id` = '".$projid."' , `prd_date` = '".$prodate."' , `lotno` = '".$lotno."' , `aging_hour` = '".$agingh."' , `fqc_man` = '".$fqcman."' , `fqc_date` = '".$fqcdate."' , `temp` = '".$temp."' , `eeprom` = '".$eeprom."' , `v12v_cur` = '".$v12c."' , `v12v_volt` = '".$v12v."' , `volt_3v` = '".$v3v."' , `volt_3_3v` = '".$v33v."' , `volt_5v` = '".$v5."' , `in_24v_cur` = '".$v24c."' , `in_24v_volt` = '".$v24v."' , `alarm` = '".$alrm."' , `mcu_ver` = '".$mcuv."' , `io_device` = '".$iodev."' , `dip_switch` = '".$dipswitch."' , `cpu_tft` = '".$cputft."' , `tft_cfg` = '".$tftcfg."' , `tft_stat` = '".$tftstat."' , `date` = '".$datenow."' , `active` = '1' WHERE `bus_id` = '".$busid."' AND `slot` = '".substr($slot,4)."';");
		    		}else{
		    			$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`hw_module` (`bus_id`, `sol_id`, `slot`, `func_id`, `mod_id`, `hw_ver`, `sn`, `sw_verA`, `sw_verB`, `sw_verC`, `proj_id`, `prd_date`, `lotno`, `aging_hour`, `fqc_man`, `fqc_date`, `temp`, `eeprom`, `v12v_cur`, `v12v_volt`, `volt_3v`, `volt_3_3v`, `volt_5v`, `in_24v_cur`, `in_24v_volt`, `alarm`, `mcu_ver`, `io_device`, `dip_switch`, `cpu_tft`, `tft_cfg`, `tft_stat`, `date`, `active`) VALUES ('".$busid."', '".$solid."', '".substr($slot,4)."', '".$funcid."', '".$modid."', '".$hwver."', '".$serialno."', '".$swA."', '".$swB."', '".$swC."', '".$projid."', '".$prodate."', '".$lotno."', '".$agingh."', '".$fqcman."', '".$fqcdate."', '".$temp."', '".$eeprom."', '".$v12c."', '".$v12v."', '".$v3v."', '".$v33v."', '".$v5."', '".$v24c."', '".$v24v."', '".$alrm."', '".$mcuv."', '".$iodev."', '".$dipswitch."', '".$cputft."', '".$tftcfg."', '".$tftstat."', '".$datenow."', '1');");
		    		}
	    	}
		}
	}
	$appinfo=$appinfobin.$appinfosek;
	$appinfo=substr($appinfo,0,strlen($appinfo)-1);

		$isgen=mysql_fetch_array(mysql_query("SELECT COUNT(bus_id) AS `chkgen` FROM `mnp_rack`.`hw_general` WHERE bus_id='".$busid."';"));
		if($isgen['chkgen']>0){
			$sqlquery=mysql_query("UPDATE `mnp_rack`.`hw_general` SET `mnp_ver` = '".$mnpversion."' , `cnt_swmod` = '".count($matches[1])."' , `swmod_list` = '".$mod."' , `swinfo_list` = '".$appinfo."' , `uname` = '".$uname."' , `num_cam` = '".$cameracnt."' , `cam_type` = '".$master['mnp']['camera_type']."' , `cam_list` = '".implode(",",$cameralist)."', `cam_addr` = '".$master['mnp']['camera_addr_1']."' , `num_tft` = '".$dattfton."' , `tft_type` = '".$tftypedat."' , `tft_lst` = '".$tftlistdat."',`cam_rec` = '".$confcamrec."', `cam_str_live` = '".$confcamstrlive."', `cam_str_remote` = '".$confcamremote."', `slide_toplay` = '".$confslidetoplay."', `layout` = '".$conflayout."', `brand` = '".$confbrand."', `pft_stat` = '".$confpftsts."', `pft_pavport` = '".$confpavport."', `rec_length` = '".$confreclength."', `ntp_addr` = '".$confntpaddr."', `fb_capture` = '".$conffbcap."', `snmp_srver` = '".$confsnmpsrv."', `ipc_param` = '".$master['mnp']['ipc_param']."', `mfm_rec_stop` = '".$master['mnp']['MFM_record_stop']."', `date` = '".$datenow."' WHERE `bus_id` = '".$busid."';");
		}else{
			$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`hw_general` (`bus_id`, `mnp_ver`, `cnt_swmod`, `swmod_list`, `swinfo_list`, `uname`, `num_cam`, `cam_type`, `cam_list`, `cam_addr`, `num_tft`, `tft_type`, `tft_lst`, `ipc_param`, `mfm_rec_stop`, `date`) VALUES ('".$busid."', '".$mnpversion."', '".count($matches[1])."', '".$mod."', '".$appinfo."', '".$uname."', '".$cameracnt."', '".$master['mnp']['camera_type']."', '".implode(",",$cameralist)."', '".$master['mnp']['camera_addr_1']."', '".$dattfton."', '".$tftypedat."', '".$tftlistdat."', '".$master['mnp']['ipc_param']."', '".$master['mnp']['MFM_record_stop']."', '".$datenow."');");
		}

		$istag=mysql_fetch_array(mysql_query("SELECT COUNT(bus_id) AS `chktag` FROM `mnp_rack`.`hw_tag` WHERE bus_id='".$busid."';"));
		if($istag['chktag']>0){
			$sqlquery=mysql_query("UPDATE `mnp_rack`.`hw_tag` SET `sol_id` = '".$tagsolid."' , `func_id` = '".$tagfuncid."' , `mod_id` = '".$tagmodid."' , `hw_ver` = '".$taghwver."' , `sn` = '".$tagserialno."' , `proj_id` = '".$tagprojid."' , `prod_date` = '".$tagproddate."' , `lotno` = '".$taglotno."' , `age_hour` = '".$taglotage."' , `fqc_manager` = '".$tagfcqman."' , `fqc_date` = '".$tagfcqdate."' , `oper_hour` = '".$tagoperhr."' , `oper_id` = '".$tagoperid."' , `concess` = '".$tagconcess."' , `plate` = '".$tagplate."' , `commdt` = '".$tagcommdt."' , `commby` = '".$tagcommby."', `date` = '".$datenow."' WHERE `bus_id` = '".$busid."';");
		}else{
			$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`hw_tag` (`bus_id`, `sol_id`, `func_id`, `mod_id`, `hw_ver`, `sn`, `proj_id`, `prod_date`, `lotno`, `age_hour`, `fqc_manager`, `fqc_date`, `oper_hour`, `oper_id`, `concess`, `plate`, `commdt`, `commby`, `date`) VALUES ('".$busid."', '".$tagsolid."', '".$tagfuncid."', '".$tagmodid."', '".$taghwver."', '".$tagserialno."', '".$tagprojid."', '".$tagproddate."', '".$taglotno."', '".$taglotage."', '".$tagfcqman."', '".$tagfcqdate."', '".$tagoperhr."', '".$tagoperid."', '".$tagconcess."', '".$tagplate."', '".$tagcommdt."', '".$tagcommby."', '".$datenow."');");
		}
		echo "OK\n";		
		$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`tool_hardwarecheck` (`groupid`, `busid`, `status`, `date`) VALUES ('".$hostgroup."', '".$busid."', 'OK', '".$datenow."');");
		mysql_close($sqlquery);
}else if($method=="execwifiimprove"){
	error_reporting(0);
			date_default_timezone_set('Europe/Berlin');
			require('konektor.php');
			require('RouterAPI.php');
			$API = new RouterosAPI();
			$API->debug = false;			
			$busid=$_POST['busid'];
			$busip=$_POST['busip'];
			$busgroup=$_POST['hgroup'];
			$date= date("Y-m-d H:i:s");
			$today= date("Y-m-d");
			$datenow=date("Y-m-d H:m:s");

			if ($API->connect($busip, 'admin', 'k0nijn')) {
			/*
			======================================================
			PHASE 1
			NEW Modem info-channel logic by Tom
			======================================================
			*/
			$chksysschdl=$API->comm("/system/scheduler/getall");
            //disable cleanup-modem
            if(in_array_r("cleanup-modem", $chksysschdl)){
                $sysschdlkey=$chksysschdl[recursive_array_search("cleanup-modem",$chksysschdl)]['.id'];
                $API->comm("/system/scheduler/set",array('.id' => $sysschdlkey,'disabled'=>'yes'));
            }
            //sleep 10 sec
            sleep(10);

            //check how many modemx available and remove it
            $idmodem=array();
            $modem=array();
            $ppp=$API->comm("/interface/ppp-client/print",false);
            foreach ($ppp as $key => $val) {
                if(strpos($val['name'], 'modem') !== false) {
                    array_push($modem, $val['name']);
                    array_push($idmodem, $val['.id']);
                    $API->comm("/interface/ppp-client/remove",array('.id' => $val['.id']));
                }
            }

            $usbport=array();
            $modemtype=array();
            $modemch=array();
            $modem820=0;
            $port=$API->comm("/port/print",false);
            foreach ($port as $key => $val) {
                if(strpos($val['name'], 'usb') !== false) {
                    array_push($usbport, $val['name']);
                    array_push($modemch, $val['channels']);
                    if($val['channels']==3){
                        array_push($modemtype, "Huawei 770W");
                    }else if($val['channels']==4){
                        array_push($modemtype, "Huawei 820U");
                        $modem820=$modem820+1;
                    }else if($val['channels']==6){
                        array_push($modemtype, "Huawei 820W");
                        $modem820=$modem820+1;
                    }
                }
            }
            foreach ($usbport as $key => $val) {
                if($val=="usb2"){
                    $usb2=array();
                    $usb2['name']=$modemch[$key];
                    $usb2['ch']=$modemch[$key];
                    $usb2['type']=$modemtype[$key];
                }else if($val=="usb3"){
                    $usb3=array();
                    $usb3['name']=$modemch[$key];
                    $usb3['ch']=$modemch[$key];
                    $usb3['type']=$modemtype[$key];
                }
            }

            //Conditional Check
            if($usb2['type']=="Huawei 820W" && $usb3['type']=="Huawei 770W"){
                //if you detect 1 820W (USB2) and 1 770W (USB3) you need to add modem1 on USB2
                $result="add modem1 on USB2";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if($usb2['type']=="Huawei 770W" && $usb3['type']=="Huawei 820W"){
                //In case you detect 1 770W (USB2) and 1 820W (USB3) you need to add modem1 on USB3
                $result="add modem1 on USB3";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if($usb2['type']=="Huawei 820W" && $usb3['type']=="Huawei 820W"){
                //modem contain Huawei 820W,Huawei 820W add modem1 and modem2
                $result="add modem1 and modem2";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem2','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }else if(($usb2['type']=="Huawei 820W" || $usb2['type']=="Huawei 820U") && ($usb3['type']=="Huawei 820W" || $usb3['type']=="Huawei 820U")){
                //In case you detect 1 820W or 820U (USB2) and 1 820W or 820U (USB3) you need to add modem1 on USB2 and modem2 on USB3
                $result="add modem1 on USB2 and modem2 on USB3";
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem1','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb2','profile'=>'default','use-peer-dns'=>'no','user'=>''));
                $API->comm("/interface/ppp-client/add",array('add-default-route' => 'no','allow'=>'pap','allow'=>'pap,chap,mschap1,mschap2','data-channel'=>'2','dial-command'=>'AT','dial-on-demand'=>'no','disabled'=>'no','info-channel'=>'2','keepalive-timeout'=>'30','max-mru'=>'1500','max-mtu'=>'1500','modem-init'=>"at^curc=0",'mrru'=>'disabled','name'=>'modem2','null-modem'=>'no','password'=>'','phone'=>'Z','pin'=>'','port'=>'usb3','profile'=>'default','use-peer-dns'=>'no','user'=>''));
            }
            
			//step 4 Enable all modem
			$chkmodem=$API->comm("/interface/print");
			if(in_array_r("modem1", $chkmodem)){
				$modem1key=$chkmodem[recursive_array_search("modem1",$chkmodem)]['.id'];
				$API->comm("/interface/set",array('.id' => $modem1key,'disabled'=>'no'));
			}
			if(in_array_r("modem2", $chkmodem)){
				$modem2key=$chkmodem[recursive_array_search("modem2",$chkmodem)]['.id'];
				$API->comm("/interface/set",array('.id' => $modem2key,'disabled'=>'no'));		
			}
			/*
			======================================================
			PHASE 2
			Wi-Fi Settings
			======================================================
			*/
				$chkwlan2=$API->comm("/interface/wireless/print",array('.proplist' => 'name'));
				//Step1
				//Disable Wlan2 is available
				if(in_array_r("wlan2", $chkwlan2)){
					$wlandisable=$API->comm("/interface/wireless/set",array('.id' => 'wlan2','disabled'=>'yes'));
				}
				//Step2
				//Change Data Rates
				$setting=$API->comm("/interface/wireless/set",array('.id' => 'wlan1','rate-set'=>'configured','basic-rates-b'=>'1Mbps','basic-rates-a/g'=>'6Mbps','supported-rates-b'=>'1Mbps','supported-rates-a/g'=>'6Mbps,24Mbps','distance'=>'indoors','noise-floor-threshold'=>'-90'));
				
				//If Setup OK write to database
				if(count($setting)==0){
			    $isexist=mysql_num_rows(mysql_query("SELECT bus_id FROM `mnp_rack`.`mikrotik_wifi_improve` WHERE bus_id='".$busid."';"));

			    if(count($modem)>0){
			    	$listmodem=implode(",", $modem);
			    }else{
			    	$listmodem="";
			    }

			    if(count($modemtype)>0){
			    	$listmtype=implode(",", $modemtype);
			    }else{
			    	$listmtype="";
			    }

			    if(count($modemch)>0){
			    	$listmch=implode(",", $modemch);
			    }else{
			    	$listmch="";
			    }

			    if(count($usbport)>0){
			    	$listserial=implode(",", $usbport);
			    }else{
			    	$listserial="";
			    }
			    
				mysql_select_db("mnp_rack");
				if($isexist>0){
					echo "OK";
					$sqlquery=mysql_query("UPDATE `mnp_rack`.`mikrotik_wifi_improve` SET `lastresultmsg` = '".$result."' , `lastcheck` = '".$date."' WHERE `bus_id` = '".$busid."';");
					$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`tool_wifiimprovement` (`groupid`, `busid`, `status`, `date`) VALUES ('".$busgroup."', '".$busid."', 'OK', '".$datenow."');");
			    }else{
					echo "OK";
					$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`mikrotik_wifi_improve` (`bus_id`, `group`, `ip`, `lastresultmsg`, `isokay`, `num_modem`, `modem_channel`, `modem_type`, `modem_serial`, `date`, `lastcheck`) VALUES ('".$busid."', '".$busgroup."', '".$busip."', '".$result."', '1', '".$listmodem."', '".$listmch."', '".$listmtype."', '".$listserial."', '".$date."', '".$date."');");
					$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`tool_wifiimprovement` (`groupid`, `busid`, `status`, `date`) VALUES ('".$busgroup."', '".$busid."', 'OK', '".$datenow."');");
			    }
				}else{
					echo "NOK";
					$sqlquery=mysql_query("INSERT INTO `mnp_rack`.`tool_wifiimprovement` (`groupid`, `busid`, `status`, `date`) VALUES ('".$busgroup."', '".$busid."', 'NOK', '".$datenow."');");
				}
			$API->disconnect();
		}
}else if($method=="execwifienmodemx"){
		error_reporting(0);
		date_default_timezone_set('Europe/Berlin');
		require('konektor.php');
		require('RouterAPI.php');
		$API = new RouterosAPI();
		$API->debug = false;			
		$busid=$_POST['busid'];
		$busip=$_POST['busip'];
		$busgroup=$_POST['hgroup'];
		$date= date("Y-m-d H:i:s");
		$today= date("Y-m-d");
		$datenow=date("Y-m-d H:m:s");

		if ($API->connect($busip, 'admin', 'k0nijn')) {
			$chksysschdl=$API->comm("/system/scheduler/getall");
			
	        //disable cleanup-modem
	        if(in_array_r("cleanup-modem", $chksysschdl)){
	            $sysschdlkey=$chksysschdl[recursive_array_search("cleanup-modem",$chksysschdl)]['.id'];
	            $API->comm("/system/scheduler/set",array('.id' => $sysschdlkey,'disabled'=>'yes'));
	        }

			//Enable all modem
			$chkmodem=$API->comm("/interface/print");
			if(in_array_r("modem1", $chkmodem)){
				$modem1key=$chkmodem[recursive_array_search("modem1",$chkmodem)]['.id'];
				$API->comm("/interface/set",array('.id' => $modem1key,'disabled'=>'no'));
			}
			if(in_array_r("modem2", $chkmodem)){
				$modem2key=$chkmodem[recursive_array_search("modem2",$chkmodem)]['.id'];
				$API->comm("/interface/set",array('.id' => $modem2key,'disabled'=>'no'));		
			}
			echo "Modemx on ".$busid." Enabled!";
			$API->disconnect();
		}
}else{
	if($_GET['loadmeth']=="wifiimproveload"){
		$sql="SELECT id_log,busid,groupid,status,`date` FROM `mnp_rack`.tool_wifiimprovement ORDER BY `id_log` DESC";
		$res=$MNPrack->query($sql);
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
	}else if($_GET['loadmeth']=="hardwarecheckload"){
		$sql="SELECT id_log,busid,groupid,status,`date` FROM `mnp_rack`.tool_hardwarecheck ORDER BY `id_log` DESC";
		$res=$MNPrack->query($sql);
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
	}
}
?>