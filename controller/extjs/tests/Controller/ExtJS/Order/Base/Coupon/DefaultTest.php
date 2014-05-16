<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Order_Base_Coupon_DefaultTest extends MW_Unittest_Testcase
{
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Order_Base_Coupon_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Order_Base_Coupon_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.coupon.code' => 'OPQR' ) ) ) ),
			'sort' => 'order.base.coupon.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'OPQR', $result['items'][0]->{'order.base.coupon.code'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$productManager = $baseManager->getSubManager( 'product' );
		$search = $productManager->createSearch();

		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNE' ) );
		$results = $productManager->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.coupon.baseid' => $expected->getBaseId(),
				'order.base.coupon.productid' => $expected->getId(),
				'order.base.coupon.code' => 'EFGH22'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array(
				0 => array( '==' => (object) array( 'order.base.coupon.productid' => $expected->getId() ) ) ),
				1 => array( '==' => (object) array( 'order.base.coupon.code' => 'EFGH22' ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.coupon.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.coupon.id'} );
		$this->assertEquals( $saved['items']->{'order.base.coupon.id'}, $searched['items'][0]->{'order.base.coupon.id'} );
		$this->assertEquals( $saved['items']->{'order.base.coupon.baseid'}, $searched['items'][0]->{'order.base.coupon.baseid'} );
		$this->assertEquals( $saved['items']->{'order.base.coupon.productid'}, $searched['items'][0]->{'order.base.coupon.productid'} );
		$this->assertEquals( $saved['items']->{'order.base.coupon.code'}, $searched['items'][0]->{'order.base.coupon.code'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
