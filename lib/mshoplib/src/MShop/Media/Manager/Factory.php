<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Media
 * @version $Id: Factory.php 14790 2012-01-10 17:48:19Z spopp $
 */


/**
 * Factory for objects from the media domain.
 *
 * @package MShop
 * @subpackage Media
 */
class MShop_Media_Manager_Factory
	extends MShop_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates a media manager DAO object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object
	 * @throws MShop_Media_Exception|MShop_Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/media/manager/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'MShop_Media_Manager_' . $name : '<not a string>';
			throw new MShop_Media_Exception(sprintf('Invalid characters in class name "%1$s"', $classname));
		}

		$iface = 'MShop_Media_Manager_Interface';
		$classname = 'MShop_Media_Manager_' . $name;

		$manager = self::_createManager( $context, $classname, $iface );
		return self::_addManagerDecorators( $context, $manager, 'media' );
	}

}
