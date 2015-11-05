<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default price unit item interface.
 * @package MShop
 * @subpackage Price
 */
interface MShop_Price_Item_Unit_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the code of the unit item.
	 *
	 * @return string Code of the unit item
	 */
	public function getCode();

	/**
	 * Sets the code of the unit item.
	 *
	 * @param string $code New Code of the unit item
	 * @return void
	 */
	public function setCode( $code );

	/**
	 * Returns the label of the unit item.
	 *
	 * @return string Label of the unit item
	 */
	public function getLabel();

	/**
	 * Sets the label of the unit item.
	 *
	 * @param string $label New label of the unit item
	 * @return void
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the unit item.
	 *
	 * @return string Status of the unit item
	 */
	public function getStatus();

	/**
	 * Sets the status of the unit item.
	 *
	 * @param integer $status New status of the unit item
	 * @return void
	 */
	public function setStatus( $status );

}