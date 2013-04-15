<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Factory for order history HTML clients in accounts.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Account_History_Factory
	extends Client_Html_Common_Factory_Abstract
	implements Client_Html_Common_Factory_Interface
{
	/**
	 * Creates a account history client object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $name Client name (default: "Default")
	 * @return Client_Html_Interface Filter part implementing Client_Html_Interface
	 * @throws Client_Html_Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( MShop_Context_Item_Interface $context, array $templatePaths, $name = null )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/client/html/account/history/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'Client_Html_Account_History_' . $name : '<not a string>';
			throw new Client_Html_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Client_Html_Interface';
		$classname = 'Client_Html_Account_History_' . $name;

		return self::_createClient( $context, $classname, $iface, $templatePaths );
	}

}