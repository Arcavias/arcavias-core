<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage MAdmin
 */


/**
 * Cache proxy for creating cache object on demand.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class MAdmin_Cache_Proxy_Default
	implements MW_Cache_Interface
{
	private $_object;
	private $_context;


	/**
	 * Initializes the cache controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
	}


	/**
	 * Removes all expired cache entries.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
		$this->_getObject()->cleanup();
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function delete( $key )
	{
		$this->_getObject()->delete( $key );
	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @inheritDoc
	 *
	 * @param string[] $keys List of key strings that identify the cache entries
	 * 	that should be removed
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function deleteList( array $keys )
	{
		$this->_getObject()->deleteList( $keys );
	}


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * @inheritDoc
	 *
	 * @param string[] $tags List of tag strings that are associated to one or more
	 * 	cache entries that should be removed
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function deleteByTags( array $tags )
	{
		$this->_getObject()->deleteByTags( $tags );
	}


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function flush()
	{
		$this->_getObject()->flush();
	}


	/**
	 * Returns the cached value for the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 * key is found in the cache, the given default value is returned
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function get( $key, $default = null )
	{
		return $this->_getObject()->get( $key, $default );
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @inheritDoc
	 *
	 * @param string[] $keys List of key strings for the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function getList( array $keys )
	{
		return $this->_getObject()->getList( $keys );
	}


	/**
	 * Returns the cached keys and values associated to the given tags if available.
	 *
	 * @inheritDoc
	 *
	 * @param string[] $tags List of tag strings associated to the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a tag isn't associated to any cache entry, nothing is returned
	 * 	for that tag
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function getListByTags( array $tags )
	{
		return $this->_getObject()->getListByTags( $tags );
	}


	/**
	 * Tests if caching is available.
	 *
	 * @inheritDoc
	 *
	 * @return boolean True if available, false if not
	 * @deprecated
	 */
	public function isAvailable()
	{
		return $this->_getObject()->isAvailable();
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Key string for the given value like product/id/123
	 * @param string $value Value string that should be stored for the given key
	 * @param string[] $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @param string|null $expires Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function set( $key, $value, array $tags = array(), $expires = null )
	{
		$this->_getObject()->set( $key, $value, $tags, $expires );
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @inheritDoc
	 *
	 * @param array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param string[] $tags Associative list of key/tag or key/tags pairs that should be
	 * 	associated to the values identified by their key. The value associated
	 * 	to the key can either be a tag string or an array of tag strings
	 * @param array $expires Associative list of key/datetime pairs.
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function setList( array $pairs, array $tags = array(), array $expires = array() )
	{
		$this->_getObject()->setList( $pairs, $tags, $expires );
	}


	/**
	 * Returns the cache object or creates a new one if it doesn't exist yet.
	 *
	 * @return MW_Cache_Interface Cache object
	 */
	protected function _getObject()
	{
		if( !isset( $this->_object ) ) {
			$this->_object = MAdmin_Cache_Manager_Factory::createManager( $this->_context )->getCache();
		}

		return $this->_object;
	}
}
