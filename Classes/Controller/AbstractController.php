<?php
namespace TQ\TqSlideshow\Controller;

use \TQ\TqSlideshow\Utility\JsonUtility;
use \TQ\TqSlideshow\Utility\JsonExpressionUtility;
use \TQ\TqSlideshow\Utility\DatabaseUtility;

use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Mvc\RequestInterface;
use \TYPO3\CMS\Extbase\Mvc\ResponseInterface;

use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Backend\Utility\IconUtility;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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

abstract class AbstractController extends ActionController {

    ###########################################################################
    # Attributs
    ###########################################################################

	/**
	 * Create the New button for Grid
	 */
	protected $_createNewButton	= true;

	/**
	 * The Button for a new Entry
	 */
	protected $_newButton	= null;

	/**
	 * The Button for a new Entry
	 */
	protected $_area	= null;

	/**
	 * The data
	 */
	protected $_data	= null;

	/**
	 * The current Area
	 *
	 * @var string
	 */
	protected $_ajaxController	= null;

	/**
	 * The Language definition of the Module
	 *
	 * @var array
	 */
	protected $languageKeyList	= array();


    /**
     * The Language definition of the Module (from base extension)
     *
     * @var array
     */
    protected $languageBaseKeyList	= array();

    ###########################################################################
    # Methods
    ###########################################################################

