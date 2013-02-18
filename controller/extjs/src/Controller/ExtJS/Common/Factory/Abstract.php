<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Abstract.php 869 2012-06-28 16:38:36Z fblasel $
 */


/**
 * Common methods for all factories.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Factory_Abstract
{
	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Controller_ExtJS_Common_Interface $controller Controller object
	 * @param string $classprefix Decorator class prefix, e.g. "Controller_ExtJS_Attribute_Decorator_"
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function _addDecorators( MShop_Context_Item_Interface $context,
		Controller_ExtJS_Interface $controller, array $decorators, $classprefix )
	{
		$iface = 'Controller_ExtJS_Common_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string($name) ? $classprefix . ucfirst( strtolower( $name ) ) : '<not a string>';
				throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . ucfirst( strtolower( $name ) );

			if( class_exists( $classname ) === false ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$controller =  new $classname( $context, $controller );

			if( !( $controller instanceof $iface ) ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Controller_ExtJS_Common_Interface $controller Controller object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function _addControllerDecorators( MShop_Context_Item_Interface $context,
		Controller_ExtJS_Interface $controller, $domain )
	{
		if ( !is_string( $domain ) || $domain === '' ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$subdomains = explode('/', $domain);
		$domain = $localClass = $subdomains[0];
		if (count($subdomains) > 1) {
			$localClass = str_replace(' ', '_', ucwords( implode(' ', $subdomains) ) );
		}

		$config = $context->getConfig();

		$decorators = $config->get( 'controller/extjs/common/decorators/default', array() );
		$excludes = $config->get( 'controller/extjs/' . $domain . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[ $key ] );
			}
		}

		$classprefix = 'Controller_ExtJS_Common_Decorator_';
		$controller =  self::_addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_ExtJS_Common_Decorator_';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/global', array() );
		$controller =  self::_addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_ExtJS_'. $localClass . '_Decorator_';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/local', array() );
		$controller =  self::_addDecorators( $context, $controller, $decorators, $classprefix );

		return $controller;
	}


	/**
	 * Creates a controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function _createController( MShop_Context_Item_Interface $context, $classname, $interface )
	{
		if( class_exists( $classname ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$controller =  new $classname( $context );

		if( !( $controller instanceof $interface ) ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
