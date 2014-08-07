<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Interface for order item base service attribute.
 *
 * @package MShop
 * @subpackage Order
 */
interface MShop_Order_Item_Base_Service_Attribute_Interface
	extends MShop_Common_Item_Interface
{
	/**
	 * Returns the original attribute ID of the ordered service attribute.
	 *
	 * @return string Attribute ID of the ordered service attribute
	 */
	public function getAttributeId();

	/**
	 * Sets the original attribute ID of the ordered service attribute.
	 *
	 * @param string $id Attribute ID of the ordered service attribute
	 * @return void
	 */
	public function setAttributeId( $id );

	/**
	 * Returns the order service id of the order service attribute if available.
	 *
	 * @return integer|null Returns the order service id of the order service attribute if available
	 */
	public function getServiceId();

	/**
	 * Sets the order service id.
	 *
	 * @param integer Order service id for the order service attribute item.
	 * @return void
	 */
	public function setServiceId( $id );

	/**
	 * Returns the type of the service attribute item.
	 *
	 * @return string Type of the service attribute item
	 */
	public function getType();

	/**
	 * Sets the type for the service attribute item.
	 *
	 * @param string $type Type as defined by the service provider
	 * @return void
	 */
	public function setType( $type );

	/**
	 * Returns the name of the service attribute item.
	 *
	 * @return string Name of the service attribute item
	 */
	public function getName();

	/**
	 * Sets a new name for the service attribute item.
	 *
	 * @param string $name Name as defined by the service provider
	 * @return void
	 */
	public function setName( $name );

	/**
	 * Returns the code of the service attribute item.
	 *
	 * @return string code of the service attribute item
	 */
	public function getCode();

	/**
	 * Sets a new code for the service attribute item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return void
	 */
	public function setCode( $code );

	/**
	 * Returns the value of the service attribute item.
	 *
	 * @return string service attribute item value
	 */
	public function getValue();

	/**
	 * Sets a new value for the service attribute item.
	 *
	 * @param string $value service attribute item value
	 * @return void
	 */
	public function setValue( $value );

	/**
	 * Copys all data from a given attribute item.
	 *
	 * @param MShop_Attribute_Item_Interface $item Attribute item to copy from
	 * @return void
	 */
	public function copyFrom( MShop_Attribute_Item_Interface $item );
}
