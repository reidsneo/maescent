<?php 
$file = fopen("listupdatenya.txt", "r");
while(!feof($file)){
    $line = fgets($file);
    $dat=explode(",", $line);
    //echo ." -a ".$dat['1']." -t ".."<br>";
    if($dat['1']=="0.0.0.0"){
    	$aton="";
    }else{
    	$aton="-a ".$dat['1'];
    }
    
    if(trim($dat['2'])=="0.0.0.0"){
    	$tunnel="";
    }else{
    	$tunnel=" -t ".$dat['2'];
    }
    //ada merubah host 4000
    $atontunnel=trim($aton.$tunnel);
    echo "UPDATE `centreon`.`host` SET `host_alias` = '$atontunnel' WHERE `host_name` = '".$dat['0']."';<br>";
}
fclose($file);

 ?>