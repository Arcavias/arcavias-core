<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


/**
 * Test class for MShop_Service_Provider_Delivery_Default.
 */
class MShop_Service_Provider_Delivery_DefaultTest extends MW_Unittest_Testcase
{

	/**
	 * @var    MShop_Service_Provider_Delivery_Default
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Delivery_DefaultTest');
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
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $serviceManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Default'));
		$result = $serviceManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$item->setConfig( array( 'project' => '8502_TEST' ) );
		$item->setCode( 'test' );

		$this->_object = new MShop_Service_Provider_Delivery_Default(TestHelper::getContext(), $item );
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
		$this->assertArrayHasKey( 'project', $this->_object->getConfigBE());
	}


	public function testGetConfigFE()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$this->assertEquals( array(), $this->_object->getConfigFE( $basket ) );
	}


	public function testCheckConfigBE()
	{
		$attributes = array( 'project' => 'Unit', 'url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'null', $result['project'] );
		$this->assertInternalType( 'null', $result['url'] );


		$attributes = array( 'project' => '', 'url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'string', $result['project'] );
		$this->assertInternalType( 'null', $result['url'] );


		$attributes = array( 'project' => 'Unit', 'url' => null );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'null', $result['project'] );
		$this->assertInternalType( 'string', $result['url'] );
	}


	public function testCheckConfigBEwrongTypes()
	{
		$attributes = array( 'project' => true, 'url' => 'http://unittest.com', 'password' => 1111 );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 5, count( $result ) );
		$this->assertInternalType( 'null', $result['url'] );
		$this->assertInternalType( 'null', $result['ssl'] );
		$this->assertInternalType( 'null', $result['username'] );
		$this->assertInternalType( 'string', $result['password'] );
		$this->assertInternalType( 'string', $result['project'] );
	}


	public function testCheckConfigFE()
	{
		$this->assertEquals(array(), $this->_object->checkConfigFE( array() ));
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

	public function testIsAvaible()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubManager('base');

		$this->assertTrue( $this->_object->isAvailable( $orderBaseManager->createItem() ) );
	}

	public function testIsImplemented()
	{
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Delivery_Abstract::FEAT_QUERY ) );
	}

	public function testProcess()
	{
	}

	public function testBuildXML()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$criteria = $orderManager->createSearch();
		$expr = array (
			$criteria->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$criteria->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::STAT_REFUSED )
		);

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item available for order statuspayment "%1s" and "%2s"', MShop_Order_Item_Abstract::STAT_REFUSED, MShop_Order_Item_Abstract::TYPE_WEB ) );
		}

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBase = $orderBaseManager->getItem( $order->getBaseId() );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>
			<orderlist>
				<orderitem>
					<id><![CDATA['. $order->getId() .']]></id>
					<type><![CDATA[web]]></type>
					<datetime><![CDATA[2008-02-15T12:34:56Z]]></datetime>
					<customerid><![CDATA[' . $orderBase->getCustomerId() . ']]></customerid>
					<projectcode><![CDATA[8502_TEST]]></projectcode>
					<languagecode><![CDATA[DE]]></languagecode>
					<currencycode><![CDATA[EUR]]></currencycode>
					<deliveryitem>
						<code><![CDATA[73]]></code>
						<name><![CDATA[solucia]]></name>
					</deliveryitem>
					<paymentitem>
						<code><![CDATA[OGONE]]></code>
						<name><![CDATA[ogone]]></name>
						<fieldlist>
							<fielditem>
								<name><![CDATA[ACOWNER]]></name>
								<value><![CDATA[test user]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ACSTRING]]></name>
								<value><![CDATA[9876543]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[NAME]]></name>
								<value><![CDATA[CreditCard]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[REFID]]></name>
								<value><![CDATA[12345678]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[TXDATE]]></name>
								<value><![CDATA[2009-08-18]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[X-ACCOUNT]]></name>
								<value><![CDATA[Kraft02]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[X-STATUS]]></name>
								<value><![CDATA[9]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ogone-alias-name]]></name>
								<value><![CDATA[aliasName]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ogone-alias-value]]></name>
								<value><![CDATA[aliasValue]]></value>
							</fielditem>
						</fieldlist>
					</paymentitem>
					<priceitem>
						<price><![CDATA[53.50]]></price>
						<shipping><![CDATA[1.50]]></shipping>
						<discount><![CDATA[0.00]]></discount>
						<total><![CDATA[55.00]]></total>
					</priceitem>
					<productlist>
						<productitem>
							<position><![CDATA[1]]></position>
							<code><![CDATA[CNE]]></code>
							<name><![CDATA[Cafe Noire Expresso]]></name>
							<quantity><![CDATA[9]]></quantity>
							<priceitem>
								<price><![CDATA[4.50]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[4.50]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[2]]></position>
							<code><![CDATA[CNC]]></code>
							<name><![CDATA[Cafe Noire Cappuccino]]></name>
							<quantity><![CDATA[3]]></quantity>
							<priceitem>
								<price><![CDATA[6.00]]></price>
								<shipping><![CDATA[0.50]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[6.50]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[3]]></position>
							<code><![CDATA[U:MD]]></code>
							<name><![CDATA[Unittest: Monetary rebate]]></name>
							<quantity><![CDATA[1]]></quantity>
							<priceitem>
								<price><![CDATA[-5.00]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[-5.00]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[4]]></position>
							<code><![CDATA[ABCD]]></code>
							<name><![CDATA[16 discs]]></name>
							<quantity><![CDATA[1]]></quantity>
							<priceitem>
								<price><![CDATA[0.00]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[0.00]]></total>
							</priceitem>
						</productitem>
					</productlist>
					<addresslist>
						<addressitem>
							<type><![CDATA[delivery]]></type>
							<salutation><![CDATA[mr]]></salutation>
							<title><![CDATA[Dr.]]></title>
							<firstname><![CDATA[Our]]></firstname>
							<lastname><![CDATA[Unittest]]></lastname>
							<company><![CDATA[Metaways]]></company>
							<address1><![CDATA[Pickhuben]]></address1>
							<address2><![CDATA[2-4]]></address2>
							<address3><![CDATA[]]></address3>
							<postalcode><![CDATA[20457]]></postalcode>
							<city><![CDATA[Hamburg]]></city>
							<state><![CDATA[Hamburg]]></state>
							<countrycode><![CDATA[DE]]></countrycode>
							<email><![CDATA[eshop@metaways.de]]></email>
							<phone><![CDATA[055544332211]]></phone>
						</addressitem>
						<addressitem>
							<type><![CDATA[payment]]></type>
							<salutation><![CDATA[mr]]></salutation>
							<title><![CDATA[]]></title>
							<firstname><![CDATA[Our]]></firstname>
							<lastname><![CDATA[Unittest]]></lastname>
							<company><![CDATA[]]></company>
							<address1><![CDATA[Durchschnitt]]></address1>
							<address2><![CDATA[1]]></address2>
							<address3><![CDATA[]]></address3>
							<postalcode><![CDATA[20146]]></postalcode>
							<city><![CDATA[Hamburg]]></city>
							<state><![CDATA[Hamburg]]></state>
							<countrycode><![CDATA[DE]]></countrycode>
							<email><![CDATA[eshop@metaways.de]]></email>
							<phone><![CDATA[055544332211]]></phone>
						</addressitem>
					</addresslist>
					<additional>
						<comment><![CDATA[]]></comment>
						<discount/>
					</additional>
				</orderitem>
			</orderlist>';

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;

		if ( $dom->loadXML( $expected ) !== true ) {
			throw new Exception( 'Loading XML failed' );
		}

		$this->assertEquals( $dom->saveXML(), $this->_object->buildXML( $order ) );
	}
}