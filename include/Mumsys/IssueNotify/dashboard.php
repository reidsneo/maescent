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
$redledsql="SELECT * FROM ai_parameter WHERE isreported=0 ORDER BY totscr DESC";
$redledalert=$MNPrack->query($redledsql);
/*echo "<select>
        <option>Display Unreported Issue</option>
        <option>Display Reported Issue</option>
    </select>";*/
echo "<table style='border:1px solid yellowgreen;width:80%;>
<tr><td colspan='12'><h1><center><b>Unreported</b> Daily Issue Analyst on ".date("Y-m-d")."</center></h1></td></tr>
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
    <td align='center'><b>Frequent in Days</b></td>
    <td align='center'><b>RedLED Score</b></td>
    <td align='center'><b>PFT Score</b></td>
    <td align='center'><b>Alert Score</b></td>
    <td align='center'><b>Issue Spotted</b></td>
    <td align='center'><b>Last Checked</b></td>
</tr>";
if($redledalert->numRows()>0){    
    while ($val =$redledalert->fetchRow()) {
        if(strtotime($val['dur_bus'])>strtotime($mindur)){
            echo "<tr>
            <td align='center'>".$val['id_bus']."</td>
            <td align='center'>".$val['dur_bus']."</td>
            <td align='center'>".$val['record']."</td>
            <td align='center'>".$val['nvs']."</td>
            <td align='center'>".$val['ssd']."</td>
            <td align='center'>".$val['pfttemp']."</td>
            <td align='center'>".$val['pftlux']."</td>
            <td align='center'>".$val['lastissue'];
                $lux=calcpftlux($val['pftlux'],$scrpftlux,$maxpft);
                $temp=calcpfttemp($val['pfttemp'],$scrpfttemp,$maxpft);
                $tottemp=($lux+$temp);
                $pftflag=calcpftscore($tottemp,$scrpft,$scralert);
                $nvs=calcnvs($val['nvs'],$scrnvs);
                $ssd=calcssd($val['ssd'],$scrssd);
                $record=calcrecord($val['record'],$scrrecording,$maxcam);
                $redled=calcredled($record,$nvs,$ssd,$scrredled,$scralert);
                $totalalert=calctotalalert($redled,$pftflag);
                echo "</td>
                <td align='center'><b>".$val['cnt_alert']."</b></td>
                <td align='center'><b>".count(explode(",",$val['list_day']))."X</b></td>
                <td align='center'><b>".$redled."</b></td>
                <td align='center'><b>".$pftflag."</b></td>
                <td align='center' style='color:red;'><b>".(($totalalert+$scralert))."</b></td>
                <td align='center'><b>".$val['date_spotted']."</b></td>
                <td align='center'><b>".$val['last_check']."</b></td>
            </tr>";
        }

    }
}else{
    echo "<tr><td colspan='12' align='center' width='100%''><h3>No Data to be displayed</h3></td></tr>";
}
echo "</table>";
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
?>