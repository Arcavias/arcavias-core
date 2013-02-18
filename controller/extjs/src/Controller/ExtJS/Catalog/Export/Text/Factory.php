<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id$
 */


/**
 * ExtJS catalog text export controller factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Catalog_Export_Text_Factory
	extends Controller_ExtJS_Common_Factory_Abstract
	implements Controller_ExtJS_Common_Factory_Interface
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/extjs/catalog/export/text/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_ExtJS_Catalog_Export_Text_' . $name : '<not a string>';
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_ExtJS_Common_Load_Text_Interface';
		$classname = 'Controller_ExtJS_Catalog_Export_Text_' . $name;

		return self::_createController( $context, $classname, $iface );
	}
}
