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
$today= date("Y-m-d");
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


$res=$MNPrack->query("SELECT * FROM mum_setting");
while ($row =$res->fetchRow()) {
    if($row['nm_setting']=="emailcron") $hotspotmon=$row['val_setting'];
    if($row['nm_setting']=="emailcheckinterval") $emailcheckinterval=$row['val_setting'];
}
echo "--------------------------\n";
echo "      Email Daemon    \n";
echo "  Interval is ".$emailcheckinterval." Seconds\n";
echo "--------------------------\n";
while (true) {
    $res=$MNPrack->query("SELECT * FROM mum_setting");
    while ($row =$res->fetchRow()) {
        if($row['nm_setting']=="emailcron") $hotspotmon=$row['val_setting'];
        if($row['nm_setting']=="emailcheckinterval") $emailcheckinterval=$row['val_setting'];
        if($row['nm_setting']=="emailnotifytime") $emailnotifytime=$row['val_setting'];
        if($row['nm_setting']=="emailtarget") $emailtarget=$row['val_setting'];
        if($row['nm_setting']=="emailcc") $emailcc=$row['val_setting'];
        if($row['nm_setting']=="emailbotuser") $emailbotuser=$row['val_setting'];
        if($row['nm_setting']=="emailbotpass") $emailbotpass=$row['val_setting'];
        if($row['nm_setting']=="emailtargetname") $emailtargetname=$row['val_setting'];
        if($row['nm_setting']=="emaillastreport") $emaillastreport=$row['val_setting'];
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
$sytelinedate="";
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
$issue="";
$issue=$issue."\n======================================";
if(count($datarow)>0){
foreach ($datarow as $key => $val) {
    if(strtotime($val['duration']['time'])>strtotime($mindur)){
        try{
            $buserp = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncidents','strPropertyList'=>'IncNum,Charfld2,IncDate,Description,PriorCode,StatCode,SSR','strFilter'=>"SerNum='".$val['name']."'",'strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'1','s'=>''));
            $fsincidents=CentreonSoap::parsexml($buserp->LoadDataSetResult->any);
        }
        catch(Exception $ex){
            $issue=$issue.'\nCannot Connect to Syteline!';
        }
        $body=$body."<tr>
        <td align='center'>".$val['name']."</td>
        <td align='center'>".$val['duration']['time']."</td>
        <td align='center'>".$val['record']."</td>
        <td align='center'>".$val['nvs']."</td>
        <td align='center'>".$val['ssd']."</td>
        <td align='center'>".$val['pfttemp']."</td>
        <td align='center'>".$val['pftlux']."</td>
        <td align='center'>";
        $issue=$issue."\n[Issue] ".$val['name']." - Duration : ".$val['duration']['time']." ";
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
            $body=$body."</td>
            <td align='center'><b>".$val['cnt']."</b></td>
            <td align='center'><b>".$redled."</b></td>
            <td align='center'><b>".$pftflag."</b></td>
            <td align='center'><b>".(($totalalert+$scralert))."</b></td>
        </tr>";

            $chkifexist=$MNPrack->query("SELECT id_bus,cnt_today,list_day FROM ai_parameter WHERE id_bus='".$val['name']."' AND date_spotted LIKE '%$today%';");
            //Check Is Perimeter already exist
            $dayupdate=0;
            if($chkifexist->numRows()>0){
                $datarow=$chkifexist->fetchRow();
                $listday=$datarow['list_day'];
                if(strlen($listday)>0 AND strlen($listday)<3){
                    $listday=$datarow['list_day'];
                    if($listday!=$tgl){
                        $listday=$datarow['list_day'].",".$tgl;
                        $dayupdate=1;
                    }
                }else{
                    $lst=explode(",",$datarow['list_day']);
                    $listdae="";
                    foreach ($lst as $key => $dat) {
                        $listdae=$listdae.$dat.",";
                    }
                    $listday=$listdae.substr($tgl,0,(strlen($listdae)-1));
                    if (in_array($tgl, $lst)){
                        $dayupdate=0;
                    }else{
                        $dayupdate=1;
                    }
                }
                if($dayupdate==1){
                    $datenow=date("Y-m-d H:i:s");
                    $MNPrack->query("UPDATE `mnp_rack`.`ai_parameter` SET `dur_bus` = '".$val['duration']['time']."' , `record` = '".$val['record']."' , `nvs` = '".$val['nvs']."',`ssd` = '".$val['ssd']."',`pfttemp` = '".$val['pfttemp']."',`pftlux` = '".$val['pftlux']."',`lastissue` = '".trim($syteline.$sytelinedate)."' , `cnt_alert` = '".$val['cnt']."', `list_day`='".$listday."' , `cnt_today`=`cnt_today`+1, `last_check` = '".$datenow."' WHERE `id_bus` = '".$val['name']."';");
                }
                $issue=$issue."[Old]";
            }else{
            //Insert New Data
                $datenow=date("Y-m-d H:i:s");
                $issue=$issue."[New]";
                $MNPrack->query("INSERT INTO `mnp_rack`.`ai_parameter` (
                    `id_bus`, `con_bus`, `ver_bus`, `dur_bus`, `record`, `nvs`,
                    `ssd`, `pfttemp`, `pftlux`, `lastissue`,
                    `cnt_alert`, `cnt_today`, `cnt_total`, `list_day`, `last_check`, `scrredled`, `scrpft`, `totscr`,
                    `isreported`, `date_reported`, `date_spotted`) VALUES (
                    '".$val['name']."', '', '', '".$val['duration']['time']."', '".$val['record']."', '".$val['nvs']."',
                    '".$val['ssd']."', '".$val['pfttemp']."', '".$val['pftlux']."', '".trim($syteline.$sytelinedate)."',
                    '".$val['cnt']."', '0', '0', '".$tgl."', '".$date."', '".$redled."','".$pftflag."', '".($totalalert+$scralert)."',
                    '0', '00:00:00', '".$datenow."');");
            }
    }

}
}else{
    $issue=$issue."no issue found";
}

