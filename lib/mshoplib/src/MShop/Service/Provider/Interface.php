<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id$
 */

/**
 * Common interface for both, delivery and payment providers.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Provider_Interface
{
	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return MShop_Price_Item_Interface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( MShop_Order_Item_Base_Interface $basket );


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes );


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes );


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE();


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE( MShop_Order_Item_Base_Interface $basket );


	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return MShop_Service_Item_Interface Service item
	 */
	public function getServiceItem();


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket );


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what );


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order );


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param MW_Communication_Interface $communication Object of communication
	 */
	public function setCommunication( MW_Communication_Interface $communication );


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return boolean True if the update was successful, false if async updates are not supported
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateAsync();


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @return boolean True if the update was successful, false if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional );
}