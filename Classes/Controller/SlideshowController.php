<?php
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


// checks if t3jquery is loaded
if (t3lib_extMgm::isLoaded('t3jquery')) {
	require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
	// if t3jquery is loaded and the custom Library had been created
	if (T3JQUERY === true) {
		tx_t3jquery::addJqJS();
	}
}


class Tx_TqSlideshow_Controller_SlideshowController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Id of the content element
	 *
	 * @var	string
	 */
	protected $_contentId	= null;

	/**
	 * The Image set
	 *
	 * @var array
	 */
	protected $_imageList	= array();

	/**
	 * Upload Folder of the Images of the Extension
	 *
	 * @var string
	 */
	protected $_uploadFolder	= 'fileadmin/user_upload/tq_slideshow/';

	/**
	 * stdEffect if no is defined
	 *
	 * @var	string
	 */
	protected $_stdEffect	= array('fade');


	/**
	 * The index of the first image to show
	 *
	 * @var type
	 */
	protected $_startItem	= 0;

	/**
	 * The default change Time for the slideshow
	 *
	 * @var integer
	 */
	protected $_changeTime	= 0;


	/**
	 * The default change Time for the slideshow
	 *
	 * @var integer
	 */
	protected $_width	= 600;

	/**
	 * The default change Time for the slideshow
	 *
	 * @var integer
	 */
	protected $_height	= 200;


	/**
	 * The default mode  for the slideshow
	 *
	 * @var string
	 */
	protected $_mode	= 'normal';

	/**
	 * Specifies wether the carousel appears in horizontal or vertical orientation.
	 * Changes the carousel from a left/right style to a up/down style carousel.
	 *
	 * @var boolean
	 */
	protected $_showToolbar	= false;


	/**
	 * Specifies wether the carousel appears in horizontal or vertical orientation.
	 * Changes the carousel from a left/right style to a up/down style carousel.
	 *
	 * @var boolean
	 */
	protected $_thumbnailDirection	= false;



	/**
	 * Specifies if thumbnails should be shown
	 *
	 * @var boolean
	 */
	protected $_showThumbnails		= false;


	/**
	 * Specifies the height of one thumbnail image
	 *
	 * @var boolean
	 */
	protected $_thumbnailHeight		= false;


	/**
	 * Specifies the width of one thumbnail image
	 *
	 * @var boolean
	 */
	protected $_thumbnailWidth		= false;

	/**
	 * Specifies the name of the $.fn.TqSlideshow.transitiontn[options.transitiontn] wich will called in javascript
	 * for the thumbnail
	 *
	 * @var string
	 */
	protected $_transitiontn		= 'fade';


	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
	}

	/**
	 * List action for this historie
	 *
	 */
	public function listAction() {

		$this->view->assign('imageList', array());
	}

	/**
	 * Show Slideshow
	 */
	public function showAction() {
		global $TSFE;

		$this->_cObjData		= $this->request->getContentObjectData();
		// init and set slideshow id
		if( !empty($this->_cObjData['uid']) ) {

			$this->_contentId	 = 'c'.$this->_cObjData['uid'];
		} else {
			// this is the page slideshow
			$this->_contentId	 = 'p'.$TSFE->page['uid'];
		}

		$extConfig				= tx_tqslideshow_conf::getExtConf();
		$this->_lightBoxCls		= $extConfig['lightboxCls'];

		$this->_setSource();
		$this->_fetchImages();
		$this->_dataProcessing();
		$this->_setSlideShowOptions();

		$this->_template();

		$this->view->assign('lightboxCls',		$this->_lightBoxCls);
		$this->view->assign('contentId',		$this->_contentId);
		$this->view->assign('imageList',		$this->_imageList);
		$this->view->assign('slideShowData',	$slideShowData);
	}


	/**
	 * Get extension configuration (by name)
	 *
	 * @param	string	$name			Configuration settings name
	 * @param	mixed	$defaultValue	Default value (if configuration doesn't exists)
	 * @return	mixed
	 */
	protected function getExtConf($name, $defaultValue = NULL) {
		$ret = $defaultValue;

		if(!empty($this->extConf[$name])) {
			$ret = $this->extConf[$name];
		}
		return $ret;
	}

	/**
	 * Set Templates
	 */
	protected function _template(){
		if(!empty($this->settings['templateFile'])){
			$this->view->setTemplatePathAndFilename($this->settings['templateFile']);
		}

		if(empty($this->_imageList)){
			$template = t3lib_extMgm::extPath('tq_slideshow') . 'Resources/Private/Templates/Slideshow/Empty-image.html';
			$this->view->setTemplatePathAndFilename($template);
		}
	}

	/**
	 * Set the required javascript files to the header
	 * @global type $TSFE
	 */
	protected function _setSource(){
		global $TSFE;

		$pageTS			= tx_tqslideshow_conf::getSetupTs();
		$engine			= $this->settings['engine'];
		$jsFileList		= $pageTS['engines.'][$engine]['js.'];
		$cssFileList	= $pageTS['engines.'][$engine]['css.'];



		if( empty($TSFE->additionalHeaderData[$this->extensionName]) ) {
			$TSFE->additionalHeaderData[$this->extensionName] = '';
		}

		// include css files
		foreach($cssFileList as $cssFile) {
			$TSFE->additionalHeaderData[$this->extensionName] .= '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($cssFile).'">'."\n";
		}

		// include js files
		foreach($jsFileList as $jsFile) {
			$TSFE->additionalHeaderData[$this->extensionName] .= '<script type="text/javascript" src="'.htmlspecialchars($jsFile).'"></script>'."\n";
		}

		if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['javascript'])) {
			foreach  ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['javascript'] as $classRef) {
				$hookObj= &t3lib_div::getUserObj($classRef);
				if (method_exists($hookObj, 'javascript')) {
					$hookObj->javascript($image,$conf,$this->extensionName,$js);
				}
			}
		}
	}


	/**
	 * get Images for Content element
	 */
	protected function _fetchImages() {

		$imageList	= $this->settings['imageList'];
		if(!empty($imageList)){
			foreach($imageList as $image) {
				$this->_imageList[]	= $image['row'];
			}
		}
		return $this->_imageList;
	}

	protected function _generateLinks($image,$cachedImage,$number){
		global $TSFE;

		$altTag			= $image['imageAltText'];
		$cachedImg		= $image['cachedImage'];
		$descriptionImg	= $image['description'];

		$link	= null;
		$number++;
		// Call all lightbox hook
		if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['lightboxLink'])) {
			foreach  ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['lightboxLink'] as $classRef) {
				$hookObj= &t3lib_div::getUserObj($classRef);
				if (method_exists($hookObj, 'lightboxLink')) {
					$hookObj->lightboxLink($image,$conf,$this->extensionName,$js,$this->_contentId,$number);
				}
			}
		}

		$link = $TSFE->cObj->typolink('
			<img class="slideshow-'.$this->_contentId.' slideshow-image"
				  src="'.$cachedImg.'"
				  alt="'.$altTag.'">',
			$conf
		);

		if( $descriptionImg ) {
			$link	= '<a class="tq-rzColobor-inline" href="#inline-'.$this->_contentId.'-number-'.$number.'">';
			$link  .= '<img class="slideshow-'.$this->_contentId.' slideshow-image" src="'.$cachedImg.'"alt="'.$altTag.'">';
			$link  .= '</a>';
		}

		if( $descriptionImg && $image['is_lightbox'] ) {
			$this->_content .= '<div id="inline-'.$this->_contentId.'-number-'.$number.'">'.$descriptionImg.'</div>';
		}

		$this->view->assign('content',	$this->_content);

		return $link;


	}

	/**
	 * Collect the javascript options
	 */
	protected function _setSlideShowOptions() {
		$changeTime			= $this->settings['changeTime'];
		$imageWidth			= $this->settings['imageWidth'];
		$imageHeight		= $this->settings['imageHeight'];
		$showToolbar		= $this->settings['showToolbar'];
		$stdEffect			= $this->settings['effect'];
		$showThumbnails		= $this->settings['showThumbnails'];
		$thumbnailWidth		= $this->settings['thumbnailWidth'];
		$thumbnailHeight	= $this->settings['thumbnailHeight'];
		$thumbnailDirection	= $this->settings['thumbnailDirection'];
		$transitiontn		= $this->settings['transitiontn'];
		$carouselTb			= $this->settings['carousel'];
		$mode				= $this->settings['mode'];
		$thumbNailsToShow	= $this->settings['thumbnailToShow'];
		$containerWidth		= $this->settings['containerWidth'];
		$containerHeight	= $this->settings['containerHeight'];

		$slideshowOptions	= array();

		$slideshowOptions['changeTime']						= ($changeTime >= 500 )		? $changeTime : $this->_changeTime;
		$slideshowOptions['showThumbnails']					= ($showThumbnails )		? $showThumbnails : $this->_showThumbnails;
		$slideshowOptions['numberOfThumbnailsToDisplay']	= ($thumbNailsToShow )		? $thumbNailsToShow : false ;


		$slideshowOptions['showToolbar']			= ($showToolbar )			? $showToolbar : $this->_showToolbar;
		$slideshowOptions['thumbnailDirection']		= ($thumbnailDirection )	? $thumbnailDirection : $this->_thumbnailDirection;
		$slideshowOptions['transitiontn']			= ($transitiontn )			? $transitiontn : $this->_transitiontn;
		$slideshowOptions['mode']					= ($mode )					? $mode : $this->_mode;
		$slideshowOptions['containerWidth']			= $containerWidth			? $containerWidth: $imageWidth;
		$slideshowOptions['containerHeight']		= $containerHeight			? $containerHeight: $imageHeight;

		$slideshowOptions['stdEffect']			= $this->_stdEffect;
		$slideshowOptions['id']					= $this->_contentId;
		$slideshowOptions['startItem']			= $this->_startItem;

		$slideshowOptions['slideCount']			= count($this->_imageList);
		$slideshowOptions['imageCount']			= count($this->_imageList);
		$slideshowOptions['carousel']			= ($carouselTb ) ? $carouselTb : false;

		$effectList	= array();

		foreach( $this->_imageList as $image ) {
			$effectList[]	= $image['effect'];
		}
		$slideshowOptions['effectList']	= $effectList;
		$this->view->assign('slideshowOption',json_encode($slideshowOptions));
	}


	/**
	 * Set image Properties from PageSetup
	 */
	protected function _dataProcessing() {

		if(!empty( $this->_imageList ) ) {
			$imageWidth			= $this->settings['imageWidth'];
			$imageHeight		= $this->settings['imageHeight'];
			$thumbnailWidth		= $this->settings['thumbnailWidth'];
			$thumbnailHeight	= $this->settings['thumbnailHeight'];

			if( empty( $thumbnailWidth ) ){
				$thumbnailWidth= 150;
			}

			if( empty($thumbnailHeight ) ) {
				$thumbnailHeight= 50;
			}

			foreach( $this->_imageList as $key	=> &$image ) {
				$thumbnail	= $image['image'];
				if(!empty($image['thumbnail_alt'])){
					$thumbnail	= $image['thumbnail_alt'];
				}
				$image['cachedImage']			= tx_tqslideshow_conf::image($this->_uploadFolder.$image['image'],$imageWidth,$imageHeight);
				$image['thumbnail']				= tx_tqslideshow_conf::image($this->_uploadFolder.$thumbnail,$thumbnailWidth,$thumbnailHeight);
				$image['link']					= tx_tqslideshow_conf::isValidURL($image['link']);
				$image['renderdLink']			= $this->_generateLinks($image,$image['cachedImage'],$key);
			}
		}
	}

}


?>