<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

function isblank($value){
    if($value==""){
        return 0;
    }else{
        return $value;
    }
}
$centreon_path='/usr/share/centreon/';
require_once $centreon_path . 'www/class/centreon.class.php';
require_once $centreon_path . 'www/class/centreonSession.class.php';
require_once $centreon_path . 'www/class/centreonDB.class.php';
require_once $centreon_path . 'www/class/centreonWidget.class.php';
require_once $centreon_path . 'www/class/centreonDuration.class.php';
require_once $centreon_path . 'www/class/centreonUtils.class.php';
require_once $centreon_path . 'www/class/centreonACL.class.php';
require_once $centreon_path . 'www/class/centreonHost.class.php';
require_once $centreon_path . 'www/class/centreonService.class.php';
session_start();
$db = new CentreonDB();
if (CentreonSession::checkSession(session_id(), $db) == 0) {
  exit();
}

$MNPrack = new CentreonDB("mnp_rack");
$Dbstorage = new CentreonDB("centstorage");
    $redledsql="SELECT SQL_CALC_FOUND_ROWS h.name AS name,s.description,s.output,FROM_UNIXTIME(s.last_check) AS lastcheck,FROM_UNIXTIME(s.last_state_change) AS lastchange,s.last_state_change as changed,FROM_UNIXTIME(s.last_hard_state_change) as lasthard,'crit' AS `type` FROM centreon_storage.hosts h, centreon_storage.services s LEFT JOIN customvariables cv ON (s.service_id = cv.service_id AND s.host_id = cv.host_id AND cv.name = 'CRITICALITY_LEVEL') LEFT JOIN customvariables cv2 ON (s.service_id = cv2.service_id AND s.host_id = cv2.host_id AND cv2.name = 'CRITICALITY_ID') WHERE s.host_id = h.host_id AND h.name NOT LIKE '_Module_%' AND s.enabled = 1 AND h.enabled = 1 AND s.output LIKE '%alert%' AND s.description LIKE '%led%'";

    $pftsql="SELECT SQL_CALC_FOUND_ROWS h.name AS name,s.description,s.output,FROM_UNIXTIME(s.last_check) AS lastcheck,FROM_UNIXTIME(s.last_state_change) AS lastchange,s.last_state_change as changed,FROM_UNIXTIME(s.last_hard_state_change) as lasthard,'safe' AS `type` FROM centreon_storage.hosts h, centreon_storage.services s LEFT JOIN customvariables cv ON (s.service_id = cv.service_id AND s.host_id = cv.host_id AND cv.name = 'CRITICALITY_LEVEL') LEFT JOIN customvariables cv2 ON (s.service_id = cv2.service_id AND s.host_id = cv2.host_id AND cv2.name = 'CRITICALITY_ID') WHERE s.host_id = h.host_id AND h.name NOT LIKE '_Module_%' AND s.enabled = 1 AND h.enabled = 1 AND s.output LIKE '%alert%' AND s.output LIKE '%pft%'";

    $redledalert=$Dbstorage->query($redledsql);
    $pftalert=$Dbstorage->query($pftsql);
    echo "[";
    $maxredledalert=$redledalert->numRows();
    while ($redrow =$redledalert->fetchRow()) {
    $i=$i+1;
    $findnya = array("/opt/mnp/flags/tftFlag/", "\r\n","\r","\n","\\r","\\n","\\r\\n","/opt/mnp/flags/ibisFlag/","/opt/mnp/flags/miscFlag/","/opt/mnp/flags/ntpFlag/","alert_","alert-","/opt/mnp/flags/ledFlag/"," geen alarm","geen alarm");
    $replacenya   = array("", " "," "," "," "," "," ","","","",",",",","","","");
    $output=trim(str_replace($findnya,$replacenya,$redrow['output']));
    $output = str_replace(' ', '', $output);
    $output=preg_replace( "/\r|\n/", "", $output);
    $output = str_replace(array('\r', '\n'), '', $output);
    $outputexp=array_unique(explode(",", $output));
    $output=implode(",", $outputexp);
    $busloc=explode("-", $redrow["description"]);

    $record=0;
    $nvs=0;
    $ssd=0;
    $pftlux=0;
    $pfttemp=0;
    $chkfilesize=0;
    foreach ($outputexp as $key => $val) {
        if (strpos(trim($val),'nNVS')!== FALSE){
            $record=$record+1;
        }
        if (strpos(trim($val),'cam')!== FALSE){
            $record=$record+1;
        }
        if (strpos(trim($val),'nvs-offline')!== FALSE){
            $nvs=$nvs+1;
        }
        if (strpos(trim($val),'ssd')!== FALSE){
            $ssd=$ssd+1;
        }
        if (strpos(trim($val),'pfttemp')!== FALSE){
            $pfttemp=$pfttemp+1;
        }
        if (strpos(trim($val),'pftlux')!== FALSE){
            $pftlux=$pftlux+1;
        }
        if (strpos(trim($val),'chkfilesize')!== FALSE){
            $chkfilesize=$chkfilesize+1;
        }
    }
        $redrow['record']=$record;
        if($busloc[1]=="Voorne_Putten_2015"){
            $busloc[1]="VP 2015";
        }else if($busloc[1]=="Prov.Utrecht"){
            $busloc[1]="PU";
        }else if($busloc[1]=="AML_Ztangent"){
            $busloc[1]="AML_Z";
        }

        $redrow["description"]=$busloc[1];
        $record=0;
        $redrow['output']=trim(substr($output,1));
        $redrow['nvs']=$nvs;
        $redrow['ssd']=$ssd;
        $redrow['pfttemp']=$pfttemp;
        $redrow['pftlux']=$pftlux;
        $redrow['chkfilesize']=$chkfilesize;
        $redrow['curtime']=date("Y-m-d H:m:s");
        $redrow['cnt']=($redrow['record']+$redrow['nvs']+$redrow['ssd']+$redrow['pfttemp']+$redrow['pftlux']);

        $prevalert=$MNPrack->query("SELECT is_recording,is_nvs_offline,is_ssd,is_pftlux,is_pfttemp,(SELECT COUNT(*) FROM daily_alert WHERE bus_id='".$redrow['name']."' AND statecheck LIKE '%".date("Y-m-d")."%') as totalalert FROM daily_alert WHERE bus_id='".$redrow['name']."' AND statecheck LIKE '%".date("Y-m-d")."%' ORDER BY id_alert DESC LIMIT 1;");
        $prevalert=$prevalert->fetchRow();
        $redrow['prevrecord']=0;
        $redrow['prevnvs']=0;
        $redrow['prevssd']=0;
        $redrow['prevlux']=0;
        $redrow['prevtemp']=0;
        $redrow['prevrecord']=isblank($prevalert['is_recording']);
        $redrow['prevnvs']=isblank($prevalert['is_nvs_offline']);
        $redrow['prevssd']=isblank($prevalert['is_ssd']);
        $redrow['prevlux']=isblank($prevalert['is_pftlux']);
        $redrow['prevtemp']=isblank($prevalert['is_pfttemp']);

        $redrow['duration']=0;
        $redrow['duration']=CentreonDuration::toString(time()-$redrow['changed']);
        $redrow['alertcnt']=isblank($prevalert['totalalert']);

        $days=0;$hours=0;$minutes=0;$seconds=0;
        echo trim(json_encode($redrow));
        if($i!=$maxredledalert){
            echo ",";
        }
    }
        $i=0;
        $pftrecord=0;
        $nvs=0;
        $ssd=0;
        $pftlux=0;
        $pfttemp=0;
        $chkfilesize=0;
        $e=0;
        $maxpftalert=$pftalert->numRows();
        if($maxpftalert>0){
            echo ",";
        }
    while ($pftrow =$pftalert->fetchRow()) {
    $e=$e+1;
    $findnya = array("/opt/mnp/flags/tftFlag/", "\r\n","\r","\n","\\r","\\n","\\r\\n","/opt/mnp/flags/ibisFlag/","/opt/mnp/flags/miscFlag/","/opt/mnp/flags/ntpFlag/","alert_","alert-","/opt/mnp/flags/ledFlag/"," geen alarm","geen alarm");
    $replacenya   = array("", " "," "," "," "," "," ","","","",",",",","","","");
    $output=trim(str_replace($findnya,$replacenya,$pftrow['output']));
    $output = str_replace(' ', '', $output);
    $output=preg_replace( "/\r|\n/", "", $output);
    $output = trim(str_replace(array("\r", "\n"), '', $output));
    $output = str_replace(array('\r', '\n'), '', $output);
    $outputexp=array_unique(explode(",", $output));
    $output=implode(",", $outputexp);
    $busloc=explode("-", $pftrow["description"]);


    $record=0;
    $nvs=0;
    $ssd=0;
    $pftlux=0;
    $pfttemp=0;
    $chkfilesize=0;
    foreach ($outputexp as $key => $val) {
        if (strpos(trim($val),'nNVS')!== FALSE){
            $record=$record+1;
        }
        if (strpos(trim($val),'cam')!== FALSE){
            $record=$record+1;
        }
        if (strpos(trim($val),'nvs-offline')!== FALSE){
            $nvs=$nvs+1;
        }
        if (strpos(trim($val),'ssd')!== FALSE){
            $ssd=$ssd+1;
        }
        if (strpos(trim($val),'pfttemp')!== FALSE){
            $pfttemp=$pfttemp+1;
        }
        if (strpos(trim($val),'pftlux')!== FALSE){
            $pftlux=$pftlux+1;
        }
        if (strpos(trim($val),'chkfilesize')!== FALSE){
            $chkfilesize=$chkfilesize+1;
        }
    }
        $pftrow['record']=$record;
        if($busloc[1]=="Voorne_Putten_2015"){
            $busloc[1]="VP 2015";
        }else if($busloc[1]=="Prov.Utrecht"){
            $busloc[1]="PU";
        }else if($busloc[1]=="AML_Ztangent"){
            $busloc[1]="AML_Z";
        }

        $pftrow["description"]=$busloc[1];
        $record=0;
        $pftrow['output']=trim(substr($output,1));
        $pftrow['nvs']=$nvs;
        $pftrow['ssd']=$ssd;
        $pftrow['pfttemp']=$pfttemp;
        $pftrow['pftlux']=$pftlux;
        $pftrow['curtime']=date("Y-m-d H:m:s");
        $pftrow['chkfilesize']=$chkfilesize;
        $pftrow['cnt']=($pftrow['record']+$pftrow['nvs']+$pftrow['ssd']+$pftrow['pfttemp']+$pftrow['pftlux']);
        
        $prevalertpft=$MNPrack->query("SELECT is_recording,is_nvs_offline,is_ssd,is_pftlux,is_pfttemp,(SELECT COUNT(*) FROM daily_alert WHERE bus_id='".$pftrow['name']."' AND statecheck LIKE '%".date("Y-m-d")."%') as totalalert FROM daily_alert WHERE bus_id='".$pftrow['name']."' AND statecheck LIKE '%".date("Y-m-d")."%' ORDER BY id_alert DESC LIMIT 1;");
        $prevalertpft=$prevalertpft->fetchRow();
        $pftrow['prevrecord']=0;
        $pftrow['prevnvs']=0;
        $pftrow['prevssd']=0;
        $pftrow['prevlux']=0;
        $pftrow['prevtemp']=0;
        $pftrow['totalalert']=0;
        $pftrow['prevrecord']=isblank($prevalertpft['is_recording']);
        $pftrow['prevnvs']=isblank($prevalertpft['is_nvs_offline']);
        $pftrow['prevssd']=isblank($prevalertpft['is_ssd']);
        $pftrow['prevlux']=isblank($prevalertpft['is_pftlux']);
        $pftrow['prevtemp']=isblank($prevalertpft['is_pfttemp']);
        $pftrow['alertcnt']=isblank($prevalertpft['totalalert']);
        
        $pftrow['duration']=0;
        $pftrow['duration']=CentreonDuration::toString(time()-$pftrow['changed']);

        $days=0;$hours=0;$minutes=0;$seconds=0;
        echo trim(json_encode($pftrow));
        if($e!=$maxpftalert){
            echo ",";
        }
    }

    echo "]";
?>