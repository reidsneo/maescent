<?php

/*
 * Copyright 2005-2015 Centreon
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
 * SVN : $URL$
 * SVN : $Id$
 *
 */

class CentreonGMT {

    var $listGTM;
    var $myGMT;
    var $use;
    /**
     *
     * @var array
     */
    var $aListTimezone;
        
    /**
     *
     * @var type 
     */
    protected $db;
    
    /**
     * 
     * @param string $myTimezone
     */
    var $myTimezone;
    
    /**
     * 
     * @param string $myOffset
     */
    var $myOffset;

    public function __construct($DB)
    {
        $this->db = $DB;
        
        /*
         * Define Table of GMT line
         */
        $this->listGTM = $this->getList();
        /*
         * Flag activ / inactiv
         */
        $this->use = $this->checkGMTStatus($DB);
    }
    
    function checkGMTStatus($DB) {
        return 1;
    }

    function used() {
        return $this->use;
    }

    function setMyGMT($value)
    {
        $this->myGMT = $value;
    }

    function getGMTList() {
        return $this->listGTM;
    }

    function getMyGMT() {
        return $this->myGMT;
    }

    function getMyTimezone()
    {
        if (is_null($this->myTimezone)) {
            if (isset($this->listGTM[$this->myGMT])) {
                $this->myTimezone = $this->listGTM[$this->myGMT];
            } else {
                $this->myTimezone = date_default_timezone_get();
            }
        }
        return $this->myTimezone;
    }
    function getMyOffset()
    {
        if (is_null($this->myOffset)) {
            if (count($this->aListTimezone) == 0) {
                $this->getList();
            }
            $this->myOffset = $this->aListTimezone[$this->myGMT]['timezone_offset'];
        }
        return $this->myOffset;
    }
    function getMyGMTForRRD()
    {
        $sOffset = '';
        if (count($this->listGTM) == 0) {
            $this->getList();
        }

        if (isset($this->aListTimezone[$this->myGMT]['timezone_offset'])) {
           $sOffset = $this->aListTimezone[$this->myGMT]['timezone_offset'];
        }
        return $sOffset;
    }

    function getDate($format, $date, $gmt = NULL)
    {
        $return = "";
        if (!$date) {
            $date = "N/A";
        }
        if ($date == "N/A") {
            return $date;
        }
        /*
         * Specify special GMT
         */
        if (!isset($gmt)) {
            $gmt = $this->myGMT;
        }

        if ($this->use) {
            if (isset($date) && isset($gmt)) {
                if (count($this->listGTM) == 0) {
                    $this->getList();
                }
                
                if (isset($this->listGTM[$gmt]) && !empty($this->listGTM[$gmt])) {
                    $sDate = new DateTime();
                            
                    $sDate->setTimestamp($date);
                    $sDate->setTimezone(new DateTimeZone($this->listGTM[$gmt]));
                    $return = $sDate->format($format);
                } else {
                    $return = date($format, $date);
                }

                return $return;
            } else {
                return "";
            }
        } else {
            return date($format, $date);
        }
    }

    function getUTCDate($date, $gmt = NULL) {
        /*
         * Specify special GMT
         */
        $return = "";
        if (!isset($gmt))
            $gmt = $this->myGMT;

        if ($this->use) {
            if (isset($date) && isset($gmt)) {
                if (count($this->listGTM) == 0) {
                    $this->getList();
                }
                
                if (isset($this->listGTM[$gmt]) && !empty($this->listGTM[$gmt])) {
                    
                    $sDate = new DateTime();
                    $sDate->setTimestamp($date);
                    
                    $sDate->setTimezone(new DateTimeZone($this->listGTM[$gmt]));
                    $iTimestamp = $sDate->getTimestamp();

                    $sOffset = $sDate->getOffset();
                    
                    $return = $iTimestamp + $sOffset;
                    
                } else {
                    $return = $date;
                }
                
            } else {
                $return = "";
            }
        } else {
            $return = $date;
        }
        return $return;
    }
    
    function getUTCDateFromString($date, $gmt = NULL){
        /*
         * Specify special GMT
         */

        
        $return = "";
        if (!isset($gmt))
            $gmt = $this->myGMT;
        if ($this->use) {
            if (isset($date) && isset($gmt)) {
                if (count($this->listGTM) == 0) {
                    $this->getList();
                }
                
                if (isset($this->listGTM[$gmt]) && !empty($this->listGTM[$gmt])) {
                    
                    $sDate = new DateTime($date,new DateTimeZone($this->listGTM[$gmt]));
                    $iTimestamp = $sDate->getTimestamp();
                    $return = $iTimestamp;
                } else {
                    $sDate = new DateTime($date);
                    $iTimestamp = $sDate->getTimestamp();
                    $return = $iTimestamp;
                }
                
            } else {
                $return = "";
            }
        } else {
            $return = $date;
        }
        return $return;
    }
    

    function getDelaySecondsForRRD($gmt) {
        $str = "";
        if ($gmt) {
            if ($gmt > 0)
                $str .= "+";
        } else {
            return "";
        }
    }

