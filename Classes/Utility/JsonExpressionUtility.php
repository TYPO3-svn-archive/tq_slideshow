<?php
namespace TQ\TqSlideshow\Utility;

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

class JsonExpressionUtility  {
    /**
     * The javascript expression
     *
     * @var string
     */
    protected $_expression;

    /**
     * Constructor
     *
     * @param	string	$expression		the expression to hold
     */
    public function __construct($expression) {
        $this->_expression	= (string)$expression;
    }

    /**
     * Returns a string representation of this expression
     *
     * @return string					the javascript expression
     */
    public function toString() {

        return $this->_expression;
    }

    /**
     * Cast to string
     *
     * proxies to {@see toString()}
     *
     * @return string					the javascript expression
     */
    public function __toString() {
        return $this->toString();
    }
}