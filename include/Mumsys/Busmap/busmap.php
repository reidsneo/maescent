<?php
require_once realpath(dirname(__FILE__).'/../common.php');

$busmap = array();
$MNPrack = new CentreonDB("mnp_rack");
$DBStorage = new CentreonDB("centstorage");
$DB = new CentreonDB("centreon");


  $qhostgroup = $DB->getAll("SELECT hg_id,hg_name FROM `hostgroup` WHERE hg_id!=60 AND hg_id!=55 AND hg_id!=58 AND hg_id!=56 AND hg_id!=59 AND hg_id!=61 AND hg_id!=53 AND hg_id!=54 ORDER BY hg_name");
  //$qdtbus = $DBStorage->getAll("SELECT h.`host_id`,h.name AS hostname,hg.`name` AS hostgroup,h.address,h.alias FROM `hosts` h, hostgroups hg,hosts_hostgroups hgid WHERE hgid.host_id=h.`host_id` AND hgid.`hostgroup_id`=hg.`hostgroup_id` AND h.name!='Centreon-Server' AND h.name!='Cabang1' AND h.name!='Cabang2' AND h.name!='localrak-xxx19' AND h.name!='localrak-xxx242' AND h.name!='Arriva_Server'");
    $qdtbus = $DBStorage->getAll("SELECT h.`host_id`,h.name AS hostname,hg.`hg_name` AS hostgroup,h.address,h.alias FROM `centreon_storage`.`hosts` h, `centreon`.`hostgroup` hg,`centreon_storage`.`hosts_hostgroups` hgid WHERE hgid.host_id=h.`host_id` AND hgid.`hostgroup_id`=hg.`hg_id` AND h.name!='Centreon-Server' AND h.name!='Cabang1' AND h.name!='Cabang2' AND h.name!='localrak-xxx19' AND h.name!='localrak-xxx242' AND h.name!='Arriva_Server'");
    foreach ($qdtbus as $key => $val) {
      
    }

  

  $tpl = new Smarty();
  $tpl = initSmartyTpl($path, $tpl);
  $tpl->assign('hostlist', $qhostgroup);
  $tpl->assign('busdata', $qdtbus);
  $tpl->assign('urlsavebusdata', realpath(dirname(__FILE__).'/busmap_act.php'));
  $tpl->assign('msg_interval', "rew");
  $tpl->display(realpath(dirname(__FILE__))."/busmap.tpl");
/*
$i=0;
while($busmaps = $DBRESULT->fetchRow()) {
    $busmap[$i] = array('id' =>$busmaps["bus_id"],'x' =>$busmaps["bus_x"],'y' =>$busmaps["bus_y"],'ip' =>$busmaps["bus_ip"]);
	$i=$i+1;
}
/*
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}
$b = array($busmap,$busdata);
foreach ($busmap as $key => $val) {
	if(in_array_r($val['id'], $b)){
		$newbusdata[$key]=array('id' =>$val['id'],'x' =>$busmap[$key]['x'],'y' =>$busmap[$key]['y']);
	}
}
function array_merge_recursive_distinct(array &$array1, array &$array2) {
  $merged = $array1;

  foreach($array2 as $key => &$value) {
    if(is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
      $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
    } else {
      $merged[$key] = $value;
    }
  }

  return $merged;
}
*
$result = array_merge_recursive_distinct($newbusdata,$busdata);
//echo "<pre>".print_r($result)."</pre>";
*/
?>