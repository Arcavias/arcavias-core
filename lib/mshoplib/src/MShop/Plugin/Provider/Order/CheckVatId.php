<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks addresses are available in a basket as configured.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_CheckVatId
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Interface
{


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{	
		$p->addListener( $this, 'check.after' );
		$p->addListener( $this, 'addProduct.before' );
		$p->addListener( $this, 'setAddress.before' );
		$p->addListener( $this, 'deleteAddress.after' );
		$p->addListener( $this, 'setService.before' );
		$p->addListener( $this, 'addCoupon.before' );
		$p->addListener( $this, 'deleteCoupon.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$addresses = $order->getAddresses();
		$orderProducts = $order->getProducts();
		
		foreach( $orderProducts as $pos => $orderProduct )
		{
			$orderPosPrice = $orderProduct->getPrice();
			$tax = $orderPosPrice->getTaxrate();
		}
					
		foreach( $addresses as $pos => $item )
		{
			$vatids = $item->getVatID();
		}
		
		
		if(!isset($vatid)){
			$orderPosPrice->setTaxrate('0.00');	
		} else {
			$orderPosPrice->setTaxrate($tax);	
		}		
		
	}
}