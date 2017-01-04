<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

/*===================================================*/
$mindur="00:20:00";

//SCORE
//main score
$scrduration=33.33;
$scrfrequent=33.33;
$scralert=33.33;

//alert subscore
$scrredled=0.8;
$scrpft=0.2;
$scrother=0.15; //notused now

//redled subscore
$scrrecording=33.33;
$scrnvs=33.33;
$scrssd=33.33;

//pft subscore
$scrpftlux=0.7;
$scrpfttemp=0.3;

$maxcam=5;
$maxpft=5;


//SAKAMOTO ALGORITHM
function calcfrequent($cnttoday,$ssd,$nvs,$syteline){
    if($ssd>0 OR $nvs>0 OR strlen($syteline)>0){
        if($cnttoday>0 AND $cnttoday<6){
            return 5;
        }else if($cnttoday>5 AND $cnttoday<10){
            return 8;
        }
    }
}


function calcpftlux($cnt,$scrpftlux,$maxpft){
    return ($cnt/$maxpft)*$scrpftlux;
}
function calcpfttemp($cnt,$scrpfttemp,$maxpft){
    return ($cnt/$maxpft)*$scrpfttemp;
}

function calcpftscore($pftscr,$scrpft,$scralert){
    return ($pftscr*$scrpft);
}
/////////////////////////////////////////////////
function calcssd($cnt,$scrssd){
    return ($cnt*$scrssd);
}
function calcnvs($cnt,$scrnvs){
    return ($cnt*$scrnvs);
}
function calcrecord($cnt,$scrrecording,$maxcam){
    return ($cnt/($maxcam*2))*$scrrecording;
}
////////////////////////////////////////////////
function calcredled($record,$nvs,$ssd,$scrredled,$scralert){
    return ($record+$nvs+$ssd);
}
//////////////////////////////////////////////////
function calctotalalert($redledflag,$pftflag){
    return ($redledflag+$pftflag);
}
//die();
/*=====================================================*/

function isblank($value){
    if($value==""){
        return 0;
    }else{
        return $value;
    }
}

$i=0;
$body="";
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
require_once $centreon_path . 'www/class/centreonSoap.class.php';

