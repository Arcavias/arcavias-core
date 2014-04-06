<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Decorator for service providers adding additional costs.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_Costs
extends MShop_Service_Provider_Decorator_Abstract
{
	private $_beConfig = array(
		'costs.percent' => array(
			'code' => 'costs.percent',
			'internalcode'=> 'costs.percent',
			'label'=> 'Costs: Decimal percent value',
			'type'=> 'decimal',
			'internaltype'=> 'decimal',
			'default'=> 0,
			'required'=> true,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes )
	{
		$error = $this->_getProvider()->checkConfigBE( $attributes );
		$error += $this->_checkConfig( $this->_beConfig, $attributes );

		return $error;
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing MW_Common_Critera_Attribute_Interface
	 */
	public function getConfigBE()
	{
		$list = $this->_getProvider()->getConfigBE();

		foreach( $this->_beConfig as $key => $config ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $config );
		}

		return $list;
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
		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['costs.percent'] ) ) {
			throw new MShop_Service_Provider_Exception( sprintf( 'Missing configuration "%1$s"', 'costs.percent' ) );
		}

		$value = $basket->getPrice()->getValue() * $config['costs.percent'] / 100;
		$price = $this->_getProvider()->calcPrice( $basket );
		$price->setCosts( $price->getCosts() + $value );

		return $price;
	}
}