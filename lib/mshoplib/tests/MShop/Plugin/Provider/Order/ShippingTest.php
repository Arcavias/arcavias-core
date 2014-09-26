<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Plugin_Provider_Order_Shipping.
 */
class MShop_Plugin_Provider_Order_ShippingTest extends MW_Unittest_Testcase
{
	private $_order;
	private $_object;
	private $_product;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$plugin = $pluginManager->createItem();
		$plugin->setTypeId( 2 );
		$plugin->setProvider( 'Shipping' );
		$plugin->setConfig( array('threshold' => array ('EUR' => '34.00' ) ) );
		$plugin->setStatus( '1' );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $manager->createSearch();

		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC', 'IJKL' ) ) );

		$pResults = $manager->searchItems( $search, array( 'price' ) );

		if ( count($pResults) !== 3 ) {
			throw new Exception('Wrong number of products');
		}

		$products = array();
		foreach( $pResults as $prod ) {
			$products[ $prod->getCode() ] = $prod;
		}

		if( ( $price = current( $products['IJKL']->getRefItems('price') ) ) === false ) {
			throw new Exception('No price item found');
		}
		$price->setValue(10.00);

		$this->_product = $orderBaseProductManager->createItem();
		$this->_product->copyFrom( $products['CNE'] );
		$this->_product->setPrice( $price );

		$product2 = $orderBaseProductManager->createItem();
		$product2->copyFrom( $products['CNC'] );
		$product2->setPrice( $price );

		$product3 = $orderBaseProductManager->createItem();
		$product3->copyFrom( $products['IJKL'] );
		$product3->setPrice( $price );

		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );

		$serviceSearch = $orderBaseServiceManager->createSearch();
		$exp = array(
			$serviceSearch->compare( '==', 'order.base.service.type', 'delivery' ),
			$serviceSearch->compare( '==', 'order.base.service.costs', '5.00' )
		);
		$serviceSearch->setConditions( $serviceSearch->combine( '&&', $exp ) );
		$results = $orderBaseServiceManager->searchItems( $serviceSearch );

		if ( ($delivery = reset($results)) === false ) {
			throw new Exception('No order base item found');
		}

		$this->_order = $orderBaseManager->createItem();

		$this->_order->setService( $delivery, 'delivery' );
		$this->_order->addProduct( $this->_product );
		$this->_order->addProduct( $product2 );
		$this->_order->addProduct( $product3 );

		$this->_object = new MShop_Plugin_Provider_Order_Shipping( $context, $plugin );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_product );
	}

	/**
	 * @todo Implement testRegister().
	 */
	public function testRegister()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testUpdate().
	 */
	public function testUpdate()
	{
		$this->assertEquals( 5.00, $this->_order->getPrice()->getCosts() );
		$this->_object->update($this->_order, 'addProduct');

		$this->_order->addProduct( $this->_product );
		$this->_object->update($this->_order, 'addProduct');

		$this->assertEquals( 0.00, $this->_order->getPrice()->getCosts());
	}
}
