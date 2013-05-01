<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ExampleTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Example.
 */
class MShop_Service_Provider_Decorator_ExampleTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Service_Provider_Decorator_Example
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Decorator_ExampleTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	protected function setUp()
	{
		$context = TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $context );
		$search = $servManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Default'));
		$result = $servManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$item->setConfig( array( 'project' => '8502_TEST' ) );

		$serviceProvider = $servManager->getProvider($item);
		$this->_object = new MShop_Service_Provider_Decorator_Example( $context, $item, $serviceProvider);
	}


	public function testGetConfigBE()
	{
		$this->assertArrayHasKey( 'country', $this->_object->getConfigBE() );
		$this->assertArrayHasKey( 'url', $this->_object->getConfigBE() );
	}


	public function testCheckConfigBE()
	{
		$attributes = array( 'country' => 'DE', 'project' => 'Unit', 'url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 6, count( $result ) );
		$this->assertInternalType( 'null', $result['country'] );
		$this->assertInternalType( 'null', $result['project'] );
		$this->assertInternalType( 'null', $result['username'] );
		$this->assertInternalType( 'null', $result['password'] );
		$this->assertInternalType( 'null', $result['url'] );
		$this->assertInternalType( 'null', $result['ssl'] );


		$attributes = array( 'country' => '', 'project' => 'Unit', 'url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 6, count( $result ) );
		$this->assertInternalType( 'string', $result['country'] );
		$this->assertInternalType( 'null', $result['project'] );
		$this->assertInternalType( 'null', $result['username'] );
		$this->assertInternalType( 'null', $result['password'] );
		$this->assertInternalType( 'null', $result['url'] );
		$this->assertInternalType( 'null', $result['ssl'] );
	}


	public function testCalcPrice()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext())->getSubManager('base');
		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare('==', 'order.base.price', '672.00'));
		$result = $orderBaseManager->searchItems($search);

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$price = $this->_object->calcPrice( $item );

		$this->assertInstanceOf('MShop_Price_Item_Interface', $price);
		$this->assertEquals($price->getValue(), '12.95');

	}


	public function testIsAvailable()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext())->getSubManager('base');
		$localeManager = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext());

		$localeItem = $localeManager->createItem();

		$orderBaseDeItem = $orderBaseManager->createItem();
		$localeItem->setLanguageId('de');
		$orderBaseDeItem->setLocale($localeItem);

		$orderBaseEnItem = $orderBaseManager->createItem();
		$localeItem->setLanguageId('en');
		$orderBaseEnItem->setLocale($localeItem);

		$this->assertFalse($this->_object->isAvailable($orderBaseDeItem));
		$this->assertTrue($this->_object->isAvailable($orderBaseEnItem));
	}

	public function testIsImplemented()
	{
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
	}


	public function testCall()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$criteria = $orderManager->createSearch();
		$expr = array (
			$criteria->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$criteria->compare( '==', 'order.statuspayment', '6' )
		);

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item available for order statuspayment "%1s" and "%2s"', '6', 'web' ) );
		}

		$this->_object->buildXML( $order );
	}
}