<?php
if (!isset($centreon)) {
	exit();
}
	$MNPrack = new CentreonDB("mnp_rack");
	//$DBStorage = new CentreonDB("mnp_rack");
	$DBStorage = new CentreonDB("centstorage");
	$tpl = new Smarty();
	$tpl = initSmartyTpl($path, $tpl);
if(!isset($_GET['busid'])){
	$arr = array();
	$DBRESULT = $DBStorage->query("SELECT SQL_CALC_FOUND_ROWS h.name AS hostname,FROM_UNIXTIME(s.last_check), FROM_UNIXTIME(s.last_state_change), FROM_UNIXTIME(s.last_hard_state_change) FROM centreon_storage.hosts h, centreon_storage.services s LEFT JOIN customvariables cv ON (s.service_id = cv.service_id AND s.host_id = cv.host_id AND cv.name = 'CRITICALITY_LEVEL') LEFT JOIN customvariables cv2 ON (s.service_id = cv2.service_id AND s.host_id = cv2.host_id AND cv2.name = 'CRITICALITY_ID') WHERE s.host_id = h.host_id AND h.name NOT LIKE '_Module_%' AND s.enabled = 1 AND h.enabled = 1 AND s.output LIKE '%alert%' AND s.description LIKE '%led%'");
	//$DBRESULT = $DBStorage->query("SELECT id_alert as host_id,bus_id as hostname  from daily_alert ORDER BY id_alert DESC limit 100");
	$maxrow=$DBRESULT->numRows();
	$i=0;
	if($maxrow>0){
		while($row = $DBRESULT->fetchRow()){
			array_push($arr, $row);
			$dtbus = $MNPrack->query("SELECT msg_alert,is_recording,is_nvs_offline,is_ssd,is_pftlux,is_pfttemp,is_chkfilesize FROM daily_alert WHERE bus_id='$row[hostname]' ORDER BY id_alert DESC LIMIT 1;");
			while($bus = $dtbus->fetchRow()){
				//array_push($busdat, array ($row['hostname']=> $bus));
				$busdat[$row['hostname']]=$bus;
			}
			$i++;
		}
	}else{
		$busdat="";
	}
	$databus='{"data": ['.json_encode($arr).']}';
	$DBRESULT->free();
	$tpl->assign('msg_interval', "");
	$tpl->assign('bus', $arr);
	$tpl->assign('busdat', $busdat);
	$tpl->assign('datenow',date("d/m/Y"));
	$tpl->assign('datetimenow',date("Y-m-d H:m:s"));
	$tpl->display(realpath(dirname(__FILE__))."/dash.tpl");
}else{
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	$infor = new CentreonSoap();
	$result = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncidents','strPropertyList'=>'IncNum,Charfld2,IncDate','strFilter'=>'','strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'4','s'=>''));
	$erp = new PDO("dblib:host=erpdbserver.cloudapp.net:14435;dbname=MTNL_App;", "sa", "4Dragon2Cheese"); //SyteLine_AppDemo //MTNL_App
	$erp->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION );
	//"SerNum='".$_GET['busid']."'"
	$buserp = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncidents','strPropertyList'=>'IncNum,Charfld2,IncDate,Description,PriorCode,StatCode,SSR','strFilter'=>"SerNum='".$_GET['busid']."'",'strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'20','s'=>''));
	$fsincidents=CentreonSoap::parsexml($buserp->LoadDataSetResult->any);
	if(isset($fsincidents['FSIncidents']['IDO']['IncNum'])){
		$erprow=1;
		$tpl->assign('buserp',array('0' =>$fsincidents['FSIncidents']['IDO']));
	}else{
		$erprow=count($fsincidents['FSIncidents']['IDO']);
		$tpl->assign('buserp', $fsincidents['FSIncidents']['IDO']);
	}
	$busdt = array();
	$busview = $MNPrack->query("SELECT id_alert,bus_id AS bus,bus_locate,msg_alert,`lastcheck` FROM daily_alert WHERE `lastcheck` LIKE '%".date("Y-m-d")."%' AND bus_id='".$_GET['busid']."' ORDER BY id_alert DESC");
	$maxrow=$busview->numRows();
	while($dt = $busview->fetchRow()) {
		array_push($busdt, $dt);
	}
	$tpl->assign('busid',$_GET['busid']);
	$tpl->assign('busdat', $busdt);
	$tpl->assign('issuecnt', $maxrow);
	$tpl->assign('acccnt', $erprow);
	$tpl->assign('datenow',date("d/m/Y"));
	$tpl->display(realpath(dirname(__FILE__))."/dash_view.tpl");
}
?>
