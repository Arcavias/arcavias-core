<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


/**
 * Test class for MShop_Service_Provider_Payment_PrePay.
 */
class MShop_Service_Provider_Payment_PrePayTest extends MW_Unittest_Testcase
{

	/**
	 * @var    MShop_Service_Provider_Payment_PrePay
	 * @access protected
	 */
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Payment_PrePayTest');
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

		$this->_object = new MShop_Service_Provider_Payment_PrePay( $context, $serviceItem );
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


	public function testGetConfigBE()
	{
		$this->assertEquals( array(), $this->_object->getConfigBE() );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( array(), $this->_object->checkConfigBE( array('url' => 'testurl' ) ) );
	}


	public function testProcess()
	{
		// Currently does nothing.
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );

		$this->_object->process( $manager->createItem() );
	}


	public function testIsImplemented()
	{
		$this->assertTrue( $this->_object->isImplemented(MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL ) );
		$this->assertFalse( $this->_object->isImplemented(MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) );
	}


	public function testCancel()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderItem = $manager->createItem();
		$this->_object->cancel( $orderItem );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_CANCELED, $orderItem->getPaymentStatus() );
	}
}