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

class tx_tqslideshow_conf {

	public function checkExtState($extName) {
		$ret	= false;
		if(t3lib_extMgm::isLoaded($extName)){
			$ret = true;
		}
		return $ret;
	}

	public static function rootPid() {
		static $ret = null;
		if( $ret === null ) {
			global $TSFE;

			$rootLine = $TSFE->rootLine;
			ksort($rootLine);
			$rootPage = reset( $rootLine );

			$ret = $rootPage['uid'];
		}
		return $ret;
	}


	/**
	 * Returns the extension variables defined in ext_conf_template.txt
	 */
	public static function getExtConf($name = null){

		if(!empty($name)){
			return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tq_slideshow'][$name]);
		}
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tq_slideshow']);



    }

	/**
	 * Get setup value
	 *
	 * @param	string	$name	Config name
	 * @return	string
	 */
	public static function getConf($name = null) {
		if( !empty( $name) ) {
			return self::get('plugin.tx_tqslideshow.'.$name);
		}
	}


	/**
	 * Get extension setup
	 *
	 * @param	string	$name	Config name
	 * @return	string
	 */
	public static function getSetupTs() {
		global $TSFE;

		$setup			= $TSFE->tmpl->setup;
		$ret			= $setup['plugin.']['tx_tqslideshow.'];
		return $ret;

	}

	public function setSessionKey($array = array() ) {
		global $TSFE;
		$TSFE->fe_user->setKey('ses','tx_tqslideshow',$array);
		$TSFE->storeSessionData();
	}


	/**
	 * Returns the Session key for extension
	 *
	 * @global	array	$TSFE
	 * @return	array
	 */
	public function getSessionKey(){
		global $TSFE;
		return 	$TSFE->fe_user->getKey('ses','tx_tqslideshow');
	}

	/**
	 * Get SetupTS Value
	 *
	 * @param	string	$name	SetupTS Node
	 * @return	string
	 */
	public static function get($name = null) {
		global $TSFE;

		$ret	= null;

		$setup	= $TSFE->tmpl->setup;

		$parts	= explode('.',$name);
		$token	= reset( array_splice($parts, count($parts)-1, 1) );

		foreach($parts as $part) {
			$setup = $setup[$part.'.'];
		}
		$ret = $setup[$token];

		return $ret;
	}

	/**
	 * Get SetupTS Node
	 *
	 * @param	string	$name	SetupTS Node
	 * @return	array
	 */
	public static function getNode( $name = null ) {
		global $TSFE;

		$ret = null;

		$setup = $TSFE->tmpl->setup;

		$parts	= explode('.',$name);
		$token	= reset( array_splice($parts, count($parts)-1, 1) );

		foreach($parts as $part) {
			$setup = $setup[$part.'.'];
		}

		$ret = $setup[$token.'.'];

		return $ret;
	}


	/**
	 * Render the Image with his proporties
	 *
	 * @global		TSFE Object		$TSFE	Typo3 TSFE
	 * @staticvar	tslib_cObj		$cObj	tslib_cObj
	 *
	 * @param		string			$image	The Image source
	 * @param		integer			$width	The Width of the image
	 * @param		integer			$height	The Height for the Images
	 * @return		string			The new image source
	 */
	public static function image($image, $width = null, $height = null) {
		global $TSFE;
		static $cObj = null;

		if( $cObj === null ) {
			$cObj = t3lib_div::makeInstance('tslib_cObj');
		}
		$rootPid	= tx_tqslideshow_conf::rootPid();

		if(!is_file($image)){
            $extPath    = t3lib_extMgm::siteRelPath('tq_slideshow');
            $image      = $extPath.'Resources/Public/images/dummy.jpg';
		}



        $confType	= $TSFE->tmpl->setup['plugin.']['tx_tqslideshow.']['image'];
		$conf		= $TSFE->tmpl->setup['plugin.']['tx_tqslideshow.']['image.'];


		$cObj->data['image']	= $image;
		$cObj->data['width']	= $width;
		$cObj->data['height']	= $height;

		return $cObj->cObjGetSingle($confType, $conf);
	}


	/**
	 * Returns the Content of an page Content
	 *
	 * @param string/array	$id	Id of the content Object
	 */
	public function fetchContent($id){
		global $TYPO3_DB;
		$cObj	= t3lib_div::makeInstance('tslib_cObj');
		$content = null;
		$header = null;


		if(is_array($id)){
			$id	= implode(',', $id);
		}

		$query	= 'SELECT *
					 FROM tt_content
					WHERE uid = '.(int)$id;

		$res		 = $TYPO3_DB->sql_query($query);
		$contentData = $TYPO3_DB->sql_fetch_assoc($res);
		if(!empty($contentData)){
			$header	=  $contentData['header'];
		}


		$tt_content_conf = array(
			'tables'		=> 'tt_content',
			'source'		=> $id,
			'dontCheckPid'	=> 1,
		);
		$content = $cObj->RECORDS($tt_content_conf);
		return array($header, $content);
	}

	/**
	 * Validate the Link for an Image
	 *
	 * @param	string	$url	The Url
	 * @return	string/false	The Validated URL
	 */
	public static function isValidURL($url) {
		return $url;
	}

}


?>