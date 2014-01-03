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


class Tx_TqSlideshow_Controller_SlideshowController extends ActionController {

	/**
	 * Id of the content element
	 *
	 * @var	string
	 */
	protected $_contentId	= null;

    /**
     * Slideshow data
     *
     * @var array
     */
    protected $_data	= array();

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
     * Initializes the controller before invoking an action method.
     *
     * @return void
     */
    protected function initializeAction() {
        $extKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $this->_getVars = GeneralUtility::_GET($extKey);
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     * @api
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
        ###############################
        # TemplateVar: UID
        ###############################
        $cObj   = $this->configurationManager->getContentObject();
        $uid    = $cObj->data['uid'];
        $view->assign('UID', $uid);
    }

	/**
	 * List action for this historie
	 */
	public function listAction() {
		$this->view->assign('imageList', array());
	}

	/**
	 * Show Slideshow
	 */
	public function showAction() {
		global $TSFE;

        $this->_cObjData  = $this->configurationManager->getContentObject();
		// init and set slideshow id
		if( !empty($this->_cObjData->data['uid']) ) {

			$this->_contentId	 = 'c'.$this->_cObjData->data['uid'];
		} else {
			// this is the page slideshow
			$this->_contentId	 = 'p'.$TSFE->page['uid'];
		}

		$this->_setSource();
        $this->_fetchData();

		$this->_fetchMedia();
		$this->_dataProcessing();

		$this->_setSlideShowOptions();

        $this->view->assign('Data',		        $this->_data);
		$this->view->assign('ContentId',		$this->_contentId);
		$this->view->assign('imageList',		$this->_mediaList);
	}



	/**
	 * Set the required javascript files to the header
	 * @global type $TSFE
	 */
	protected function _setSource(){
		global $TSFE;

        if(!empty($TSFE->additionalHeaderData[$this->extensionName])) {
            return;
        }

        $pageTS			= tx_tqslideshow_conf::getSetupTs();
		$engine			= $this->settings['engine'];

        if(empty($engine)){
            $engine = 'tqSlideshow.';
        }

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
	}

    /**
     * fetch the data
     */
    protected function _fetchData(){}

    /**
     * get Images for Content element
     */
    protected function _fetchMedia() {
        $ret    = array();


        if(!empty($this->_data)){

            switch($this->_data['media_mode']) {
                case 1: // manual list

                    $params = array(
                        'slideshow_id = '.$this->_data['uid'],
                    );
                    $this->_mediaList   = Media::getMediaList($params);
                    break;
                case 2: // Collection
                    $collection = Collection::getCollection('uid = '.$this->_data['collection_id']);

                    if(!empty($collection) ) {
                        $this->_mediaList    = Media::getMediaList('collection_id = '.$this->_data['collection_id']);
                    }
                    break;
            }

            foreach($this->_mediaList as &$data ) {
                $uid    = $data['uid'];
                switch( $data['media_type'] ) {
                    case 1: // image
                    case 'image':
                        $data['media_type'] = 'image';
                        $data['image']  = FalService::findByRelation('tx_tq_slideshow_media', 'image', $uid);
                        break;
                    case 2:
                    case 'video';
                        $data['media_type'] = 'video';
                        $data['videoWidth'] = $this->settings['videoWidth'];
                        $data['videoHeight'] = $this->settings['videoHeight'];

                        switch($data['video_type']) {
                            case 'youtube':
                                // No files, only urls
                                $data['_isTypeYoutube'] = 1;
                                parse_str( parse_url($data['media_video_youtube'], PHP_URL_QUERY), $youtubeQueryData);
                                if( !empty($youtubeQueryData['v']) ) {
                                    $data['media_video_youtube_serial'] = $youtubeQueryData['v'];
                                }

                                $data['media_image_preview']    = FalService::findByRelation('tx_tq_slideshow_media', 'media_image_preview', $uid);
                                break;
                            case 'local':
                                // Local storage

                                $data['media_image_preview']    = FalService::findByRelation('tx_tq_slideshow_media', 'media_image_preview', $uid);

                                $data['_isTypeLocal'] = 1;
                                // Fetch file informations
                                $data['media_video_flash']  = FalService::findByRelation('tx_tq_slideshow_media', 'media_video_flash', $uid);
                                $data['media_video_theora'] = FalService::findByRelation('tx_tq_slideshow_media', 'media_video_theora', $uid);
                                $data['media_video_h264']   = FalService::findByRelation('tx_tq_slideshow_media', 'media_video_h264', $uid);
                                break;
                        }
                        $ret[]  = $data;
                        break;
                }
            }
            unset($data);
        }

        return $ret;
    }


