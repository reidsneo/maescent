<?php
isset($_GET["host_id"]) ? $hG = $_GET["host_id"] : $hG = NULL;
isset($_POST["host_id"]) ? $hP = $_POST["host_id"] : $hP = NULL;
$hG ? $host_id = $hG : $host_id = $hP;

isset($_GET["select"]) ? $cG = $_GET["select"] : $cG = NULL;
isset($_POST["select"]) ? $cP = $_POST["select"] : $cP = NULL;
$cG ? $select = $cG : $select = $cP;

isset($_GET["dupNbr"]) ? $cG = $_GET["dupNbr"] : $cG = NULL;
isset($_POST["dupNbr"]) ? $cP = $_POST["dupNbr"] : $cP = NULL;
$cG ? $dupNbr = $cG : $dupNbr = $cP;

/*
 * Pear library
 */
require_once "HTML/QuickForm.php";
require_once 'HTML/QuickForm/select2.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

/*
 * Path to the configuration dir
 */
global $path;

$path = "./include/configuration/configObject/host/";

/*
 * PHP functions
 */
require_once $path."DB-Funcadd.php";
require_once "./include/common/common-Func.php";

if (isset($_POST["o1"]) && isset($_POST["o2"])){
    if ($_POST["o1"] != "") {
        $o = $_POST["o1"];
    }
    if ($_POST["o2"] != "") {
        $o = $_POST["o2"];
    }
 }

/* Set the real page */
if ($ret['topology_page'] != "" && $p != $ret['topology_page']) {
    $p = $ret['topology_page'];
 }

$acl = $oreon->user->access;
$dbmon = new CentreonDB('centstorage');
$aclDbName = $acl->getNameDBAcl($oreon->broker->getBroker());
$hgs = $acl->getHostGroupAclConf(null, $oreon->broker->getBroker());
$aclHostString = $acl->getHostsString('ID', $dbmon);
$aclPollerString = $acl->getPollerString();
require_once($path."formHostadd.php");
?>
