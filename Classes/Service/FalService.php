<?php
namespace TQ\TqSlideshow\Service;

use TYPO3\CMS\Core\Resource\StorageRepository;
use TQ\TqSlideshow\Utility\DatabaseUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Blaschke, TEQneers GmbH & Co. KG <info@teqneers.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

class FalService {

    /**
     * Find FAL entries for a table relation
     *
     * @param   string  $table  Table name
     * @param   string  $field  Table field name
     * @param   string  $uid    Table row id
     * @return  array
     */
    public static function findByRelation($table, $field, $uid) {
        $oldErrorReporting = error_reporting();
        error_reporting(0);

        $ret = array();
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $fileList = $fileRepository->findByRelation($table, $field, $uid);

        if( !empty($fileList) ) {
            foreach($fileList as $file) {
                try {
                    /* @var $file \TYPO3\CMS\Core\Resource\FileReference */
                    $ret[] = $file->toArray();
                } catch( \Exception $e ) {
                    // No file found
                }
            }
        }

        error_reporting($oldErrorReporting);

        return $ret;
    }

    /**
     *  Add a file to the FAL archtitectur
     *
     * @param array $argsSysFile    The argument lsit for the sys_file
     * @param array $args           The argument lsit for media and reference list
     */
    public function addFile($argsSysFile, $args ) {
        $keys   = array_keys($argsSysFile);
        $values    = array_values($argsSysFile);

        foreach($values as &$val ) {
            $val    = DatabaseUtility::quote($val);
        }
        unset($val);

        $query      = 'INSERT INTO sys_file ('.implode(',',$keys).') VALUES ('.implode(',',$values).')';
        $uidLocal   = DatabaseUtility::execInsert($query);

        $query      = 'INSERT INTO tx_tq_slideshow_media
                            (title,pid,collection_id,media_type,crdate,image) VALUES
                             ('.DatabaseUtility::quote(basename($args['uniqueName'])).','.$args['pid'].','.$args['recordId'].',1,'.time().','.DatabaseUtility::quote('collection-'.$args['recordId'].'/'.basename($args['uniqueName'])).')';
        $uidForeign =  DatabaseUtility::execInsert($query);

        if( $uidLocal && $uidForeign ) {
            // create reference record
            $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_file_reference', array(
                    'pid' => $args['pid'],
                    'crdate' => time(),
                    'tstamp' => time(),
                    'sorting' => 0,
                    'uid_local' => $uidLocal,
                    'uid_foreign' => $uidForeign,
                    'tablenames' => 'tx_tq_slideshow_media',
                    'fieldname' => 'image',
                    'sorting_foreign' => 0,
                    'table_local' => 'sys_file',
                ));
        }
    }

}