    /**
     * Collect the javascript options
     */
    protected function _setSlideShowOptions() {
        $changeTime			= $this->_data['changeTime'];
        $imageWidth			= $this->_data['imageWidth'];
        $imageHeight		= $this->_data['imageHeight'];
        $showToolbar		= $this->_data['showToolbar'];
        $showThumbnails		= $this->_data['showThumbnails'];

        $thumbnailDirection	= $this->_data['thumbnailDirection'];
        $transitiontn		= $this->_data['transitiontn'];

        $carouselTb			= $this->_data['carousel'];
        $mode				= $this->_data['mode'];
        $thumbNailsToShow	= $this->_data['thumbnailToShow'];
        $containerWidth		= $this->_data['containerWidth'];
        $containerHeight	= $this->_data['containerHeight'];

        $slideshowOptions	= array();

        $slideshowOptions['changeTime']						= ($changeTime >= 500 )		? $changeTime : $this->_changeTime;
        $slideshowOptions['showThumbnails']					= ($showThumbnails )		? $showThumbnails : $this->_showThumbnails;

        $slideshowOptions['numberOfThumbnailsToDisplay']	= $this->settings['numberOfThumbnailsToDisplay'];

        $slideshowOptions['showToolbar']			= ($showToolbar )			? $showToolbar : $this->_showToolbar;
        $slideshowOptions['thumbnailDirection']		= ($thumbnailDirection )	? $thumbnailDirection : $this->_thumbnailDirection;
        $slideshowOptions['transitiontn']			= ($transitiontn )			? $transitiontn : $this->_transitiontn;
        $slideshowOptions['mode']					= ($mode )					? $mode : $this->_mode;
        $slideshowOptions['containerWidth']			= $containerWidth			? $containerWidth: $imageWidth;
        $slideshowOptions['containerHeight']		= $containerHeight			? $containerHeight: $imageHeight;

        $slideshowOptions['stdEffect']			= $this->_stdEffect;
        $slideshowOptions['id']					= $this->_contentId;
        $slideshowOptions['startItem']			= $this->_startItem;

        $slideshowOptions['slideCount']			= count($this->_mediaList);
        $slideshowOptions['imageCount']			= count($this->_mediaList);
        $slideshowOptions['carousel']			= ($carouselTb ) ? $carouselTb : false;

        $effectList	= array();

        foreach( $this->_mediaList as $image ) {
            $effectList[]	= array(
                $image['effect_forward'],
                $image['effect_backward']
            );
        }

        $slideshowOptions['effectList']	= $effectList;
        $this->view->assign('slideshowOption',json_encode($slideshowOptions));
    }

    /**
     * Set image Properties from PageSetup
     */
    protected function _dataProcessing() {
        if(!empty( $this->_mediaList ) ) {

            foreach( $this->_mediaList as $key	=> &$obj ) {
                $thumbnail  = null;


                if(empty($obj['media_type'])) {
                    continue;
                }

                switch( $obj['media_type'] ) {
                    case 'image':
                        $thumbnail	= $obj['image'][0]['url'];
                        if(!empty($obj['thumbnail_alt'])){
                            $thumbnail	= $obj['thumbnail_alt'];
                        }
                        $obj['thumbnail']	= tx_tqslideshow_conf::image($thumbnail);

                        break;
                    case 'video':
                        if(!empty($obj['media_image_preview'])){
                            $thumbnail	        = $obj['media_image_preview'][0]['url'];
                            $obj['thumbnail']	= tx_tqslideshow_conf::image($thumbnail);
                        }
                        break;

                    default:
                        // do a hook fpr other elements
                        if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['mediatype'])) {
                            foreach  ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['mediatype'] as $classRef) {
                                $hookObj= &t3lib_div::getUserObj($classRef);
                                if (method_exists($hookObj, 'mediatype')) {
                                    $hookObj->mediatype($this->extensionName,$obj);
                                }
                            }
                        }
                        break;

                }
            }
        }
    }
}


?>