    function getMyGMTFromSession($sid = NULL, $DB) {
        global $pearDB;

        if (!isset($pearDB) && isset($DB))
            $pearDB = $DB;

        if (!isset($sid))
            return 0;

        $DBRESULT = $pearDB->query("SELECT `contact_location` FROM `contact`, `session` " .
                "WHERE `session`.`user_id` = `contact`.`contact_id` " .
                "AND `session_id` = '" . CentreonDB::escape($sid) . "' LIMIT 1");
        if (PEAR::isError($DBRESULT)) {
            $this->myGMT = 0;
        }
        $info = $DBRESULT->fetchRow();
        $DBRESULT->free();
        $this->myGMT = $info["contact_location"];
    }
    
    function getMyGTMFromUser($userId, $DB = null)
    {
        global $pearDB;
        
        if (!isset($pearDB) && isset($DB)) {
            $pearDB = $DB;
        }
        
        $DBRESULT = $pearDB->query("SELECT `contact_location` FROM `contact` " .
                "WHERE `contact`.`contact_id` = " . $userId .
                " LIMIT 1");
        if (PEAR::isError($DBRESULT)) {
            $this->myGMT = 0;
        }
        $info = $DBRESULT->fetchRow();
        $DBRESULT->free();
        $this->myGMT = $info["contact_location"];
    }

    function getHostCurrentDatetime($host_id, $date_format = 'c') {
        global $pearDB;
        static $locations = null;

        $date = time();
        $sReturn = date($date_format, $date);
        
        if ($this->use) {
            if (is_null($locations)) {
                $locations = array();

                $query = "SELECT host_id, host_location FROM host WHERE host_id";
                $res = $pearDB->query($query);
                while ($row = $res->fetchRow()) {
                    $locations[$row['host_id']] = $row['host_location'];
                }
            }
            if (isset($locations[$host_id]) && isset($this->listGTM[$locations[$host_id]]) && !empty($this->listGTM[$locations[$host_id]])) {          
                $sDate = new DateTime();
                $sDate->setTimezone(new DateTimeZone($this->listGTM[$locations[$host_id]]));
                $sReturn = $sDate->format($date_format);
            }
        }
        
        return $sReturn;
    }

    function getUTCDateBasedOnHostGMT($date, $hostId, $dateFormat = 'c')
    {
        global $pearDB;
        static $locations = null;

        if ($this->use) {
            /* Load host location */
            if (is_null($locations)) {
                $locations = array();
                $query = "SELECT host_id, host_location FROM host WHERE host_id";
                $res = $pearDB->query($query);
                while ($row = $res->fetchRow()) {
                    $locations[$row['host_id']] = $row['host_location'];
                }
            }
            if (isset($locations[$hostId])) {
                $date = $this->getUTCDate($date, $locations[$hostId]);
            }
        }
        return date($dateFormat, $date);
    }

    function getUTCTimestampBasedOnHostGMT($date, $hostId, $dateFormat = 'c')
    {
        global $pearDB;
        static $locations = null;

        if ($this->use) {
            /* Load host location */
            if (is_null($locations)) {
                $locations = array();
                $query = "SELECT host_id, host_location FROM host WHERE host_id";
                $res = $pearDB->query($query);
                while ($row = $res->fetchRow()) {
                    $locations[$row['host_id']] = $row['host_location'];
                }
            }
            if (isset($locations[$hostId])) {
                $date = $this->getUTCDate($date, $locations[$hostId]);
            }
        }
        return $date;
    }
    
    function getUTCLocationHost($hostId){
        global $pearDB;
        static $locations = null;

        if ($this->use) {
            /* Load host location */
            if (is_null($locations)) {
                $locations = array();
                $query = "SELECT host_id, host_location FROM host WHERE host_id";
                $res = $pearDB->query($query);
                while ($row = $res->fetchRow()) {
                    $locations[$row['host_id']] = $row['host_location'];
                }
            }
            if (isset($locations[$hostId])) {
                return $locations[$hostId];
            }
        }
        return null;
    }
    
    /**
     * Get the list of timezone
     *
     * @return array
     */
    public function getList()
    {
        $aDatas = array();
        
        $queryList = "SELECT timezone_id, timezone_name, timezone_offset FROM timezone ORDER BY timezone_name asc";
        $res = $this->db->query($queryList);
        if (PEAR::isError($res)) {
            return array();
        }
 
        $aDatas[null] = null;
        while ($row = $res->fetchRow()) {
            $aDatas[$row['timezone_id']] =  $row['timezone_name'];
            $this->aListTimezone[$row['timezone_id']] = $row;
        }
         
        return $aDatas;
    }
    
    /**
     * 
     * @param type $values
     * @return type
     */
    public function getObjectForSelect2($values = array(), $options = array())
    {
        $items = array();
        
        $explodedValues = implode(',', $values);
        if (empty($explodedValues)) {
            $explodedValues = "''";
        }

        # get list of selected timezones
        $query = "SELECT timezone_id, timezone_name "
            . "FROM timezone "
            . "WHERE timezone_id IN (" . $explodedValues . ") "
            . "ORDER BY timezone_name ";
        
        $resRetrieval = $this->db->query($query);
        while ($row = $resRetrieval->fetchRow()) {
            $items[] = array(
                'id' => $row['timezone_id'],
                'text' => $row['timezone_name']
            );
        }

        return $items;
    }
}
