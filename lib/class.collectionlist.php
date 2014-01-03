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


class tx_tqslideshow_collectionlist {

	function user_collection( &$params, &$pObj)  {
		$params['items']	= array();

        $query  = 'SELECT uid, title FROM tx_tq_slideshow_collection WHERE hidden=0 AND deleted = 0 ORDER BY crdate';
		$collectionList		= \TQ\TqSlideshow\Utility\DatabaseUtility::getAll($query);

		foreach($collectionList as $v) {
			$params['items'][] = array(0 => $v['title'] , 1 => $v['uid']);
		}
	}

}


?>