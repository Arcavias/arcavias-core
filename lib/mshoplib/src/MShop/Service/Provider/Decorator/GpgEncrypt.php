<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Decorator for encrypting service attributes using GPG.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Decorator_GpgEncrypt
	extends MShop_Service_Provider_Decorator_Abstract
{
	/**
	 * Sets the payment attributes in the given service.
	 *
	 * @param MShop_Order_Item_Base_Service_Interface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
	{
		$this->_getProvider()->setConfigFE( $orderServiceItem, $attributes );

		$attributeItems = $orderServiceItem->getAttributes();
		$servConfig = $this->getServiceItem()->getConfig();
		$config = $this->_getContext()->getConfig();

		if( !isset( $servConfig['gpgencrypt.attributes'] ) || trim( $servConfig['gpgencrypt.attributes'] ) == '' ) {
			return;
		}

		$keypath = 'mshop/service/provider/payment/directdebit/gpg/pubkey-file';
		$keyname = 'mshop/service/provider/payment/directdebit/gpg/pubkey-id';

		if( ( $pubkeyfile = $config->get( $keypath ) ) == null )
		{
			$msg = sprintf( 'Please configure the location of the GPG public key ring using "%1$s"', $keypath );
			throw new MShop_Service_Exception( $msg );
		}

		if( ( $pubkeyname = $config->get( $keyname ) ) == null )
		{
			$msg = sprintf( 'Please configure the ID of the GPG public key using "%1$s"', $keyname );
			throw new MShop_Service_Exception( $msg );
		}

		$options = array(
			'publicKeyring' => $pubkeyfile,
			'homedir' => $config->get( 'mshop/service/provider/payment/directdebit/gpg/home-dir', '/tmp' ),
			'debug' => $config->get( 'mshop/service/provider/payment/directdebit/gpg/debug', false ),
		);

		if( ( $execfile = $config->get( 'mshop/service/provider/payment/directdebit/gpg/exec-file' ) ) != null ) {
			$options['binary'] = $execfile;
		}

		$manager = MShop_Factory::createManager( $this->_getContext(), 'order/base/service/attribute' );

		try
		{
			$gpg = new Crypt_GPG( $options );
			$gpg->addEncryptKey( $pubkeyname );

			foreach( explode( ',', $servConfig['gpgencrypt.attributes'] ) as $attrname )
			{
				if( ( $attrItem = $orderServiceItem->getAttributeItem( trim( $attrname ) ) ) !== null )
				{
					$ordBaseAttrItem = $manager->createItem();
					$ordBaseAttrItem->setType( $attrItem->getType() . '/gpg' );
					$ordBaseAttrItem->setCode( $attrItem->getCode() . '/gpg' );
					$ordBaseAttrItem->setValue( $gpg->encrypt( $attrItem->getValue() ) );

					$attributeItems[] = $ordBaseAttrItem;

					$value = $attrItem->getValue();
					$attrItem->setValue( str_repeat( 'X', strlen( $value ) - 3 ) . substr( $value, -3 ) );
				}
			}
		}
		catch( PEAR_Exception $e )
		{
			$msg = sprintf( 'Error encrypting service attributes using GPG: %1$s', $e->getMessage() );
			throw new MShop_Service_Exception( $msg );
		}

		$orderServiceItem->setAttributes( $attributeItems );
	}
}