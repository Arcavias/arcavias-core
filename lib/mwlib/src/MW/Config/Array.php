<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id$
 */


/**
 * Configuration setting class using arrays
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Array
	extends MW_Config_Abstract
	implements MW_Config_Interface
{
	private $_config;
	private $_paths;


	/**
	 * Initialize config object
	 *
	 * @param Array $config Configuration array
	 * @param array|string $path Filesystem path or list of paths to the configuration files
	 */
	public function __construct( $config = array(), $paths = array() )
	{
		$this->_config = $config;
		$this->_paths = (array) $paths;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $name, $default = null )
	{
		$parts = explode( '/', trim( $name, '/' ) );

		if( ( $value = $this->_get( $this->_config, $parts ) ) !== null ) {
			return $value;
		}

		foreach( $this->_paths as $fspath ) {
			$this->_config = $this->_load( $this->_config, $fspath, $parts );
		}

		if( ( $value = $this->_get( $this->_config, $parts ) ) !== null ) {
			return $value;
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $name, $value )
	{
		$parts = explode( '/', trim( $name, '/' ) );
		$this->_config = $this->_set( $this->_config, $parts, $value );
	}


	/**
	 * Returns a configuration value from an array.
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @return mixed Found value or null if no value is available
	 */
	protected function _get( $config,  $parts )
	{
		if( ( $current = array_shift( $parts ) ) !== null && isset( $config[$current] ) )
		{
			if( count( $parts ) > 0 ) {
				return $this->_get( $config[$current], $parts );
			}

			return $config[$current];
		}

		return null;
	}


	/**
	 * Sets a configuration value in the array.
	 *
	 * @param array $config Configuration sub-part
	 * @param array $path Configuration path parts
	 * @param array $value The new value
	 */
	protected function _set( $config, $path, $value )
	{
		if( ( $current = array_shift( $path ) ) !== null )
		{
			if( isset( $config[$current] ) ) {
				$config[$current] = $this->_set( $config[$current], $path, $value );
			} else {
				$config[$current] = $this->_set( array(), $path, $value );
			}

			return $config;
		}

		return $value;
	}


	/**
	 * Loads the configuration files when found.
	 *
	 * @param array $config Configuration array which should contain the loaded configuration
	 * @param string $path Path to the configuration directory
	 * @param array $parts List of config name parts to look for
	 * @return array Merged configuration
	 */
	protected function _load( array $config, $path, array $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null )
		{
			$newPath = $path . DIRECTORY_SEPARATOR . $key;

			if( is_dir( $newPath ) )
			{
				if( !isset( $config[$key] ) ) {
					$config[$key] = array();
				}

				$config[$key] = $this->_load( $config[$key], $newPath, $parts );
			}

			if( file_exists( $newPath . '.php' ) )
			{
				if( !isset( $config[$key] ) ) {
					$config[$key] = array();
				}

				$config[$key] = $this->_merge( $config[$key], $this->_include( $newPath . '.php' ) );
			}
		}

		return $config;
	}


	/**
	 * Merges a multi-dimensional array into another one
	 *
	 * @param array $left Array to be merged into
	 * @param array $right Array to merge in
	 */
	protected function _merge( array $left, array $right )
	{
		foreach( $right as $key => $value )
		{
			if( isset( $left[$key] ) && is_array( $left[$key] ) && is_array( $value ) ) {
				$left[$key] = $this->_merge( $left[$key], $value );
			} else {
				$left[$key] = $value;
			}
		}

		return $left;
	}
}