$chkunreport=$MNPrack->query("SELECT isreported FROM `ai_parameter` WHERE isreported='0';");
if($chkunreport->numRows()>0){
    //echo "mail sended";
    $time = date("H:i:s",strtotime($emaillastreport));
    $datereport = date("Y-m-d",strtotime($emaillastreport));
    $datenow = date("Y-m-d");
    $timealarm = date("H:i:s",strtotime($emailnotifytime));
    if($datereport!=$datenow){
        if(strtotime($emailnotifytime)<strtotime(date("H:i:s"))){
            //echo "lebih kecil";
            $sendemail=1;
        }else{
            //echo "lebih besar";
            //$sendemail=1;
            $sendemail=0;
        }
    }else{
        //echo "today has reported";
        $sendemail=0;
    }
}
$body=$body."</table>";
if($sendemail==1){    
    require_once 'PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = $emailbotuser;
    $mail->Password = $emailbotpass;
    $mail->setFrom($emailbotuser, 'CentreonBot');
    $mail->addReplyTo($emailbotuser, 'CentreonBot');
    $mail->addAddress($emailtarget, $emailtargetname);
    $ccmail=explode(";", $emailcc);
    foreach ($ccmail as $key => $val) {
       $mail->AddCC(trim($val));
    }
    $mail->Subject = 'Daily Issue Analyst on '.date("Y-m-d");
    $mail->IsHTML(true);
    $mail->Body=$body;
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
        $MNPrack->query("UPDATE `mnp_rack`.`ai_parameter` SET `isreported` = '1',`date_reported` = '".$datenow."' WHERE isreported='0';");
        $MNPrack->query("UPDATE `mnp_rack`.`mum_setting` SET `val_setting` = '".$date."' WHERE `nm_setting` = 'emaillastreport';");
    }
}
$datenow=date("Y-m-d H:i:s");
$issue=$issue."\n======================================";
echo $issue;
echo "\ninterval :".$emailcheckinterval."----------------------------------------------- ".$datenow."\n";
sleep($emailcheckinterval);
}
?>