$date= date("Y-m-d H:i:s");
$tgl= date("d");
$MNPrack = new CentreonDB("mnp_rack");
$Dbstorage = new CentreonDB("centstorage");
$syteline="";
$infor = new CentreonSoap();
$result = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncidents','strPropertyList'=>'IncNum,Charfld2,IncDate','strFilter'=>'','strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'4','s'=>''));
    $erp = new PDO("dblib:host=erpdbserver.cloudapp.net:14435;dbname=MTNL_App;", "sa", "4Dragon2Cheese"); //SyteLine_AppDemo //MTNL_App
    $erp->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION );
    function timegap($time){
        $day="";
        if (preg_match('/^(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?(?:(?<minutes>\d+)m\s*)?(?:(?<seconds>\d+)s\s*)?$/', $time, $matches)) {
            $time=sprintf(
                '%02s:%02s:%02s',
                (!empty($matches['hours'])   ? $matches['hours']   : '00'),
                (!empty($matches['minutes']) ? $matches['minutes'] : '00'),
                (!empty($matches['seconds']) ? $matches['seconds'] : '00')
                );
            if(!empty($matches['days'])){
              $day=$matches['days'];
          }
          if(!empty($matches['hours'])){
              $hour=$matches['hours'];
          }
          if(!empty($matches['minutes'])){
              $min=$matches['minutes'];
          }
          if(!empty($matches['seconds'])){
              $sec=$matches['seconds'];
          }
          return array('time' => $time,'day'=>$day);
      }else{
          if (preg_match('/^[0-9]{0,2}+d/', $time, $matches)) {
            $day=$matches[0];
        }
        $sub=str_replace($day,"",$time);
        if(preg_match('/(^[0-9]{2})(:{1})([0-9]{2})(:{1})([0-9]{2})$/',$sub,$match)){
            $time=$match[0];
            $normalgap=1;
        }
        if($normalgap==1){
            return array('time' => $time,'day'=>str_replace("d", "",$day));
        }
    }
}
$redledsql="(SELECT h.name AS name,s.description,s.output,
FROM_UNIXTIME(s.last_check) AS lastcheck,
FROM_UNIXTIME(s.last_state_change) AS lastchange,
s.last_state_change AS changed,
FROM_UNIXTIME(s.last_hard_state_change) AS lasthard,'crit' AS `type` FROM centreon_storage.hosts h, centreon_storage.services s LEFT JOIN customvariables cv ON (s.service_id = cv.service_id AND s.host_id = cv.host_id AND cv.name = 'CRITICALITY_LEVEL') LEFT JOIN customvariables cv2 ON (s.service_id = cv2.service_id AND s.host_id = cv2.host_id AND cv2.name = 'CRITICALITY_ID') WHERE s.host_id = h.host_id AND h.name NOT LIKE '_Module_%' AND s.enabled = 1 AND h.enabled = 1 AND s.output LIKE '%alert%' AND s.description LIKE '%led%'
)UNION ALL (
SELECT h.name AS name,s.description,s.output,
FROM_UNIXTIME(s.last_check) AS lastcheck,
FROM_UNIXTIME(s.last_state_change) AS lastchange,
s.last_state_change AS changed,
FROM_UNIXTIME(s.last_hard_state_change) AS lasthard,'crit' AS `type` FROM centreon_storage.hosts h, centreon_storage.services s LEFT JOIN customvariables cv ON (s.service_id = cv.service_id AND s.host_id = cv.host_id AND cv.name = 'CRITICALITY_LEVEL') LEFT JOIN customvariables cv2 ON (s.service_id = cv2.service_id AND s.host_id = cv2.host_id AND cv2.name = 'CRITICALITY_ID') WHERE s.host_id = h.host_id AND h.name NOT LIKE '_Module_%' AND s.enabled = 1 AND h.enabled = 1 AND s.output LIKE '%pft%')";
$redledalert=$Dbstorage->query($redledsql);
$maxredledalert=$redledalert->numRows();
$datarow=array();
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
    $redrow['duration']=0;
    $redrow['duration']=timegap(CentreonDuration::toString(time()-$redrow['changed']));
    $days=0;$hours=0;$minutes=0;$seconds=0;
    $datarow[$i]=$redrow;
}
$body=$body."<table style='border:1px solid yellowgreen;''>
<tr><td colspan='12'><h1><center>Daily Issue Analyst on ".date("Y-m-d")."</center></h1></td></tr>
<tr>
    <td align='center'>Bus ID</td>
    <td align='center'>Duration</td>
    <td align='center'>Recording</td>
    <td align='center'>NVS</td>
    <td align='center'>SSD</td>
    <td align='center'>PftTemp</td>
    <td align='center'>Pftlux</td>
    <td align='center'>Last Issue on Syteline</td>
    <td align='center'><b>Total Alert</b></td>
    <td align='center'><b>RedLED Score</b></td>
    <td align='center'><b>PFT Score</b></td>
    <td align='center'><b>Alert Score</b></td>
