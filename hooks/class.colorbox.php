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

class tx_tq_slideshow_colorbox {
	/**
	 *	Generates the lightbox link for the Image
	 * @global type $TSFE
	 *
	 * @param	type	$image
	 * @param	type	$conf
	 * @param	type	$extName
	 */
	public function lightboxLink(&$image,&$conf,$extName,$js,$contentId,$number){
		global $TSFE;

		if(tx_tqslideshow_conf::checkExtState('rzcolorbox')){
			$atTagParams	= '';

			if($image['is_lightbox']) {
				$ret	= '';
				if($image['connect_lightbox']) {
					$rel	= 'rel=colorbox[slideshowGroup-'.$contentId.']';
				}

				$url			= $image['link']['url'];
				$description	= $image['description'];

				if($url) {
					if(strpos($url,'png')  ||
					strpos($url,'jpg')  ||
					strpos($url,'jpeg') ||
					strpos($url,'gif')){
						$atTagParams	.= 'class="rzcolorbox" '.$rel;
					} else {
						$atTagParams	.= 'class="tq-rzColobor-Content cboxElement" '.$rel;
					}
					$conf = array(
						'parameter'			=> $image['link']['url'],
						'ATagParams'		=> $atTagParams,
					);
				}
			}
		}
	}
}


?>