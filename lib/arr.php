<?php 
$data="nvs-offline ,cam0_failed-conn2srv_nok ,cam1_failed-conn2srv_nok ,cam2_failed-conn2srv_nok ,nNVS_2_nok";
$output=str_replace($findnya, $replacenya,$data);
$outputexp=array_unique(explode(",", $output));
$output=implode(",", $outputexp);
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
echo time();
echo $record."<br>".$nvs."<br>".$ssd."<br>".$pfttemp."<br>".$pftlux."<br>".$chkfilesize."<br>a".$nvs;
 ?>