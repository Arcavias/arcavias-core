<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Abstract.php 14558 2011-12-22 19:19:28Z nsendetzky $
 */


/**
 * Base decorator methods for service provider.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Decorator_Abstract
	extends MShop_Service_Provider_Abstract
	implements MShop_Service_Provider_Decorator_Interface
{
	private $_object;
	private $_context;
	private $_item;


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 * @param MShop_Service_Provider_Interface $provider Service provider or decorator
	 */
	public function __construct(MShop_Context_Item_Interface $context,
		MShop_Service_Item_Interface $serviceItem, MShop_Service_Provider_Interface $provider )
	{
		$this->_object = $provider;
		$this->_context = $context;
		$this->_item = $serviceItem;
	}


	/**
	 * Returns the price when using the provider.
	 * Usually, this is the lowest price that is available in the service item but can also be a calculated based on
	 * the basket content, e.g. 2% of the value as transaction cost.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return MShop_Price_Item_Interface Price item containing the price, shipping, rebate
	 */
	public function calcPrice( MShop_Order_Item_Base_Interface $basket )
	{
		return $this->_object->calcPrice($basket);
	}

	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		return $this->_object->checkConfigBE($attributes);
	}

	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigFE( array $attributes )
	{
		return $this->_object->checkConfigFE($attributes);
	}

	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		return $this->_object->getConfigBE();
	}

	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigFE( MShop_Order_Item_Base_Interface $basket )
	{
		return $this->_object->getConfigFE( $basket );
	}

	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return MShop_Service_Item_Interface Service item
	 */
	public function getServiceItem()
	{
		return $this->_item;
	}

	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, scoring, etc. should be implemented in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		return $this->_object->isAvailable($basket);
	}

	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		return $this->_object->isImplemented( $what );
	}

	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order )
	{
		$this->_object->query( $order );
	}

	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return boolean True if the update was successful, false if async updates are not supported
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateAsync()
	{
		return $this->_object->updateAsync();
	}

	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @return boolean True if the update was successful, false if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional )
	{
		return $this->_object->updateSync($additional);
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the provider object.
	 *
	 * @return MShop_Service_Provider_Interface Service provider object
	 */
	protected function _getProvider()
	{
		return $this->_object;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws MShop_Service_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if ( ( $result = call_user_func_array( array( $this->_object, $name ), $param) ) === false ) {
			throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', $name ) );
		}

		return $result;
	}
}
