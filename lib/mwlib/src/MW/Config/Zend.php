<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id: Zend.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Configuration setting class using Zend_Config
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Zend
	extends MW_Config_Abstract
	implements MW_Config_Interface
{
	private $_config;
	private $_paths;


	/**
	 * Initialize config object with Zend_Config instance
	 *
	 * @param Zend_Config $config Configuration object
	 * @param array|string $path Filesystem path or list of paths to the configuration files
	 */
	public function __construct( Zend_Config $config, $path = array() )
	{
		$this->_config = $config;
		$this->_paths = (array) $path;
	}


	/**
	 * Clones the objects inside.
	 */
	public function __clone()
	{
		$this->_config = clone $this->_config;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $path, $default = null )
	{
		$path = trim( $path, '/' );

		$result = $default;
		$parts = explode( '/', $path );

		try
		{
			foreach( $this->_paths as $fspath ) {
				$this->_load( $this->_config, $fspath, $parts );
			}

			$result = $this->_get( $this->_config, '', $parts );
		}
		catch( MW_Config_Exception $e ) {
			return $default;
		}

		if ($result instanceof Zend_Config) {
			$result = $result->toArray();
		}

		return $result;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $path, $value )
	{
		$path = trim($path, '/');

		$parts = explode('/', $path);

		$config = $this->_config;
		$max = count($parts) - 1;

		for ($i = 0; $i < $max; $i++)
		{
			$val = $config->get($parts[$i]);

			if ($val instanceof Zend_Config) {
				$config = $val;
			} else {
				$config->{$parts[$i]} = new Zend_Config( array(), true );
				$config = $config->{$parts[$i]};
			}
		}

		$config->{$parts[$max]} = $value;
	}


	/**
	 * Descents into the configuration specified by the given path and returns the value if found.
	 *
	 * @param Zend_Config $config Configuration object which should contain the loaded configuration
	 * @param string $path Path to the configuration directory
	 * @param array $parts List of config name parts to look for
	 * @throws MW_Config_Exception if no value is found
	 */
	protected function _get( Zend_Config $config, $path, array $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null )
		{
			if( isset( $config->$key ) )
			{
				if( $config->$key instanceof Zend_Config ) {
					return $this->_get( $config->$key, $path . DIRECTORY_SEPARATOR . $key, $parts );
				} else if( empty( $parts ) ) {
					return $config->$key;
				}
			}

			throw new MW_Config_Exception( 'Key not found' );
		}
		else
		{
			return $config;
		}
	}


	/**
	 * Loads the configuration files when found.
	 *
	 * @param Zend_Config $config Configuration object which should contain the loaded configuration
	 * @param string $path Path to the configuration directory
	 * @param array $parts List of config name parts to look for
	 */
	protected function _load( Zend_Config $config, $path, array $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null )
		{
			$newPath = $path . DIRECTORY_SEPARATOR . $key;

			if( is_dir( $newPath ) )
			{
				if( !isset( $config->$key ) ) {
					$config->$key = new Zend_Config( array(), true );
				}

				$this->_load( $config->$key, $newPath, $parts );
			}

			if( file_exists( $newPath . '.php' ) )
			{
				if( !isset( $config->$key ) ) {
					$config->$key = new Zend_Config( array(), true );
				}

				$config->$key->merge( new Zend_Config( $this->_include( $newPath . '.php' ), true ) );
			}
		}
	}

}