<?php
/*
 * Copyright 2005-2016 Centreon
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
 * As a special exception, the copyright holders of this program give Centreon
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of Centreon choice, provided that
 * Centreon also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

function getTypeLabels() {
    return array(
        0 => "SOFT",
        1 => "HARD"
    );
}

function getStatusLabels($object = 'host') {
    $statusLabels = array();
    if ($object == 'host') {
        $statusLabels = array(
            0 => "Up",
            1 => "Down",
            2 => "Unreachable",
            3 => "Pending"
        );
    } elseif ($object == 'service') {
        $statusLabels = array(
            0 => "Ok",
            1 => "Warning",
            2 => "Critical",
            3 => "Unknown",
            4 => "Pending"
        );
    }

    return $statusLabels;
}

function getStatusColors($db, $object = 'host') {
    $statusHColors = array(
        0 => "#13EB3A",
        1 => "#F91D05",
        2 => "#DCDADA",
        3 => "#2AD1D4"
    );
    $statusSColors = array(
        0 => "#13EB3A",
        1 => "#F8C706",
        2 => "#F91D05",
        3 => "#DCDADA",
        4 => "#2AD1D4"
    );
    $statusINColors = array(
        -1 => "#00bfb3",
    );

    $res = $db->query("SELECT `key`, `value` FROM `options` WHERE `key` LIKE 'color%'");
    while ($row = $res->fetchRow()) {
        if ($row['key'] == "color_ok") {
            $statusSColors[0] = $row['value'];
        } elseif ($row['key'] == "color_warning") {
            $statusSColors[1] = $row['value'];
        } elseif ($row['key'] == "color_critical") {
            $statusSColors[2] = $row['value'];
        } elseif ($row['key'] == "color_unknown") {
            $statusSColors[3] = $row['value'];
        } elseif ($row['key'] == "color_pending") {
            $statusSColors[4] = $row['value'];
        } elseif ($row['key'] == "color_up") {
            $statusHColors[4] = $row['value'];
        } elseif ($row['key'] == "color_down") {
            $statusHColors[4] = $row['value'];
        } elseif ($row['key'] == "color_unreachable") {
            $statusHColors[4] = $row['value'];
        }
    }

    $statusColors = array();
    if ($object == 'host') {
        $statusColors = $statusHColors;
    } elseif ($object == 'service') {
        $statusColors = $statusSColors;
    } elseif ($object == 'info') {
        $statusColors = $statusINColors;
    }

    return $statusColors;
}

