<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default price unit item implementation.
 * @package MShop
 * @subpackage Product
 */
class MShop_Price_Item_Unit_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Price_Item_Unit_Interface
{
	private $_values;

	/**
	 * Initializes the unit item object with the given values
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct('price.unit.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the code of the unit item.
	 *
	 * @return string Code of the unit item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the unit item.
	 *
	 * @param string $code New code of the unit item
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the label of the unit item.
	 *
	 * @return string Label of the unit item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label of the unit item.
	 *
	 * @param string $label New label of the unit item
	 */
	public function setLabel( $label )
	{
		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the unit item.
	 *
	 * @return integer Status of the unit item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the unit item.
	 *
	 * @param integer $status New status of the unit item
	 */
	public function setStatus( $status )
	{
		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['price.unit.code'] = $this->getCode();
		$list['price.unit.label'] = $this->getLabel();
		$list['price.unit.status'] = $this->getStatus();

		return $list;
	}

}