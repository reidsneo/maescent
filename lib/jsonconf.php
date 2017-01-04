<?php 

echo "<pre>";
$tesarr=array(
	'r' =>array(
		'ssh' => array('u' => "guest",'pw' => "gu35t",'p' => "2222"),
		'telnet' => array('p' => "8000")
		),

	'a' =>array(
		'ssh' => array('u' => "guest",'pw' => "gu35t",'p' => "2222"),
		'telnet' => array('p' => "8000")
		),
	't' =>array(
		'ssh' => array('u' => "guest",'pw' => "gu35t",'p' => "2222"),
		'telnet' => array('p' => "8000")
		)
	);
print_r($tesarr);
echo "</pre>";
$nasi=serialize($tesarr);
//$woe= unserialize($nasi);
var_dump ($nasi);

 ?>