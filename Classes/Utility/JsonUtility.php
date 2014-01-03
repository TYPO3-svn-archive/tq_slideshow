<?php
namespace TQ\TqSlideshow\Utility;

use \TQ\TqSlideshow\UtilityJsonExpressionUtility;

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

class JsonUtility {
    /**
     * Convenience factory method to create Javascript expressions
     *
     * @param	string				$expression		The javascript expression to create
     * @return	TQJsonExpression
     */
    public static function expression($expression) {
        return new JsonExpressionUtility($expression);
    }


    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * Encodes using ext/json's json_encode().
     *
     * NOTE: Encoding native javascript expressions is possible using TQExtJsonExpression
     * or by implementing TQJsonExpressionInterface.
     *
     * @see		TQJsonExpression
     * @param	mixed		$value			The data to encode
     * @param	boolean		$findExpression	Run the expression finder
     * @return	string						JSON encoded object
     */
    public static function encode($value, $findExpression = true ) {
        // if we have an object that supports the json() method we'll use that
        if ( is_object($value) && method_exists($value, 'json') ) {
            return $value->json();
        }

        // Pre-encoding look for TQJsonExpression objects or strings starting
        // with "function(" and replacing by tmp ids
        $javascriptExpressions	= array();
        if($findExpression) {
            $value	= self::_recursiveJsonExprFinder($value, $javascriptExpressions);
        }

        $json	= json_encode($value);

        //only do post-proccessing to revert back the TQJson if any.
        if (count($javascriptExpressions) > 0) {
            foreach ($javascriptExpressions as $expr) {
                $json	= str_replace('"' . $expr['key'] . '"', $expr['value'], $json);
            }
        }

        return $json;
    }

    /**
     * This can replace json_decode and will return objects as assoc arrays by default
     *
     * @param	string		$json	Json encoded string
     * @return	string				Json decoded mixed
     */
    public static function decode( $json ) {
        return json_decode( $json, true );
    }

    /**
     * Check & Replace TQJsonExpression / TQJsonExpressionInterface for tmp ids in the value
     *
     * Check if the value is a TQJsonExpressionInterface, and if replace its value
     * with a magic key and save the javascript expression in an array.
     *
     * NOTE: this method is recursive.
     *
     * NOTE: This method is used internally by the encode method.
     *
     * @see		encode
     * @param	mixed	$value					A string - object property to be encoded
     * @param	array	&$javascriptExpressions	An array of javascript expressions
     * @param	string	$currentKey				A string to store the current key for id generation
     * @return	mixed
     */
    protected static function _recursiveJsonExprFinder($value, array &$javascriptExpressions, $currentKey = null) {
        // check if we have a string starting with "function("
        // we then assume a Javascript expression
        if (is_string($value) && strpos($value, 'function(') === 0) {
            $value	= new JsonExpressionUtility($value);
        } else if ( is_object($value) && method_exists($value, 'json') ) {
            // if we have an object that supports the json() method we'll use that
            $value	= new JsonExpressionUtility($value->json());
        }
        if ($value instanceof JsonExpressionUtility) {
            $key						= "____" . $currentKey . "_" . (count($javascriptExpressions));
            $javascriptExpressions[]	= array(
                "key"	=> $key,
                "value"	=> $value->toString(),
            );
            $value						= $key;
        } elseif (is_array($value) || $value instanceof Traversable) {
            foreach ($value as $k => $v) {
                $value[$k]	= self::_recursiveJsonExprFinder($value[$k], $javascriptExpressions, $k);
            }
        } elseif (is_object($value)) {
            foreach ($value as $k => $v) {
                $value->$k	= self::_recursiveJsonExprFinder($value->$k, $javascriptExpressions, $k);
            }
        }
        return $value;
    }
}
