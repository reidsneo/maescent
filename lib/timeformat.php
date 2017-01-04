<?php
/*
function translateTime($timeString) {
    if (preg_match('/^(?:(?<days>\d+)d\s*)?((?<hours>\d+)h\s*)?(?:(?<minutes>\d+)m\s*)?(?:(?<seconds>\d+)s\s*)?$/', $timeString, $matches)) {
        return sprintf(
            '%02s:%02s:%02s:%02s',
            (!empty($matches['days'])   ? $matches['days']   : '00'),
            (!empty($matches['hours'])   ? $matches['hours']   : '00'),
            (!empty($matches['minutes']) ? $matches['minutes'] : '00'),
            (!empty($matches['seconds']) ? $matches['seconds'] : '00')
        );
    }

    return '00:00:00';
}
var_dump( translateTime('6h3m54s') ); 
var_dump( translateTime('9h44m20s') ); 
var_dump( translateTime('9h4m8s') ); 
var_dump( translateTime('2m14s') ); 
var_dump( translateTime('1h47m59s') ); 
var_dump( translateTime('1d05:25:04') ); 
var_dump( translateTime('1d24m58s') ); 
*/

function timegap($time){
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

function calctime($timegap){
	if(count($timegap['day'])>0){
		$hour=$timegap['day']*24;
		$parts = explode(':', $timegap['time']);
		$sumtime=($hour+$parts[0]);
		if(strlen($sumtime)<2){
			$sumtime="0".$sumtime;
		}
		return $sumtime.":".$parts[1].":".$parts[2];
	}else{
		return $timegap['time'];
	}
}

var_dump( timegap('6h3m54s') ); 
echo "<br>";
var_dump( timegap('9h44m20s') ); 
echo "<br>";
var_dump( timegap('9h4m8s') ); 
echo "<br>";
var_dump( timegap('2m14s') ); 
echo "<br>";
var_dump( timegap('24h47m59s') ); 
echo "<br>";
var_dump( timegap('4d05:25:04') ); 
echo "<br>";
var_dump( timegap('1d24m58s') );

$dateval="1d00:43:59";
echo "<hr><br>";
var_dump(calctime(timegap($dateval)));
?>