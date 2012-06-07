<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010
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

class tx_tq_slideshow_javascript {

	/**
	 * Adds additional Javascript source to the header
	 *
	 * @global	type	$TSFE
	 * @param	type	$image
	 * @param	type	$conf
	 * @param	type	$extName
	 */
	public function javascript(&$image,&$conf,$extName){
		global $TSFE;

		$TSFE->additionalHeaderData[$extName] .= '
			<script type="text/javascript">
				$(document).ready(function() {
					jQuery(".tq-rzColobor-Content").colorbox({iframe:true, innerWidth:425, innerHeight:344});
					jQuery(".tq-rzColobor-inline").colorbox({inline:true, innerWidth:425, innerHeight:344});


				});
				var colorboxOnOpen = function(){};
			</script>';





	}
}


?>