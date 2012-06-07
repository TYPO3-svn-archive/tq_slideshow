<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktsuteam.de>
*  All rights reserved
*
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
 * Utility to add the YAG Icon to Element Wizzard
 *
 * @package Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_TqSlideshow_Utility_WizzardIcon {

	/**
	 * Processing the wizard items array
	 *
	 * @param	array		$wizardItems: The wizard items
	 * @return	Modified array with wizard items
	 */
	function proc($wizardItems)	{
		global $LANG;

		$llFile = t3lib_extMgm::extPath('tq_slideshow').'Resources/Private/Language/locallang.xml';
		$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);

		$wizardItems['plugins_tx_tqslideshow_pi1'] = array(
			'icon'=>t3lib_extMgm::extRelPath('tq_slideshow').'Resources/Public/Icons/teqneers.png',
			'title'=>Tx_PtExtbase_Div::getLLL('tx_TqSlideshow.title', $LOCAL_LANG),
			'description'=>Tx_PtExtbase_Div::getLLL('tx_TqSlideshow.description', $LOCAL_LANG),
			'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=tqslideshow_pi1'
		);

		return $wizardItems;
	}
}
?>