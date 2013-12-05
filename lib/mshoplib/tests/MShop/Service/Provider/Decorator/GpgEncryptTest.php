<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Decorator_GpgEncrypt.
 */
class MShop_Service_Provider_Decorator_GpgEncryptTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_servItem;
	private $_ordServItem;
	private $_provider;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_servItem = MShop_Service_Manager_Factory::createManager( $this->_context )->createItem();
		$this->_ordServItem = MShop_Factory::createManager( $this->_context, 'order/base/service' )->createItem();

		$this->_provider = new MShop_Service_Provider_Payment_PrePay( $this->_context, $this->_servItem );

		$this->_object = new MShop_Service_Provider_Decorator_GpgEncrypt( $this->_context, $this->_servItem, $this->_provider );
	}


	protected function tearDown()
	{
	}


	public function testSetConfigFe()
	{
		$this->_object->setConfigFE( $this->_ordServItem, array() );
	}


	public function testSetConfigFeNoKeyFile()
	{
		$this->_servItem->setConfig( array( 'gpgencrypt.attributes' => 'test' ) );

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->setConfigFE( $this->_ordServItem, array() );
	}


	public function testSetConfigFeNoKeyId()
	{
		$keyfile = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '_gpgencrypt/pubring.gpg';
		$this->_context->getConfig()->set( 'mshop/service/provider/payment/directdebit/gpg/pubkey-file', $keyfile );

		$this->_servItem->setConfig( array( 'gpgencrypt.attributes' => 'test' ) );

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->setConfigFE( $this->_ordServItem, array() );
	}


	public function testSetConfigFeNoBinary()
	{
		$config = $this->_context->getConfig();

		$keyfile = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '_gpgencrypt/pubring.gpg';
		$config->set( 'mshop/service/provider/payment/directdebit/gpg/pubkey-file', $keyfile );
		$config->set( 'mshop/service/provider/payment/directdebit/gpg/pubkey-id', '3F3591CC' );
		$config->set( 'mshop/service/provider/payment/directdebit/gpg/exec-file', 'gpg-invalid' );

		$this->_servItem->setConfig( array( 'gpgencrypt.attributes' => 'test' ) );

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->setConfigFE( $this->_ordServItem, array() );
	}


	public function testSetConfigFeOK()
	{
		$config = $this->_context->getConfig();

		$keyfile = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '_gpgencrypt/pubring.gpg';
		$config->set( 'mshop/service/provider/payment/directdebit/gpg/pubkey-file', $keyfile );
		$config->set( 'mshop/service/provider/payment/directdebit/gpg/pubkey-id', '3F3591CC' );

		$this->_servItem->setConfig( array( 'gpgencrypt.attributes' => 'test' ) );

		$this->_object->setConfigFE( $this->_ordServItem, array( 'test' => 'secret' ) );

		$attrItem = $this->_ordServItem->getAttributeItem( 'test' );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Attribute_Interface', $attrItem );
		$this->assertEquals( 'XXXret', $attrItem->getValue() );

		$attrItem = $this->_ordServItem->getAttributeItem( 'test/gpg' );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Attribute_Interface', $attrItem );
		$this->assertStringStartsWith( '-----BEGIN PGP MESSAGE-----', $attrItem->getValue() );
	}

}