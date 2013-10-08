<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Container
 */


/**
 * Factory for manageing containers like Zip or Excel.
 *
 * @package MW
 * @subpackage Container
 */
class MW_Container_Factory
{
	/**
	 * Opens an existing container or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public static function getContainer( $resourcepath, $type, $format, array $options = array() )
	{
		if( ctype_alnum( $type ) === false )
		{
			$classname = is_string( $type ) ? 'MW_Container_' . $type : '<not a string>';
			throw new MW_Container_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MW_Container_Interface';
		$classname = 'MW_Container_' . $type;

		if( class_exists( $classname ) === false ) {
			throw new MW_Container_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object =  new $classname( $resourcepath, $format, $options );

		if( !( $object instanceof $iface ) ) {
			throw new MW_Container_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}
}