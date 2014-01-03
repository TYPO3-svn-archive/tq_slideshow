<?php
namespace TQ\TqSlideshow\Utility;

class DatabaseUtility {
    ###########################################################################
    # Attributs
    ###########################################################################

    /**
     * SQL num rows
     *
     * @var integer
     */
	static public $numRows = 0;

	###########################################################################
	# Convenience functions
	###########################################################################

	/**
     * Get one
     *
     * @param	string		$query	SQL query
     * @return	mixed
     */
	public static function getOne($query) {
        global $TYPO3_DB;

        $ret = null;

        $res = self::query($query);
        if( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
            $ret = reset($row);
        }

        return $ret;
    }

	/**
     * Get row
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getRow($query) {
        global $TYPO3_DB;

        $ret = null;

        $res = self::query($query);
        if( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
            $ret = $row;
        }

        return $ret;
    }

	/**
     * Get All
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getAll($query) {
        global $TYPO3_DB;

        $ret = array();

        $res = self::query($query);
        while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
            $ret[] = $row;
        }
        self::free($res);

        return $ret;
    }

	/**
     * Get All
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getAllIndexed($query) {
        global $TYPO3_DB;

        $ret = array();

        $res = self::query($query);
        while( $row = $TYPO3_DB->sql_fetch_assoc($res) ) {
            $key = reset($row);

            $ret[$key] = $row;
        }
        self::free($res);

        return $ret;
    }

	/**
     * Get List
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getList($query) {
        global $TYPO3_DB;

        $ret = array();

        $res = self::query($query);
        while( $row = $TYPO3_DB->sql_fetch_row($res) ) {
            $ret[ $row[0] ] = $row[1];
        }

        return $ret;
    }

	/**
     * Get column
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getCol($query) {
        global $TYPO3_DB;

        $ret = array();

        $res = self::query($query);
        while( $row = $TYPO3_DB->sql_fetch_row($res) ) {
            $ret[] = $row[0];
        }

        return $ret;
    }

	/**
     * Get column
     *
     * @param	string		$query	SQL query
     * @return	array
     */
	public static function getColIndexed($query) {
        global $TYPO3_DB;

        $ret = array();

        $res = self::query($query);
        while( $row = $TYPO3_DB->sql_fetch_row($res) ) {
            $ret[ $row[0] ] = $row[0];
        }

        return $ret;
    }

	/**
     * Get count
     *
     * @param	string		$query	SQL query
     * @return	integer
     */
	public static function getCount($query) {
        $query = 'SELECT COUNT(*) FROM ('.$query.') tmp';
        return self::getOne($query);
    }

	/**
     * Exec query (INSERT)
     *
     * @param	string		$query	SQL query
     * @return	integer				Last insert id
     */
	public static function execInsert($query) {
        global $TYPO3_DB;

        $res = self::query($query);
        $ret = $TYPO3_DB->sql_insert_id();
        self::free($res);

        return $ret;
    }

	/**
     * Exec query (DELETE, UPDATE etc)
     *
     * @param	string		$query	SQL query
     * @return	integer					Affected rows
     */
	public static function exec($query) {
        global $TYPO3_DB;

        $res = self::query($query);
        $ret = $TYPO3_DB->sql_affected_rows();
        self::free($res);

        return $ret;
    }

    ###########################################################################
    # Quote functions
    ###########################################################################


    /**
     * Quote value
     *
     * @param   string  $value  Value
     * @return  string
     */
    public static function quote($value) {
        global $TYPO3_DB;

        if( $value === null ) {
            return 'NULL';
        }

        return $TYPO3_DB->fullQuoteStr($value, 'pages');
    }

    /**
     * Quote array with values
     *
     * @param   array  $values  Values
     * @return  array
     */
    public static function quoteArray($values) {
        global $TYPO3_DB;

        $ret = array();
        foreach($values as $k => $v) {
            $ret[$k] = self::quote($v);
        }

        return $ret;
    }

    /**
     * Sanitize field for sql usage
     *
     * @param   string  $field  SQL Field/Attribut
     * @return  string
     */
    public static function sanitizeSqlField($field) {
        return preg_replace('/[^_a-zA-Z0-9\.]/', '', $field);
    }


    /**
     * Sanitize table for sql usage
     *
     * @param   string  $table  SQL Table
     * @return  string
     */
    public static function sanitizeSqlTable($table) {
        return preg_replace('/[^_a-zA-Z0-9]/', '', $table);
    }

	###########################################################################
	# Helper functions
	###########################################################################

	/**
     * Add condition to query
     *
     * @param	array|string	$condition	Condition
     * @return	string
     */
	public static function addCondition($condition) {
        $ret = ' ';

        if( !empty($condition) ) {
            if( is_array($condition) ) {
                $ret .= ' AND (( '. implode(" )\nAND (",$condition) .' ))';
            } else {
                $ret .= ' AND ( '.$condition.' )';
            }
        }

        return $ret;
    }

