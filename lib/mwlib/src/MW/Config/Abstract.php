<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id: Abstract.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Common methods for all configuration classes
 *
 * @package MW
 * @subpackage Config
 */
abstract class MW_Config_Abstract implements MW_Config_Interface
{
	private $_includeCache = array();


	/**
	* Includes config files using a simple caching.
	*
	* @param string path and file name of a config file
	* @return array return value of the requested config file
	**/
	protected function _include( $file )
	{
		if( !isset( $this->_includeCache[$file] ) ) {
			$this->_includeCache[$file] = include $file;
		}

		return $this->_includeCache[$file];
	}
}