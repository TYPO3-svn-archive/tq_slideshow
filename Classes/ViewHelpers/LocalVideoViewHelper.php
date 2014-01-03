<?php
namespace TQ\TqSlideshow\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
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

class LocalVideoViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {


    /**
     * The files
     *
     * @var int
     */
    protected $_files	= array();


    /**
     * The files
     *
     * @var int
     */
    protected $_videoList	= array();


    protected $_templateHTMLVideoHeader	= '<div class="section-header"><h4><a name="mediaFiles">Trailer</a></h4></div>
		<div id="jplayer_inspector"></div>';

    protected $_templateHTMLVideo	= '
		<div id="jp_container_###CONTENTID###-video" class="jp-video slideshow-video-container">
			<div class="jp-type-single" style="width:###VIDEO_WIDTH###px height:###VIDEO_HEIGHT###px" >
				<div id="jquery_jplayer_###CONTENTID###-video" class="jp-jplayer"></div>


                    <div class="jp-interface">
						<div class="jp-controls-holder">
							<ul class="jp-controls">
								<li><a href="javascript:;" class="jp-play active" tabindex="1">play</a></li>
								<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
								<!--
								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
								-->
							</ul>
							<!--
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						    -->
						</div>
					</div>


				<div class="jp-gui" style="width:###VIDEO_WIDTH###px">
                    <ul class="jp-toggles">
                        <li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
                        <li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
                    </ul>

                    <div class="jp-progress">
                        <div class="jp-seek-bar">
                            <div class="jp-play-bar"></div>
                        </div>
					</div>

					<div class="jp-current-time"></div>
					<div class="jp-duration"></div>


				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>';


    protected $_templateJSVideo	= '
	$("#jquery_jplayer_###CONTENTID###-video").jPlayer({
		ready: function () {
			$(this).jPlayer("setMedia", {
				###SOURCE###
			});
		},
		swfPath: "typo3conf/ext/tq_slideshow/Resources/Public/flash/",
		cssSelectorAncestor: "#jp_container_###CONTENTID###-video"
		###ADDITIONAL_PARAMS###
		,size: {
			width: "###VIDEO_WIDTH###px",
			height: "###VIDEO_HEIGHT###px",
			cssClass: "jp-video-360p"
		},
	});
';



    /**
     * main render function
     *
     * @param array $file           File row
     * @param string $contentId      ContentID
     * @return the content
     */
    public function render($file, $contentId) {
        global $TSFE;


        $this->_files   = $file;
        $this->_collectVideoFiles();
        $markerList = $this->_setMarkers($contentId);

        foreach( $markerList as $key => $value ) {
            $this->_templateHTMLVideo	= str_replace($key, $value,  $this->_templateHTMLVideo);
            $this->_templateJSVideo	    = str_replace($key, $value, $this->_templateJSVideo);
        }

        $TSFE->additionalFooterData[] = '
<script type="text/javascript">
//<![CDATA[
	$(document).ready(function(){
		'.$this->_templateJSVideo.'
	});
//]]>
</script>';

        return $this->_templateHTMLVideo;


    }


    protected function _collectVideoFiles() {
        $typeList = array(
            'media_video_flash',
            'media_video_theora',
            'media_video_h264'
        );

        foreach($typeList as $type) {
            if(!empty($this->_files[$type])) {
                $tmp    = $this->_files[$type];

                foreach($tmp as $v) {
                   switch($v['extension']) {
                       case 'mp4':
                           $ext = 'm4v';
                           break;
                       case 'ogg':
                           $ext = 'ogv';
                           break;
                        default:
                           $ext = $v['extension'];
                   }

                   $this->_videoList[] = array(
                       'mime' => $ext,
                       'url'  =>  $v['url']
                   );
                }
            }
        }


        // set preview image
        if(!empty($this->_files['media_image_preview'])) {
               $this->_videoList[]  = array(
                   'mime' => 'poster',
                   'url'  =>  $this->_files['media_image_preview'][0]['url']
               );
        }
    }


    protected function _setMarkers($cId) {
        $markerArray    = array();

        $markerArray['###CONTENTID###'] = $cId;

        $sourceArray    = array();
        $supplyArray    = array();
        foreach($this->_videoList as $v ) {

              $sourceArray[]    = $v['mime'].':"'.$v['url'].'"';
              $supplyArray[]    = $v['mime'];
        }

        $markerArray['###SOURCE###']	= implode(',',$sourceArray);

        $markerArray['###ADDITIONAL_PARAMS###']	= '
            ,supplied: "'.  implode(',', $supplyArray).'"
            ';
        $markerArray['###VIDEO_WIDTH###']	= $this->_files['videoWidth'];
        $markerArray['###VIDEO_HEIGHT###']	= $this->_files['videoHeight'];

        return $markerArray;

    }

}