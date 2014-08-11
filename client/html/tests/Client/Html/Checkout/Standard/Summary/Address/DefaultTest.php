<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Summary_Address_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Standard_Summary_Address_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		Controller_Frontend_Basket_Factory::createController( $this->_context )->clear();
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$view = TestHelper::getView();
		$view->standardBasket = $this->_getBasket();
		$this->_object->setView( $view );

		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->setAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT, $customer->getPaymentAddress() );
		$controller->setAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY, $customer->getPaymentAddress() );

		$view = TestHelper::getView();
		$view->standardBasket = $this->_getBasket();
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="common-summary-address container">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	protected function _getBasket()
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->setAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT, $customer->getPaymentAddress() );
		$controller->setAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY, $customer->getPaymentAddress() );

		return $controller->get();
	}
}
