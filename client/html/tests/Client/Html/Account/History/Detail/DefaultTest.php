<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Account_History_Detail_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite( 'Client_Html_Account_History_Detail_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = clone TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Account_History_Detail_Default( $this->_context, $paths );
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
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$customer = $this->_getCustomerItem( 'UTC001' );
		$this->_context->setUserId( $customer->getId() );

		$output = $this->_object->getHeader();
	}


	public function testGetBody()
	{
		$customer = $this->_getCustomerItem( 'UTC001' );
		$this->_context->setUserId( $customer->getId() );

		$view = $this->_object->getView();
		$param = array(
			'h-action' => 'detail',
			'h-order-id' => $this->_getOrderItem( $customer->getId() )->getId()
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<div class="account-history-detail">', $output );
		$this->assertRegExp( '#<div class="basket">#', $output );
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


	protected function _getCustomerItem( $code )
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function _getOrderItem( $customerid )
	{
		$manager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'order.base.customerid', $customerid ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
