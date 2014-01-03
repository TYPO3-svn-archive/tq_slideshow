<?php
namespace TQ\TqSlideshow\Backend\Ajax;


use TQ\TqSlideshow\Service\FalService;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Backend\Utility\IconUtility;


use \TQ\TqSlideshow\Utility\DatabaseUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 2013 TEQneers GmbH & Co. KG <info@teqneers.de>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * TYPO3 Backend ajax module client
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	tq_slideshow
 */
class CollectionController extends \TQ\TqSlideshow\Backend\Ajax\AbstractController {

    ###########################################################################
    # Attributs
    ###########################################################################

	/**
	 * The database table
	 *
	 * @var string
	 */
	protected $dbTable	= 'tx_tq_slideshow_collection';


    protected   $_allowedExtensions = array('jpg','png');

    /**
     * Db search field list
     *
     * @var array
     */
    protected $dbSearchFieldList = array(
        'main.title' => 'text',
    );

    ###########################################################################
    # Methods
    ###########################################################################

    /**
     * Init
     */
    protected function _init() {
        parent::_init();

        switch($this->sortField) {
            case '__status__':
                $this->sortField = null;
                break;
        }
    }

    /**
     * Process condition
     * @param   array   $condition
     * @return  array
     */
    protected function _processCondition($condition) {
        return $condition;
    }

    /**
     * Process get list result
     *
     * @param   array   $data
     * @return  array
     */
    protected function _processGetListResult(&$data) {

    }

    /**
     * Returns the list of images from a directory
     *
     * @param $path
     * @return array
     */
    public function imageList($path){
        $imageList  = array();

        if(empty($path)){
            return $imageList;
        }
        $path   = PATH_site.$path;
        if(is_dir($path)){
            $tmp  = scandir($path,1);
            foreach($tmp as $img ) {
                $imgInfo = pathinfo($path."/".$img);
                $ext    = $imgInfo['extension'];
                if(in_array($ext,$this->_allowedExtensions)) {
                    $imageList[] = $imgInfo;
                }
            }
        }
        return $imageList;
    }

    /**
     * Returns the Directory and file list from a parent directory
     *
     * @return array
     */
    protected function _executeGetFolder(){
        $rawPostVarList = GeneralUtility::_POST();
        $data           = array();

        $path           = $rawPostVarList['node'];
        $rootPath       = PATH_site.$path;
        $iterator       = new \DirectoryIterator($rootPath);

        foreach($iterator as $obj ) {
            if ($obj->isDir()) {
                if($obj->getFilename() == '.' || $obj->getFilename() == '..') {
                    continue;
                }
                 $data[]    = array(
                     'text' => $obj->getFilename(),
                     'id'   => $path.'/'.$obj->getFilename(),
                     'cls'  => 'folder'
                 );
            }

            if ($obj->isFile()) {
                if(in_array($obj->getExtension(), $this->_allowedExtensions )) {
                    $data[]    = array(
                        'text' => $obj->getFilename(),
                        'id'   => $path.'/'.$obj->getFilename(),
                        'leaf'  => true,
                        'cls'  => 'file'
                    );
                }
            }
        }
        return $data;
    }

    /**
     * Save the Images to Database and put them to the tq_slideshow directory
     */
    protected function _executeSaveImage(){
        $rawGetVarList = GeneralUtility::_GET();

        $argsSysFile    = array();
        $args           = array();

        $conf = \tx_tqslideshow_conf::getExtConf();

        $pid    = 0;
        if(!empty($conf['storagePID'])) {
            $pid    = $conf['storagePID'];
        }

        $dir        = $rawGetVarList['path'];
        $recordUid  = $rawGetVarList['uid'];

        // fetch images from directory
        $imageList  = $this->imageList($dir);

        $fileUtility    = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Utility\\File\\BasicFileUtility');
        $savePath       = PATH_site.'fileadmin/user_upload/tq_slideshow/collection-'.$recordUid;

        if(!$fileUtility->is_directory($savePath)) {
           mkdir($savePath);
        }
        foreach($imageList as $image ) {



            $uniqueName = $fileUtility->getUniqueName($image['basename'],$savePath);
            $orgImage   = $image['dirname'].'/'.$image['basename'];
            $newImage   = $uniqueName;

            $fileInfo   = $fileUtility->getTotalFileInfo($newImage);

            if (copy($orgImage,$newImage)) {

                $args   = array(
                    'recordId'      => $recordUid,
                    'uniqueName'    => $uniqueName,
                    'pid'           => $pid
                );

                $argsSysFile = array(
                    'pid'   => 0,
                    'tstamp'   => time(),
                    'crdate'   => time(),
                    'cruser_id'   => '',
                    'deleted'   => 0,
                    't3ver_oid'   => 0,
                    't3ver_id'   => 0,
                    't3ver_wsid'   => 0,
                    't3ver_label'   => 0,
                    't3ver_state'   => 0,
                    't3ver_stage'   => 0,
                    't3ver_count'   => 0,
                    't3ver_tstamp'   => 0,
                    't3ver_move_id'   => 0,
                    't3_origuid'   => 0,
                    'type'   => '',
                    'storage'   => '',
                    'identifier'   => '/fileadmin/user_upload/tq_slideshow/collection-'.$recordUid.'/'.$fileInfo['file'],
                    'extension'   => $fileInfo['fileext'],
                    'mime_type'   => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fileInfo['path'].$fileInfo['file']),
                    'name'   => $fileInfo['file'],
                    'title'   => null,
                    'sha1'   => sha1($fileInfo['path'].$image['basename']),
                    'size'   => filesize($fileInfo['path'].$fileInfo['file']),
                    'creation_date'   => time(),
                    'modification_date'   => time(),
                    'width'   => '',
                    'height'   => '',
                    'description'   => '',
                    'alternative'   => ''
                );

                FalService::addFile($argsSysFile,$args);

            }
        }
        return true;

    }
}
