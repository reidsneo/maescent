<?php
$filepath=$_POST['filepath'];
$starttime=$_POST['starttime'];
$endtime=$_POST['endtime'];
$myfile = fopen($filepath, "r") or die("Unable to open file!");
if(isset($starttime) AND isset($endtime)){
	echo "SORT MODE [ENABLED]<br><br>";
	$start=intval(str_replace(":","",$starttime));
	$end=intval(str_replace(":","",$endtime));
	while(!feof($myfile)) {
		$time=intval(str_replace(":","",substr(fgets($myfile),0,8)));
		if($time>=$start && $time<=$end){
			echo fgets($myfile)."<br>";
		}
	}
}else{
	while(!feof($myfile)) {
	  echo fgets($myfile)."<br>";
	}
}

fclose($myfile);
?> 