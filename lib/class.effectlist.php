<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Nico Korthals, TEQneers GmbH & Co. KG <info@teqneers.de>
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


class tx_tqslideshow_effectlist {

	function user_effect( &$params, &$pObj)  {
		$params['items']	= array();
		$extConf			= tx_tqslideshow_beconf::loadTS($params['row']['pid']);
		$effectList			= $extConf['effectList.'];

		foreach($effectList as $key => $value) {
			$params['items'][] = array(0 => $value , 1 => $key);
		}
	}



	function thumbnail_effect(&$params,&$pObj)  {
		$params['items']	= array();
		$extConf			= tx_tqslideshow_beconf::loadTS($params['row']['pid']);
		$effectList			= $extConf['effectListThumbnail.'];

		foreach($effectList as $key => $value) {
			$params['items'][] = array(0 => $value , 1 => $key);
		}
	}


	function user_engine(&$params,&$pObj) {
		$params['items']	= array();
		$extConf			= tx_tqslideshow_beconf::loadTS($params['row']['pid']);
		$engineList			= $extConf['engines.'];


		foreach($engineList as $key => $value) {
			if(!isset($value['name'])){
				$engineName	= 'not defined';
			} else {
				$engineName = $value['name'];
			}
			$params['items'][] = array(0 => $engineName , 1 => $key);
		}
	}

}


?>