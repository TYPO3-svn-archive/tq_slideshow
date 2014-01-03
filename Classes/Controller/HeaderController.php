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

class Tx_TqSlideshow_Controller_HeaderController extends Tx_TqSlideshow_Controller_SlideshowController {
    /**
     * fetch the data
     */
    protected function _fetchData(){
        global $TSFE;

        $pageId = $TSFE->page['uid'];
        $params = array(
            'pageselector LIKE  \'%'.$pageId.'%\'',
        );
        $row    =  Slideshow::getSlideshow($params);

        if(empty($row)) {
            $params = array(
                'pageselector LIKE  \'%'.tx_tqslideshow_conf::rootPid().'%\'',
            );
            $row    =  Slideshow::getSlideshow($params);
        }
        $this->_data    = $row;

    }

}
?>