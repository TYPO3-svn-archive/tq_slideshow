<?php
namespace TQ\TqSlideshow\Backend\Ajax;

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
class SlideshowController extends \TQ\TqSlideshow\Backend\Ajax\AbstractController {

    ###########################################################################
    # Attributs
    ###########################################################################

	/**
	 * The database table
	 *
	 * @var string
	 */
	protected $dbTable	= 'tx_tq_slideshow';

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

}
