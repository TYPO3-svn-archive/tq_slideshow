<?php
namespace TQ\TqSlideshow\Backend\Ajax;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Backend\Utility\IconUtility;

use \TQ\TqSlideshow\Utility\DatabaseUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 TEQneers GmbH & Co. KG <info@teqneers.de>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * TYPO3 Backend ajax module base
 *
 * @author		TEQneers GmbH & Co. KG <info@teqneers.de>
 * @package		TYPO3
 * @subpackage	wtv_base
 */
abstract class AbstractController {

	###########################################################################
	# Attributes
	###########################################################################

    /**
     * The database table
     *
     * @var string
     */
    protected $dbTable	= null;

    /**
     * Db search field list
     *
     * @var array
     */
    protected $dbSearchFieldList = array(  'main.title' => 'text' );

    /**
     * Standard condition
     *
     * @var string
     */
    protected $stdCondition = 'main.deleted = 0';

	/**
	 * POST vars (transformed from json)
	 *
	 * @var array
	 */
	protected $postVar	= array();

	/**
	 * Sorting field
	 */
	protected $sortField	= null;

	/**
	 * start for paging
	 *
	 * @var integer
	 */
	protected $limitStart	= 0;

    /**
     * limit
     *
     * @var integer
     */
    protected $limit	= 0;

	/**
	 * Sorting dir
	 *
	 * @var string
	 */
	protected $sortDir	= null;

	###########################################################################
	# Methods
	###########################################################################

	/**
	 * Execute ajax call
	 */
	public function main() {
		$ret = null;

		// Try to find method
		$function = '';
		if( !empty($_GET['cmd']) ) {
			// GET-param
			$function = (string)$_GET['cmd'];

			// security
			$function = strtolower( trim($function) );
			$function = preg_replace('[^a-z]', '' , $function);
		}

		// Call function
		if( !empty($function) ) {
			$method = '_execute'.$function;
			$call	= array($this, $method);

			if(	is_callable($call) ) {
				$this->_fetchParams();
    			$this->_init();
				$ret = $this->$method();
			}
		}

		// Output json data
		header('Content-type: application/json');
		echo json_encode($ret);
		exit;
	}


    /**
     * Get list of elements (with total count of all elements)
     *
     * @return	array
     */
    protected function _executeGetList() {
        global $TYPO3_DB;

        ###############################
        # Sort
        ###############################
        // default sort
        $sort = 'crdate ASC';

        if( !empty($this->sortField) && !empty($this->sortDir) ) {
            // already filered
            $sort = 'main.'.$this->sortField.' '.$this->sortDir;
        }

        // PAGING
        $limit	= 'LIMIT '.(int)$this->limitStart. ','.(int)$this->limitCount;

        ###############################
        # Criteria
        ###############################
        $condition = array(
            'default' => $this->stdCondition,
        );

        // Fulltext
        if( !empty($this->postVar['criteriaFulltext']) ) {
            $subCondition = array();

            // Add uid
            $subCondition[] = 'main.uid = '.(int)$this->postVar['criteriaFulltext'];

            $criteriaFulltextList = explode(' ', $this->postVar['criteriaFulltext']);

            // Add fields from search field list
            foreach($this->dbSearchFieldList as $field => $fieldType) {

                switch($fieldType) {
                    case 'id':
                        $subCondition[] = $field.' = '.(int)$this->postVar['criteriaFulltext'];
                        break;

                    case 'text':
                        $tmp = array();
                        foreach($criteriaFulltextList as $value) {
                            //$subCondition[] = $field.' LIKE '.DatabaseUtility::quote('%'.$value.'%');
                            $tmp[] = $field.' LIKE '.DatabaseUtility::quote('%'.$value.'%');
                        }

                        if( !empty($tmp) ) {
                            $subCondition[] = implode('  AND  ',$tmp);
                        }

                        break;
                }
            }

            $condition[] = implode(' OR ', $subCondition );
        }

        $condition = $this->_processCondition($condition);

        ###############################
        # Fetch total
        ###############################
        $totalProperty = $this->_fetchCount($condition);

        ###############################
        # Fetch rows
        ###############################
        $data = $this->_fetchList($condition, $sort, $limit);

        $ret = array(
            'results'   => $totalProperty,
            'rows'      => $data,
        );



        $this->_processGetListResult($ret);

        return $ret;
    }

