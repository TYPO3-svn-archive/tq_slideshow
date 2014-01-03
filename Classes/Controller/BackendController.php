<?php
namespace TQ\TqSlideshow\Controller;

use \TQ\TqSlideshow\Utility\JsonUtility;
use \TQ\TqSlideshow\Utility\JsonExpressionUtility;
use \TQ\TqSlideshow\Utility\DatabaseUtility;

use \TYPO3\CMS\Backend\Utility\BackendUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nico Korthals (TEQneers GmbH & Co. KG) <korthals@teqneers.de>
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

class BackendController extends \TQ\TqSlideshow\Controller\AbstractController {

	#######################################################################
	# attributes
	#######################################################################

    /**
	 * The current page Id
	 *
     * @var integer
     */
    protected $pageId	= 1;

	/**
	 * The current Area
	 *
	 * @var type
	 */
	protected $_ajaxController	= 'slideshow';

	#######################################################################
	# methods
	#######################################################################

    /**
     * Initializes the controller before invoking an action method.
     *
     * @return void
     */
    protected function initializeAction() {
        parent::initializeAction();

        $this->languageKeyList += array(
            'button.create',
        );

        $conf = \tx_tqslideshow_conf::getExtConf();
        if(!empty($conf['storagePID'])) {
            $this->pageId   = $conf['storagePID'];
        }
    }

    protected function _moduleConf() {
        $ret = parent::_moduleConf();



        ###############################
        # Categories
        ###############################

        $query = 'SELECT c.uid,
                         c.title
                    FROM sys_category c
                   WHERE c.parent = 0
                     AND c.deleted = 0
                     AND c.hidden = 0';
        $ret['categoryList'] = DatabaseUtility::getAll($query);

        ###############################
        # Links
        ###############################

        // Create
        $params='edit[tx_tq_slideshow]['.$this->pageId.']=new';
        $returnUrl = 'mod.php?M=web_TqSlideshowBackend&action=windowClose';
        $url = 'alt_doc.php?returnUrl='.rawurlencode($returnUrl).'&'.$params;
        $ret['url.slideshow.create'] = $url;

        // Show
        $params='edit[tx_tq_slideshow][T3_DATABASE_ENTITY_UID]=edit';
        $returnUrl = 'mod.php?M=web_TqSlideshowBackend&action=windowClose';
        $url = 'alt_doc.php?returnUrl='.rawurlencode($returnUrl).'&'.$params;
        $ret['url.slideshow.show'] = $url;

        // Edit
        $params='edit[tx_tq_slideshow][T3_DATABASE_ENTITY_UID]=edit';
        $returnUrl = 'mod.php?M=web_TqSlideshowBackend&action=windowClose';
        $url = 'alt_doc.php?returnUrl='.rawurlencode($returnUrl).'&'.$params;
        $ret['url.slideshow.edit'] = $url;

        return $ret;
    }




}
