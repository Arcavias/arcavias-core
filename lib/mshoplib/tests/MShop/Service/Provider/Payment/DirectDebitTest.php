<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Payment_DirectDebit.
 */
class MShop_Service_Provider_Payment_DirectDebitTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Service_Provider_Payment_DirectDebit
	 * @access protected
	 */
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Payment_DirectDebitTest');
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
		$context = TestHelper::getContext();
		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );

		$serviceItem = $serviceManager->createItem();
		$serviceItem->setCode( 'test' );

		$this->_object = new MShop_Service_Provider_Payment_DirectDebit( $context, $serviceItem );
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
		MShop_Factory::clear();
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( 4, count( $this->_object->getConfigBE() ) );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'payment.url-success' => 'http://returnUrl'
		);

		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['payment.url-success'] );
	}


	public function testGetConfigFE()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		if( ( $order = reset( $orderItems ) ) === false ) {
			throw new Exception( sprintf('No Order found with statuspayment "%1$s" and type "%2$s"', MShop_Order_Item_Abstract::PAY_AUTHORIZED, MShop_Order_Item_Abstract::TYPE_WEB ) );
		}

		$basket = $orderBaseManager->load( $order->getBaseId() );

		$config = $this->_object->getConfigFE( $basket );

		$this->assertArrayHasKey( 'directdebit.accountowner', $config );
		$this->assertArrayHasKey( 'directdebit.accountno', $config );
		$this->assertArrayHasKey( 'directdebit.bankcode', $config );
		$this->assertArrayHasKey( 'directdebit.bankname', $config );
		$this->assertEquals( 'Our Unittest', $config['directdebit.accountowner']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$config = array(
			'directdebit.accountowner' => 'test user',
			'directdebit.accountno' => '123456789',
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->_object->checkConfigFE( $config );

		$expected = array(
			'directdebit.accountowner' => null,
			'directdebit.accountno' => null,
			'directdebit.bankcode' => null,
			'directdebit.bankname' => null,
		);

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$config = array(
			'directdebit.accountowner' => 123,
			'directdebit.accountno' => 0.1,
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->_object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'directdebit.accountowner', $result );
		$this->assertArrayHasKey( 'directdebit.accountno', $result );

		$this->assertFalse( $result['directdebit.accountowner'] === null );
		$this->assertFalse( $result['directdebit.accountno'] === null );
		$this->assertTrue( $result['directdebit.bankcode'] === null );
		$this->assertTrue( $result['directdebit.bankname'] === null );
	}


	public function testProcess()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$order = $manager->createItem();

		$this->_object->process( $order );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_AUTHORIZED, $order->getPaymentStatus() );
	}


	public function testIsImplemented()
	{
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) );
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL ) );
	}
}