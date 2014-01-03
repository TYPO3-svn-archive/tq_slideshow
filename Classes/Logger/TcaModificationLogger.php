<?php
namespace TQ\TqSlideshow\Logger;

use \TQ\TqSlideshow\Utility\DatabaseUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class TcaModificationLogger extends \Tq\TqSlideshow\Logger\AbstractLogger {

    /**
     * Find log messages for a table relation
     *
     * @param   string  $table  Table name
     * @param   integer $uid    Table row id
     * @return  array
     */
    public function findListByRelation($table, $uid) {

        $query = 'SELECT log.uid as row_key,
                         log.*,
                         FROM_UNIXTIME(log.crdate) as log_timestamp
                    FROM wtv_log_tca log
                   WHERE log.rel_table_name = '.DatabaseUtility::quote($table).'
                     AND log.rel_table_uid = '.(int)$uid.'
                ORDER BY crdate DESC
                   LIMIT 15';
        $ret = DatabaseUtility::getAllIndexed($query);

        return $ret;
    }

    /**
     * Log message (raw)
     *
     * @param   string  $level      Log level
     * @param   string  $message    Message
     * @param   array   $context    Context
     */
    protected function _logMessage($level, $message, array $context = array()) {

    }

}