</tr>";
foreach ($datarow as $key => $val) {
    if(strtotime($val['duration']['time'])>strtotime($mindur)){
        $buserp = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncidents','strPropertyList'=>'IncNum,Charfld2,IncDate,Description,PriorCode,StatCode,SSR','strFilter'=>"SerNum='".$val['name']."'",'strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'1','s'=>''));
        $fsincidents=CentreonSoap::parsexml($buserp->LoadDataSetResult->any);
        $body=$body."<tr>
        <td align='center'>".$val['name']."</td>
        <td align='center'>".$val['duration']['time']."</td>
        <td align='center'>".$val['record']."</td>
        <td align='center'>".$val['nvs']."</td>
        <td align='center'>".$val['ssd']."</td>
        <td align='center'>".$val['pfttemp']."</td>
        <td align='center'>".$val['pftlux']."</td>
        <td align='center'>";
            if(isset($fsincidents['FSIncidents']['IDO']['IncNum'])){
                $sldate=$fsincidents['FSIncidents']['IDO']['IncDate'];
                if(isset($sldate) AND $sldate!=""){
                    $syteline=str_replace($val['name'],"",$fsincidents['FSIncidents']['IDO']['Description']);
                    $sytelinedate=" (".date('Y-m-d', strtotime($sldate)).")";
                    $body=$body.$syteline.$sytelinedate;
                }
            }
            $lux=calcpftlux($val['pftlux'],$scrpftlux,$maxpft);
            $temp=calcpfttemp($val['pfttemp'],$scrpfttemp,$maxpft);
            $tottemp=($lux+$temp);
            $pftflag=calcpftscore($tottemp,$scrpft,$scralert);
            $nvs=calcnvs($val['nvs'],$scrnvs);
            $ssd=calcssd($val['ssd'],$scrssd);
            $record=calcrecord($val['record'],$scrrecording,$maxcam);
            $redled=calcredled($record,$nvs,$ssd,$scrredled,$scralert);
            $totalalert=calctotalalert($redled,$pftflag);
            $chkifexist=$MNPrack->query("SELECT id_bus,cnt_today FROM ai_parameter WHERE id_bus='".$val['name']."'");
            //Check Is Perimeter already exist
            if($chkifexist->numRows()>0){
                $MNPrack->query("UPDATE `mnp_rack`.`ai_parameter` SET `dur_bus` = '".$val['duration']['time']."' , `record` = '".$val['record']."' , `nvs` = '".$val['nvs']."',`ssd` = '".$val['ssd']."',`pfttemp` = '".$val['pfttemp']."',`pftlux` = '".$val['pftlux']."',`lastissue` = '".trim($syteline.$sytelinedate)."' , `cnt_alert` = '".$val['cnt']."' , `cnt_today`=`cnt_today`+1, `last_check` = '".$date."' WHERE `id_bus` = '".$val['name']."';");
            }else{
            //Insert New Data
                $MNPrack->query("INSERT INTO `mnp_rack`.`ai_parameter` (`id_bus`, `con_bus`, `ver_bus`, `dur_bus`, `record`, `nvs`, `ssd`, `pfttemp`, `pftlux`, `lastissue`, `cnt_alert`, `cnt_today`, `cnt_total`, `list_day`, `last_check`, `score`, `isreported`, `date_reported`, `date_spotted`) VALUES ('".$val['name']."', '', '', '".$val['duration']['time']."', '".$val['record']."', '".$val['nvs']."', '".$val['ssd']."', '".$val['pfttemp']."', '".$val['pftlux']."', '".trim($syteline.$sytelinedate)."', '".$val['cnt']."', '1', '1', '".$tgl."', '".$date."', '0', '0', '00:00:00', '".$date."');");
            }
            $body=$body."</td>
            <td align='center'><b>".$val['cnt']."</b></td>
            <td align='center'><b>".$redled."</b></td>
            <td align='center'><b>".$pftflag."</b></td>
            <td align='center'><b>".(($totalalert+$scralert))."</b></td>
        </tr>";
    }

}
$body=$body."</table>";
$sendemail=0;
if($sendemail==1){    
    require 'PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = "centreonbot@gmail.com";
    $mail->Password = "centreonbot123";
    $mail->setFrom('centreonbot@gmail.com', 'CentreonBot');
    $mail->addReplyTo('centreonbot@gmail.com', 'CentreonBot');
    $mail->addAddress('edwin.pranatha@maes-electronic.co.id', 'Edwin Ardiant');
    //$mail->AddCC("vino.mahavira@maes-electronic.co.id");
    //$mail->AddCC("yandi.kuspriandi@maes-electronic.co.id");
    $mail->Subject = 'Daily Issue Analyst on '.date("Y-m-d");
    $mail->IsHTML(true);
    $mail->Body=$body;
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}
echo $body;

/*
$lux=calcpftlux(5,$scrpftlux,$maxpft);
$temp=calcpfttemp(5,$scrpfttemp,$maxpft);
$tottemp=($lux+$temp);
$pftflag=calcpftscore($tottemp,$scrpft,$scralert);
echo "<br>pftflag".$pftflag;

$nvs=calcnvs(1,$scrnvs);
echo "<br>nvs".$nvs;

$ssd=calcssd(1,$scrssd);
echo "<br>ssd".$ssd;

$record=calcrecord(10,$scrrecording,$maxcam);
echo "<br>recording".$record;
$redled=calcredled($record,$nvs,$ssd,$scrredled,$scralert);
echo "<br>redled:".$redled;

$totalalert=calctotalalert($redled,$pftflag);
echo "<br>".($totalalert*$scralert);

echo "<br>".((6*$scrfrequent)+($totalalert*$scralert)+(10*$scrduration))/100;
*/
?>