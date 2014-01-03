<?php

use TQ\TqSlideshow\Utility\DatabaseUtility;
use TQ\TqSlideshow\Utility\Database\Slideshow;
use TQ\TqSlideshow\Utility\Database\Media;
use TQ\TqSlideshow\Utility\Database\Collection;
use TQ\TqSlideshow\Service\FalService;

use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nico Korthals (TEQneers GmbH & Co. KG) <korthals@teqneers.de>
*  All rights reserved#
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
class Tx_TqSlideshow_Controller_ContentController extends Tx_TqSlideshow_Controller_SlideshowController {
    /**
     * fetch the data
     */
    protected function _fetchData(){

        if(!empty($this->settings['slideshow_id'])) {
            $recordId   = $this->settings['slideshow_id'];
        }

        $params = array(
            'uid = '.(int)$recordId
        );

        $row    =  Slideshow::getSlideshow($params);
        $row    = array_merge($row,$this->settings);
        $this->_data = $row;

    }

}
?>