    /**
     * Initializes the controller before invoking an action method.
     *
     * @return void
     */
    protected function initializeAction() {
        // TODO: find a better auto-magic method to list all language keys
        $this->languageBaseKeyList += array(
            'label.pager.position',
            'label.pager.result',
            'label.pager.empty',

            'field.criteria.fulltext',
            'label.search.fulltext',
            'label.empty.fulltext',
            'label.search.criteriaEndtime',
            'label.empty.criteriaEndtime',

            'db.uid',
            'db.title',
            'db.status',
            'db.category',
            'db.type',
            'db.endtime',

            'db.type.standard',
            'db.type.charged',

            'status.deleted',
            'status.inactive',
            'status.hidden',
            'status.locked',
            'status.active',

            'button.search',
            'button.create',
            'button.settings',

            'title.module.slideshow',


            'message.confirm.hidden.title',
            'message.confirm.hidden.body',
            'message.confirm.hidden.button.yes',
            'message.confirm.hidden.button.cancel',

            'message.confirm.lock.title',
            'message.confirm.lock.body',
            'message.confirm.lock.button.yes',
            'message.confirm.lock.button.cancel',

            'message.confirm.unlock.title',
            'message.confirm.unlock.body',
            'message.confirm.unlock.button.yes',
            'message.confirm.unlock.button.cancel',

            'message.confirm.delete.title',
            'message.confirm.delete.body',
            'message.confirm.delete.button.yes',
            'message.confirm.delete.button.cancel',

            'label.contact.article',
            'label.contact.client',
            'label.search.category',
            'label.search.type',
            'label.search.status',

            'label.lock.reason',
            'label.contact.locked.reason',
            'label.article.locked.reason',
        );

        foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['extensions'] as &$ext ) {
           $ext['handler'] = JsonUtility::expression($ext['handler']);
           $this->_addOnList[]   = $ext;
        }
        unset($ext);
	}

    // ------------------------------------------------------------------------

    /**
     * Main action
     */
    public function mainAction() {
        global $TSFE;

        // Transform extname from camelcase to lowercase underscored key
        $extKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);

        $action = GeneralUtility::_GET('action');

        // Set default for all extensions (just a hack...)
        if( empty($action) ) {
            $action = 'list';
        }

        switch(strtolower($action)) {
            case 'windowclose':
                return $this->windowCloseAction();
                break;

            case 'list':
                $this->view->setTemplatePathAndFilename( ExtensionManagementUtility::extPath($extKey).'/Resources/Private/Templates/Backend/List.html');
                return $this->listAction();
                break;
            default:
               $actionName =  $action.'Action';
               if( method_exists($this, $actionName) ) {
                   $this->$actionName();
               } else {
                   if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['addOn'])) {
                       foreach  ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tq_slideshow']['addOn'] as $classRef) {
                           $hookObj= \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($classRef);
                           if (method_exists($hookObj, $actionName )) {
                               $hookObj->$actionName($action,$this->extensionName,$this->pageRenderer);
                           }
                       }
                   }
               }
        }
    }

    /**
     * Trigger window close (extjs window)
     */
    public function windowCloseAction() {
        $extKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
        $this->view->setTemplatePathAndFilename( ExtensionManagementUtility::extPath($extKey).'/Resources/Private/Templates/Backend/WindowClose.html');
    }

	/**
     * Simple action to list some stuff
     */
    public function listAction() {

        // Local lang
        $languageList = array();
        foreach($this->languageKeyList as $key) {
            //$jsKey = str_replace('.', '_', $key);
            $languageList[$key] = $this->_translate($key);
        }

        // Base lang
        $languageBaseList = array();
        foreach($this->languageBaseKeyList as $key) {
            //$jsKey = str_replace('.', '_', $key);
            $languageBaseList[$key] = $this->_translateBase($key);
        }


        // IconList
        $iconList = array(
            'button.create'     => IconUtility::getSpriteIcon('actions-document-new', array('title' => $this->_translateBase('hint.create')) ),
            'button.search'     => IconUtility::getSpriteIcon('actions-system-tree-search-open', array('title' => $this->_translateBase('hint.search'))),
            'button.settings'   => IconUtility::getSpriteIcon('actions-document-view', array('title' => $this->_translateBase('hint.settings')) ),
            'toolbar.show'      => IconUtility::getSpriteIcon('actions-document-info', array('title' => $this->_translateBase('hint.show'))),
            'toolbar.edit'      => IconUtility::getSpriteIcon('actions-document-open', array('title' => $this->_translateBase('hint.edit')) ),
            'toolbar.delete'    => IconUtility::getSpriteIcon('actions-edit-delete', array('title' => $this->_translateBase('hint.delete')) ),
            'toolbar.hide'      => IconUtility::getSpriteIcon('actions-edit-hide', array('title' => $this->_translateBase('hint.hide')) ),
            'toolbar.unhide'    => IconUtility::getSpriteIcon('actions-edit-unhide', array('title' => $this->_translateBase('hint.unhide')) ),
            'toolbar.lock'      => IconUtility::getSpriteIcon('status-status-locked', array('title' => $this->_translateBase('hint.lock')) ),
            'toolbar.unlock'    => IconUtility::getSpriteIcon('status-status-readonly', array('title' => $this->_translateBase('hint.unlock')) ),
        );


        // Include Ext JS inline code
        $this->pageRenderer->addJsInlineCode(
            'TQ.Conf',
            'Ext.namespace("TQ.Conf");
             TQ.Conf.Configuration = '.JsonUtility::encode($this->_moduleConf()).';
             TQ.Conf.Actions = '.JsonUtility::encode($this->_moduleActions()).';
             TQ.Conf.Lang = '.JsonUtility::encode($languageList).';
             TQ.Conf.LangBase = '.JsonUtility::encode($languageBaseList).';
             TQ.Conf.Icons = '.JsonUtility::encode($iconList).';
             TQ.Conf.addOn = '.JsonUtility::encode($this->_addOnList).';
        ');
    }

    // ------------------------------------------------------------------------

    /**
     * Build module configuration
     *
     * @return array
     */
    protected function _moduleConf() {
        $conf = array(
            'renderTo'        => 'typo3-inner-docbody',
            'limitStart'      => 0,
            'limitCount'      => 20,
            'ajaxController'  => $this->pageRenderer->backPath. 'ajax.php?ajaxID=tqslideshow::'.$this->_ajaxController,
        );

        return $conf;
    }

    /**
     * Build module javascript actions (must be callbacks)
     *
     * @return array
     */
    protected function _moduleActions() {
        return array();
    }

    // ------------------------------------------------------------------------

	 /**
     * Processes a general request. The result can be returned by altering the given response.
     *
     * @param RequestInterface $request The request object
     * @param ResponseInterface $response The response, modified by this handler
     * @return void
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response) {
		$this->template = GeneralUtility::makeInstance('template');

        $this->pageRenderer = $this->template->getPageRenderer();

        // ExtJS Debug
        //$this->pageRenderer->enableExtJsDebug();

        $extKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);

        $basePathJs = ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Backend/js';

		$this->pageRenderer->addJsFile($basePathJs.'/TQ.js');
        $this->pageRenderer->addJsFile($basePathJs.'/Plugin/FitToParent.js');

        $this->pageRenderer->addJsFile($basePathJs.'/Module/Abstract/Module.js');
        $this->pageRenderer->addJsFile($basePathJs.'/Module/Abstract/Grid.js');

        // Dynamic loading backend module
        switch( strtolower($this->extensionName)  ) {
            case 'tqslideshow':
                $this->pageRenderer->addJsFile($basePathJs.'/Module/Slideshow/Module.js');
                $this->pageRenderer->addJsFile($basePathJs.'/Module/Slideshow/Grid.js');
                break;
        }

		$this->pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Backend/css/base.css');

		parent::processRequest($request, $response);
    }

    // ------------------------------------------------------------------------

    /**
     * Translate key
     *
     * @param   string      $key        Translation key
     * @param   null|array  $arguments  Arguments (vsprintf)
     * @return  NULL|string
     */
    protected function _translate($key, $arguments = null) {
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, $this->extensionName, $arguments);
    }

    /**
     * Translate key (from base)
     *
     * @param   string      $key        Translation key
     * @param   null|array  $arguments  Arguments (vsprintf)
     * @return  NULL|string
     */
    protected function _translateBase($key, $arguments = null) {
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'tq_slideshow', $arguments);
    }

}
