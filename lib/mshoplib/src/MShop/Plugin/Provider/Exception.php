<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * MShop_Plugin_Provider_Exception
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Exception extends MShop_Plugin_Exception
{

	private $_errorCodes;


	/**
	 * Initializes the instance of the exception
	 *
	 * @param string $message Custom error message to describe the error
	 * @param integer $code Custom error code to identify or classify the error
	 * @param Exception $previous Previously thrown exception
	 * @param array $errorCodes List of error codes for error handling
	 */
	public function __construct( $message = '', $code = 0, Exception $previous = null, $errorCodes = array() )
	{
		parent::__construct( $message, $code, $previous );

		$this->_errorCodes = $errorCodes;
	}


	/**
	 * Gets the error codes of the exception
	 *
	 * @return array list of error codes
	 */
	public function getErrorCodes()
	{
		return $this->_errorCodes;
	}
}
