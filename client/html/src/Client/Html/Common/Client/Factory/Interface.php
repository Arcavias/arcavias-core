<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Common factory interface for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
interface Client_Html_Common_Client_Factory_Interface
	extends Client_Html_Interface
{
	/**
	 * Initializes the class instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths );
}
