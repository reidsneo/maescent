<?php
/*
 * Copyright 2005-2011 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

require_once "../../require.php";
require_once "./DB-Func.php";
require_once $centreon_path . 'www/class/centreon.class.php';
require_once $centreon_path . 'www/class/centreonSession.class.php';
require_once $centreon_path . 'www/class/centreonDB.class.php';
require_once $centreon_path . 'www/class/centreonWidget.class.php';
require_once $centreon_path . 'www/class/centreonUtils.class.php';
require_once $centreon_path . 'www/class/centreonACL.class.php';

require_once $centreon_path ."GPL_LIB/Smarty/libs/Smarty.class.php";

session_start();

if ( !isset($_SESSION['centreon'])  ||  !isset($_REQUEST['widgetId'])   ||  !isset($_REQUEST['page'] )) {
  exit;
}


$centreon = $_SESSION['centreon'];

$db = new CentreonDB();
if (CentreonSession::checkSession(session_id(), $db) == 0) {
    exit;
}
$dbb = new CentreonDB("centstorage");

$path = $centreon_path . "www/widgets/logs/src/";
$template = new Smarty();
$template = initSmartyTplForPopup($path, $template, "./", $centreon_path);

$centreon = $_SESSION['centreon'];
$widgetId = $_REQUEST['widgetId'];
$page = $_REQUEST['page'];

$widgetObj = new CentreonWidget($centreon, $db);
$preferences = $widgetObj->getWidgetPreferences($widgetId);

// Get status colors
$stateHColors = getStatusColors($db, 'host');
$stateSColors = getStatusColors($db, 'service');
$stateINColors = getStatusColors($db, 'info');

// Get status labels
$stateHLabels = getStatusLabels('host');
$stateSLabels = getStatusLabels('service');

// Get type labels
$typeLabels = getTypeLabels();

$host_msg_status_set = array();
if (isset($preferences['host_up']) && $preferences['host_up'] == "1")
    array_push($host_msg_status_set, "'0'");
if (isset($preferences['host_down']) && $preferences['host_down'] == "1")
    array_push($host_msg_status_set, "'1'");
if (isset($preferences['host_unreachable']) && $preferences['host_unreachable'] == "1")
    array_push($host_msg_status_set, "'2'");

$svc_msg_status_set = array();
if (isset($preferences['service_ok']) && $preferences['service_ok'] == "1")
    array_push($svc_msg_status_set, "'0'");
if (isset($preferences['service_warning']) && $preferences['service_warning'] == "1")
    array_push($svc_msg_status_set, "'1'");
if (isset($preferences['service_critical']) && $preferences['service_critical'] == "1")
    array_push($svc_msg_status_set, "'2'");
if (isset($preferences['service_unknown']) && $preferences['service_unknown'] == "1")
    array_push($svc_msg_status_set, "'3'");

$msg_req = '';
$flag_begin = 0;

// Display notification
if (isset($preferences['notification']) && $preferences['notification'] == "1") {
    if (count($host_msg_status_set)) {
        $flag_begin = 1;
        $msg_req .= " (`msg_type` = '3' AND `status` IN (" . implode(',', $host_msg_status_set)."))";
    }
    if (count($svc_msg_status_set)) {
        if ($flag_begin) {
            $msg_req .= " OR ";
        } else {
            $msg_req .= "(";
        }
        $msg_req .= " (`msg_type` = '2' AND `status` IN (" . implode(',', $svc_msg_status_set)."))";
        if (!$flag_begin) {
            $msg_req .= ") ";
        }
        $flag_begin = 1;
    }
}

// Display alert
if (isset($preferences['alert']) && $preferences['alert'] == "1") {
    if (count($host_msg_status_set)) {
        if ($flag_begin) {
            $msg_req .= " OR ";
        }
        $flag_begin = 1;
        $msg_req .= " ((`msg_type` IN ('1', '10', '11') AND `status` IN (" . implode(',', $host_msg_status_set).")) ";
        if ($preferences['state_type_filter'] == "hardonly") {
            $flag_begin = 1;
            $msg_req .= " AND `type` = '1' ";
        } else if ($preferences['state_type_filter'] == "softonly") {
            $flag_begin = 1;
            $msg_req .= " AND `type` = '0' ";
        }
        $msg_req .= ") ";
    }
    if (count($svc_msg_status_set)) {
        if ($flag_begin) {
            $msg_req .= " OR ";
        }
        $flag_begin = 1;
        $msg_req .= " ((`msg_type` IN ('0', '10', '11') AND `status` IN (" . implode(',', $svc_msg_status_set).")) ";
        if ($preferences['state_type_filter'] == "hardonly") {
            $flag_begin = 1;
            $msg_req .= " AND `type` = '1' ";
        } else if ($preferences['state_type_filter'] == "softonly") {
            $flag_begin = 1;
            $msg_req .= " AND `type` = '0' ";
        }
        $msg_req .= ") ";
    }
}

// Display error
if (isset($preferences['error']) && $preferences['error'] == "1") {
    if ($flag_begin == 0) {
        $msg_req .= " AND ";
    } else {
        $msg_req .= " OR ";
    }
    $msg_req .= " (`msg_type` IN ('4') AND `status` IS NULL) ";
}

// Display info
if (isset($preferences['info']) && $preferences['info'] == "1") {
    if ($flag_begin == 0) {
        $msg_req .= " AND ";
    } else {
        $msg_req .= " OR ";
    }
    $msg_req .= " (`msg_type` IN ('5'))";
}

if ($flag_begin) {
    $msg_req = " AND (".$msg_req.") ";
}

// Remove virtual hosts and services
$msg_req .= " AND host_name NOT LIKE '%_Module_%'";

// Search on object name
if (isset($preferences['object_name_search']) && $preferences['object_name_search'] != "") {
    $tab = split(" ", $preferences['object_name_search']);
    $op = $tab[0];
    if (isset($tab[1])) {
        $search = $tab[1];
    }
    if ($op && isset($search) && $search != "") {
        $msg_req .= " AND (host_name ".CentreonUtils::operandToMysqlFormat($op)." '".$dbb->escape($search)."' ";
        $msg_req .= " OR service_description ".CentreonUtils::operandToMysqlFormat($op)." '".$dbb->escape($search)."' ";
        $msg_req .= " OR instance_name ".CentreonUtils::operandToMysqlFormat($op)." '".$dbb->escape($search)."') ";
    }
}

// Search on output
if (isset($preferences['output_search']) && $preferences['output_search'] != "") {
    $tab = split(" ", $preferences['output_search']);
    $op = $tab[0];
    if (isset($tab[1])) {
        $outputSearch = $tab[1];
    }
    if ($op && isset($outputSearch) && $outputSearch != "") {
        $msg_req .= " AND output ".CentreonUtils::operandToMysqlFormat($op)." '".$dbb->escape($outputSearch)."' ";
    }
}

// Build final request
$orderby = "name ASC";
if (isset($preferences['order_by']) && $preferences['order_by'] != "") {
  $orderby = $preferences['order_by'];
}

$start = time() - $preferences['log_period'];
$end = time();
$query = "SELECT SQL_CALC_FOUND_ROWS * FROM logs WHERE ctime > '$start' AND ctime <= '$end' $msg_req";
$query .= " ORDER BY ctime DESC, host_name ASC, log_id DESC, service_description ASC";
$query .= " LIMIT ".($page * $preferences['entries']).",".$preferences['entries'];
$res = $dbb->query($query);
$nbRows = $dbb->numberRows();
$data = array();
$outputLength = $preferences['output_length'] ? $preferences['output_length'] : 50;

if (!$centreon->user->admin) {
    $pearDB = $db;
    $aclObj = new CentreonACL($centreon->user->get_id(), $centreon->user->get_admin());
    $lca = array("LcaHost" => $aclObj->getHostServices($dbb, null, 1));
}

while ($row = $res->fetchRow()) {
    if (!$centreon->user->admin) {
        $continue = true;
        if (isset($row['host_id']) && isset($lca['LcaHost'][$row['host_id']])) {
            $continue = false;
        } elseif (isset($row['host_id']) && isset($row['service_description'])) {
            foreach ($lca['LcaHost'][$row['host_id']] as $key => $value) {
                if ($value == $row['service_description']) {
                    $continue = false;
                }
            }
        }
        if ($continue == true) {
            continue;
        }
    }
    if (isset($row['host_name']) && $row['host_name'] != "") {
        $data[$row['log_id']]['object_name1'] = $row['host_name'];
    } elseif (isset($row['instance_name']) && $row['instance_name'] != "") {
        $data[$row['log_id']]['object_name1'] = $row['instance_name'];
    } else {
        $data[$row['log_id']]['object_name1'] = "";
    }
    if (isset($row['service_description']) && $row['service_description'] != "") {
        $data[$row['log_id']]['object_name2'] = $row['service_description'];
    } else {
        $data[$row['log_id']]['object_name2'] = "";
    }
    foreach ($row as $key => $value) {
        if ($key == "ctime") {
            $value = date("Y-m-d H:i:s", $value);
        } elseif ($key == "status") {
            if (isset($row['service_description']) && $row['service_description'] != "") {
                $data[$row['log_id']]['color'] = $stateSColors[$value];
                $value = $stateSLabels[$value];
            } else if (isset($row['host_name']) && $row['host_name'] != "") {
                $data[$row['log_id']]['color'] = $stateHColors[$value];
                $value = $stateHLabels[$value];
            } else {
	            $data[$row['log_id']]['color'] = $stateINColors[$value];
		        $value = "Info";
            }
        } elseif ($key == "output") {
            $value = substr($value, 0, $outputLength);
        } elseif ($key == "type") {
            if (isset($row['host_name']) && $row['host_name'] != "") {
                $value = $typeLabels[$value];
            } else {
                $value = "";
            }
        } elseif ($key == "retry") {
            if (!isset($row['host_name']) || $row['host_name'] == "") {
                $value = "";
            }
        }
        $data[$row['log_id']][$key] = $value;
    }
}

$template->assign('widgetId', $widgetId);
$template->assign('autoRefresh', $autoRefresh);
$template->assign('preferences', $preferences);
$template->assign('nbRows', $nbRows);
$template->assign('nblines', ($preferences['entries'] > $nbRows ? $nbRows : $preferences['entries']));
$template->assign('page', $page);
$template->assign('orderby', $orderby);
$template->assign('dataJS', count($data));
$template->assign('centreon_web_path', trim($centreon->optGen['oreon_web_path'], "/"));
$template->assign('preferences', $preferences);
$template->assign('data', $data);

/* Display Widget */
$template->display('index.ihtml');
