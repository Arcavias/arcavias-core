<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Exception.php 896 2012-07-04 12:25:26Z nsendetzky $
 */


/**
 * Common exception for frontend controller classes.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Exception
	extends Exception
{
	private $_list;


	/**
	 * Initializes the exception.
	 *
	 * @param string $msg The exception message
	 * @param integer $code The exception code
	 * @param Exception $previous The previous exception used for the exception chaining.
	 * @param array $list The associative list of errors and their messages when several errors occured
	 */
	public function __construct( $msg = '', $code = 0, Exception $previous = null, array $list = array() )
	{
		parent::__construct( $msg, $code, $previous );

		$this->_list = $list;
	}


	/**
	 * Returns the list of error messages.
	 *
	 * @return array Associative list of keys and their error messages
	 */
	public function getErrorList()
	{
		return $this->_list;
	}
}