    /**
     * Process condition
     * @param   array   $condition
     * @return  array
     */
    protected function _processCondition($condition) {
        return $condition;
    }

    /**
     * Fetch count
     *
     * @param   array   $condition  Condition
     * @return  array
     */
    protected function _fetchCount($condition) {
        $query	= 'SELECT COUNT(main.uid) as total
					 FROM '.$this->dbTable.' main
					 WHERE 1=1 '.DatabaseUtility::addCondition($condition).'
                 ORDER BY NULL';
        $ret	= DatabaseUtility::getOne($query);

        return $ret;
    }

    /**
     * Fetch list
     *
     * @param   array   $condition  Condition
     * @param   string  $sort       Sorting
     * @param   string  $limit      Limit
     * @return  array
     */
    protected function _fetchList($condition, $sort ,$limit) {
        $query	= 'SELECT main.*
					 FROM '.$this->dbTable.' main
					 WHERE 1=1 '.DatabaseUtility::addCondition($condition).'
				 ORDER BY '.$sort.' '.$limit;
        $ret	= DatabaseUtility::getAll($query);
        return $ret;
    }

    /**
     * Hide element
     *
     * @return	array
     */
    protected function _executeHide() {
        global $TYPO3_DB, $BE_USER;

        $id = (int)$this->postVar['uid'];

        if( !empty($id) ) {
            $query	= 'UPDATE '.$this->dbTable.' main
                          SET main.hidden = 1
                        WHERE main.uid = '.(int)$id.'
                              '.DatabaseUtility::addCondition($this->stdCondition);
            DatabaseUtility::exec($query);

            // Log Action
            $message = '';

            // Create context
            $context = array(
                'log_action' => 'hide',
                'user_id'    => $BE_USER->user['uid'],
                'table_name' => $this->dbTable,
                'table_uid'  => $id,
            );

            $logger = new \TQ\TqSlideshow\Logger\TcaModificationLogger();
            $logger->info($message, $context);

            $this->_triggerUpdate('hide',$id);
        }

        return 1;
    }

    /**
     * Unhide element
     *
     * @return	array
     */
    protected function _executeUnhide() {
        global $TYPO3_DB, $BE_USER;

        $id = (int)$this->postVar['uid'];

        if( !empty($id) ) {
            $query	= 'UPDATE '.$this->dbTable.' main
                          SET main.hidden = 0
                        WHERE main.uid = '.(int)$id.'
                              '.DatabaseUtility::addCondition($this->stdCondition);
            DatabaseUtility::exec($query);

            // Log Action
            $message = '';

            // Create context
            $context = array(
                'log_action' => 'unhide',
                'user_id'    => $BE_USER->user['uid'],
                'table_name' => $this->dbTable,
                'table_uid'  => $id,
            );

            $logger = new \TQ\TqSlideshow\Logger\TcaModificationLogger();
            $logger->info($message, $context);

            $this->_triggerUpdate('unhide',$id);
        }

        return 1;
    }


    /**
     * Hide element
     *
     * @return	array
     */
    protected function _executeLock() {
        global $TYPO3_DB, $BE_USER;

        if( empty($this->postVar['reason']) ) {
            return 0;
        }

        $id = (int)$this->postVar['uid'];

        if( !empty($id) ) {

            $query = 'SELECT * FROM wtv_reason_lock WHERE uid = '.(int)$this->postVar['reason'];
            $reasonRow = DatabaseUtility::getRow($query);

            if( empty($reasonRow) ) {
                // Error
                return 0;
            }


            $query	= 'UPDATE '.$this->dbTable.' main
                          SET main.locked = '.(int)$reasonRow['uid'].'
                        WHERE main.uid = '.(int)$id.'
                              '.DatabaseUtility::addCondition($this->stdCondition);
            DatabaseUtility::exec($query);

            // Log Action
            $message = 'Reason #'.(int)$reasonRow['uid'].': '.$reasonRow['title'];

            // Create context
            $context = array(
                'log_action' => 'lock',
                'user_id'    => $BE_USER->user['uid'],
                'table_name' => $this->dbTable,
                'table_uid'  => $id,
            );

            $logger = new \TQ\TqSlideshow\Logger\TcaModificationLogger();
            $logger->info($message, $context);

            $this->_triggerUpdate('lock',$id);
        }

        return 1;
    }