	/**
     * Create condition WHERE field IN (1,2,3,4)
     *
     * @param	string	$field		SQL field
     * @param	array	$values		Values
     * @param	boolean	$required	Required
     * @return	string
     */
	public static function conditionIn($field, $values, $required = true) {
        global $TYPO3_DB;

        if( !empty($values) ) {
            $quotedValues = $TYPO3_DB->fullQuoteArray($values, 'pages');

            $ret = $field.' IN ('. implode(',', $quotedValues) .')';
        } else {
            if( $required ) {
                $ret = '1=0';
            } else {
                $ret = '1=1';
            }
        }

        return $ret;
    }

	/**
     * Create condition WHERE field NOT IN (1,2,3,4)
     *
     * @param	string	$field		SQL field
     * @param	array	$values		Values
     * @param	boolean	$required	Required
     * @return	string
     */
	public static function conditionNotIn($field, $values, $required = true) {
        global $TYPO3_DB;

        if( !empty($values) ) {
            $quotedValues = $TYPO3_DB->fullQuoteArray($values, 'pages');

            $ret = $field.' NOT IN ('. implode(',', $quotedValues) .')';
        } else {
            if( $required ) {
                $ret = '1=0';
            } else {
                $ret = '1=1';
            }
        }

        return $ret;
    }



    /**
     * Build limit statement
     *
     * @param   array|string $limit         Limit configuration
     * @param   null|string $defaultLimit   Default limit
     * @return  string
     */
    public static function addOrderBy($orderBy, $direction = 'ASC') {
        $ret = ' ';

        if(!empty($orderBy) ) {
            $ret    = 'ORDER BY '.self::sanitizeSqlField($orderBy).' '.self::sanitizeSqlField($direction);
        }

        return $ret;
    }


    /**
     * Build limit statement
     *
     * @param   array|string $limit         Limit configuration
     * @param   null|string $defaultLimit   Default limit
     * @return  string
     */
    public static function addLimit($limit, $defaultLimit = null) {
        $ret = ' ';

        if( !empty($defaultLimit) ) {
            $ret = ' LIMIT '.(int)$defaultLimit;
        }


        if( empty($limit) ) {
            return $ret;
        }

        if( is_scalar($limit) ) {
            return ' LIMIT '.(int)$limit;
        }

        if( is_array($limit) ) {
            $offset		= $limit['offset'];

            if( empty($limit['limit']) ) {
                $limit['limit'] = $defaultLimit;
            }
            $limitItems	= $limit['limit'];


            if( empty($offset) && empty($limit) ) {
                return $ret;
            }

            if( empty($offset) ) {
                return ' LIMIT '.(int)$limitItems;
            }

            return ' LIMIT '.(int)$offset.','.$limitItems;
        }
    }

	/**
     * Add default access condition for TYPO3 tables
     *
     * @param	null|string	$prefix				Prefix for tables
     * @param	bool		$accessCondition	Use access condition (deleted, hidden)
     * @param	bool		$timeCondition		Use time based condition (starttime, endtime)
     * @return	string
     */
	public static function addDefaultAccessCondition($prefix = null, $accessCondition = true, $timeCondition = true) {
        static $time = null;

        $ret = '';

        if( $prefix !== null ) {
            $prefix .= '.';
        }

        if( $time === null ) {
            $time = time();

            // Hour lock
            $time = $time - $time % 3600;
        }

        $condition = array();

        // Access
        if( $accessCondition ) {
            $condition[] = $prefix.'deleted = 0 AND '.$prefix.'hidden = 0';
        }

        if( $timeCondition ) {
            // Time lock (start)
            $condition[] =  $prefix.'starttime IS NULL OR '.$prefix.'starttime = 0 OR '.$prefix.'starttime <= '.(int)$time;

            // Time lock (end)
            $condition[] =  $prefix.'endtime IS NULL OR '.$prefix.'endtime = 0 OR '.$prefix.'endtime > '.(int)$time;
        }

        if( !empty($condition) ) {
            $ret .= ' AND (( '. implode(" )\nAND (",$condition) .' ))';
        }

        return $ret;
    }

	###########################################################################
	# SQL warpper functions
	###########################################################################

	/**
     * Execute sql query
     *
     * @param	string	$query	SQL query
     * @return	resource
     */
	public static function query($query) {
        global $TYPO3_DB;

        $res = $TYPO3_DB->sql_query($query);

        if( !$res || $TYPO3_DB->sql_errno() ) {
            $errorMsg = 'SQL Error: '.$TYPO3_DB->sql_error().' [errno: '.$TYPO3_DB->sql_errno().']';

            if( defined('TYPO3_cliMode') ) {
                throw new \Exception($errorMsg);
            } else {
                debug('SQL-QUERY: '.$query, $errorMsg, __LINE__, __FILE__);
            }
        }

        if( $res ) {
            self::$numRows = $TYPO3_DB->sql_num_rows($res);
        }

        return $res;
    }

	/**
     * Free sql result
     *
     * @param	resource		$res	SQL resource
     */
	public static function free($res) {
        global $TYPO3_DB;
return;
        if( $res ) {
            $TYPO3_DB->sql_free_result($res);
        }
    }
}