<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Default implementation of the helper form item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Helper_Form_Default implements MShop_Common_Item_Helper_Form_Interface
{
	private $_url = '';
	private $_method = '';
	private $_values = array();


	/**
	 * Initializes the object.
	 *
	 * @param string $url Initial url
	 * @param string $method Initial method (e.g. post or get)
	 * @param array $values Initial values, including the form parameters 
	 * ( key/ value pairs )
	 */
	public function __construct( $url = '', $method = '', array $values = array() )
	{
		$this->_url = $url;
		$this->_method = $method;
		$this->_values = $values;
	}


	/**
	 * Returns the url.
	 *
	 * @return string Url
	 */
	public function getUrl()
	{
		return $this->_url;
	}


	/**
	 * Sets the url.
	 *
	 * @param string $url Url
	 */
	public function setUrl( $url )
	{
		$this->_url = (string) $url;
	}


	/**
	 * Returns the method.
	 *
	 * @return string Method
	 */
	public function getMethod()
	{
		return $this->_method;
	}


	/**
	 * Sets the method.
	 *
	 * @param string $method Method
	 */
	public function setMethod( $method )
	{
		$this->_method = (string) $method;
	}


	/**
	 * Returns the value for the given key.
	 *
	 * @param string $key key of value
	 * @return mixed Value for the given key
	 */
	public function getValue( $key )
	{
		if( !isset( $this->_values[ $key ] ) ) {
			return null;
		}

		return $this->_values[ $key ];
	}


	/**
	 * Sets the value for the key.
	 *
	 * @param string $key Key of value
	 * @param string $value Value for the given key
	 */
	public function setValue( $key, $value )
	{
		$this->_values[ $key ] = (string) $value;
	}


	/**
	 * Returns the all key/ value pairs.
	 *
	 * @return array Key/ value pairs
	 */
	public function getValues()
	{
		return $this->_values;
	}
}
