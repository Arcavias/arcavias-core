<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Abstract.php 1116 2012-08-13 08:17:32Z nsendetzky $
 */


/**
 * Abstract class for all service provider implementations with some default methods.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Abstract
implements MShop_Service_Provider_Interface
{
	private $_context;
	private $_serviceItem;
	private $_communication;


	/**
	 * Initializes the service provider object.
	 *
	 * @param MShop_Context_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem )
	{
		$this->_context = $context;
		$this->_serviceItem = $serviceItem;
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
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_context );
		$prices = $this->_serviceItem->getRefItems( 'price', 'default', 'default' );

		if( count( $prices ) > 0 ) {
			return $priceManager->getLowestPrice( $prices, 1 );
		}

		return $priceManager->createItem();
	}


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigBE( array $attributes )
	{
		return array();
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes )
	{
		return array();
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		return array();
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
		return array();
	}


	/**
	 * Returns the service item which also includes the configuration for the service provider.
	 *
	 * @return MShop_Service_Item_Interface Service item
	 */
	public function getServiceItem()
	{
		return $this->_serviceItem;
	}


	/**
	 * Checks if payment provider can be used based on the basket content.
	 * Checks for country, currency, address, RMS, etc. -> in separate decorators
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object
	 * @return boolean True if payment provider can be used, false if not
	 */
	public function isAvailable( MShop_Order_Item_Base_Interface $basket )
	{
		return true;
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		return false;
	}


	/**
	 * Queries for status updates for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function query( MShop_Order_Item_Interface $order )
	{
		throw new MShop_Service_Exception( sprintf( 'Method "%1$s" for provider not available', 'query' ) );
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
		return false;
	}


	/**
	 * Updates the orders for which status updates were received via direct requests (like HTTP).
	 *
	 * @param mixed $additional Update information whose format depends on the payment provider
	 * @return MShop_Order_Item_Interface|null Order item if update was successful, null if the given parameters are not valid for this provider
	 * @throws MShop_Service_Exception If updating one of the orders failed
	 */
	public function updateSync( $additional )
	{
		return null;
	}


	/**
	 * Sets the communication object for a service provider.
	 *
	 * @param MW_Communication_Interface $communication Object of communication
	 */
	public function setCommunication( MW_Communication_Interface $communication )
	{
		$this->_communication = $communication;
	}


	/**
	 * Returns the communication object for the service provider.
	 *
	 * @param MW_Communication_Interface $communication Object of communication
	 */
	protected function _getCommunication()
	{
		if( !isset( $this->_communication ) ) {
			$this->_communication = new MW_Communication_Curl();
		}

		return $this->_communication;
	}


	/**
	 * Checks required fields and the types of the config array.
	 *
	 * @param array $config Config parameters
	 * @param array $attributes Attributes for the config array
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	protected function _checkConfig( array $config, array $attributes )
	{
		$errors = array();

		foreach ( $config as $key => $def )
		{
			if( $def['required'] === true && ( !isset( $attributes[$key] ) || $attributes[$key] === '' ) )
			{
				$errors[$key] = sprintf( 'Required attribute "%1$s" in provider configuration not available', $key );
				continue;
			}

			if( isset( $attributes[$key] ) )
			{
				switch( $def['type'] )
				{
					case 'boolean':
						if( $attributes[$key] != '0' && $attributes[$key] != '1' ) {
							$errors[$key] = 'Not a true/false value'; continue 2;
						}
						break;
					case 'string':
						if( is_string( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not a string'; continue 2;
						}
						break;
					case 'integer':
						if( ctype_digit( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not an integer number'; continue 2;
						}
						break;
					case 'decimal':
					case 'float':
						if( is_numeric( $attributes[$key] ) === false ) {
							$errors[$key] = 'Not a number'; continue 2;
						}
						break;
					case 'datetime':
						$pattern = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$';
						if( preg_match( $pattern, $attributes[$key] ) !== 1 ) {
							$errors[$key] = 'Invalid date and time'; continue 2;
						}
						break;
					default:
						throw new MShop_Service_Exception( sprintf( 'Invalid characters in attribute for provider configuration. Attribute is not of type "%1$s".', $def['type'] ) );
				}
			}

			$errors[$key] = null;
		}

		return $errors;
	}


	/**
	 * Returns context item.
	 *
	 */
	protected function _getContext()
	{
		return $this->_context;
	}

}