    /**
     * Unhide element
     *
     * @return	array
     */
    protected function _executeUnlock() {
        global $TYPO3_DB, $BE_USER;

        $id = (int)$this->postVar['uid'];

        if( !empty($id) ) {
            $query	= 'UPDATE '.$this->dbTable.' main
                          SET main.locked = 0
					    WHERE main.uid = '.(int)$id.'
					          '.DatabaseUtility::addCondition($this->stdCondition);
            DatabaseUtility::exec($query);

            // Log Action
            $message = '';

            // Create context
            $context = array(
                'log_action' => 'unlock',
                'user_id'    => $BE_USER->user['uid'],
                'table_name' => $this->dbTable,
                'table_uid'  => $id,
            );

            $logger = new \TQ\TqSlideshow\Logger\TcaModificationLogger();
            $logger->info($message, $context);

            $this->_triggerUpdate('unhide',$id);
        }

        return 1;
    }


    /**
     * Delete element
     *
     * @return	array
     */
    protected function _executeDelete() {
        global $TYPO3_DB, $BE_USER;

        $id = (int)$this->postVar['uid'];

        if( !empty($id) ) {
            $query	= 'UPDATE '.$this->dbTable.' main
                          SET main.deleted = 1
                        WHERE main.uid = '.(int)$id.'
                              '.DatabaseUtility::addCondition($this->stdCondition);
            DatabaseUtility::exec($query);


            // Log Action
            $message = '';

            // Create context
            $context = array(
                'log_action' => 'delete',
                'user_id'    => $BE_USER->user['uid'],
                'table_name' => $this->dbTable,
                'table_uid'  => $id,
            );

            $logger = new \TQ\TqSlideshow\Logger\TcaModificationLogger();
            $logger->info($message, $context);

            $this->_triggerUpdate('delete',$id);
        }

        return 1;
    }

    /**
     * Clear cache
     */
    protected function _executeClearCache() {
        // Clear cache
        $TCE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $TCE->admin = 1;
        $TCE->clear_cacheCmd('pages');

        return 1;
    }

    /**
     * Init
     */
    protected function _init() {
        global $LANG;

        // Include ajax local lang
        $LANG->includeLLFile('EXT:tq_slideshow/locallang_ajax.xml');
    }

    /**
     * Collect and process POST vars and stores them into $this->postVar
     */
    protected function _fetchParams() {
        $rawPostVarList = GeneralUtility::_POST();

        foreach($rawPostVarList as $key => $value) {
            $this->postVar[$key] = json_decode($value);
        }

        // Sorting data
        if( !empty($rawPostVarList['sort']) ) {
            $this->sortField = DatabaseUtility::sanitizeSqlField( (string)$rawPostVarList['sort'] );
        }

        $this->limitStart	= 0;
        if(!empty($this->postVar['start']) ) {
            $this->limitStart	= $this->postVar['start'];
        }

        $this->limitCount	= 12;
        if(!empty($this->postVar['limit']) ) {
            $this->limitCount	= $this->postVar['limit'];
        }

        if( !empty($rawPostVarList['dir']) ) {
            switch( strtoupper($rawPostVarList['dir']) ) {
                case 'ASC':
                    $this->sortDir = 'ASC';
                    break;

                case 'DESC':
                    $this->sortDir = 'DESC';
                    break;


            }
        }
    }

    /**
     * Gets the number of records referencing the record with the UID $uid in
     * the table $tableName.
     *
     * @param string $tableName
     *        table name of the referenced record, must not be empty
     * @param integer $uid
     *        UID of the referenced record, must be > 0
     *
     * @return integer the number of references to record $uid in table
     *                 $tableName, will be >= 0
     */
    protected function getReferenceCount($tableName, $uid) {
        if (!isset($this->referenceCount[$tableName][$uid])) {
            $numberOfReferences = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
                '*',
                'sys_refindex',
                'ref_table = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(
                    $tableName, 'sys_refindex'
                ) .
                    ' AND ref_uid = ' . $uid .
                    ' AND deleted = 0'
            );

            $this->referenceCount[$tableName][$uid] = $numberOfReferences;
        }

        return $this->referenceCount[$tableName][$uid];
    }

    /**
     * Process get list result
     *
     * @param   array   $data
     * @return  array
     */
    protected function _processGetListResult(&$data) {
    }



    /**
     * Trigger update
     *
     * @param   string  $type   Type of update
     * @param   integer $id     Row id
     */
    protected function _triggerUpdate($type, $id) {

    }

}

?>