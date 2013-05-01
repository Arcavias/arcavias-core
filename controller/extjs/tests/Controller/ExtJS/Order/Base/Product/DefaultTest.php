<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


class Controller_ExtJS_Order_Base_Product_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Order_Base_Product_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Order_Base_Product_Default( TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'order.base.product.prodcode' => 'U:MD' ) ),
					1 => array( '==' => (object) array( 'order.base.product.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'order.base.product.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( -5.00, $result['items'][0]->{'order.base.product.price'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );
		$results = $baseManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No base item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.product.id' => null,
				'order.base.product.baseid' => $expected->getId(),
				'order.base.product.suppliercode' => 'unitsupplier',
				'order.base.product.prodcode' => 'EFGH22',
				'order.base.product.name' => 'FoooBar',
				'order.base.product.quantity' => 5,
				'order.base.product.flags' => 0,
				'order.base.product.status' => 1
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '==' => (object) array( 'order.base.product.name' => 'FoooBar' ) ),
					1 => array( '==' => (object) array( 'order.base.product.prodcode' => 'EFGH22' ) )
				),
			),
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.product.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.product.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.id'}, $searched['items'][0]->{'order.base.product.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.baseid'}, $searched['items'][0]->{'order.base.product.baseid'} );
		$this->assertEquals( $saved['items']->{'order.base.product.suppliercode'}, $searched['items'][0]->{'order.base.product.suppliercode'} );
		$this->assertEquals( $saved['items']->{'order.base.product.prodcode'}, $searched['items'][0]->{'order.base.product.prodcode'} );
		$this->assertEquals( $saved['items']->{'order.base.product.name'}, $searched['items'][0]->{'order.base.product.name'} );
		$this->assertEquals( $saved['items']->{'order.base.product.quantity'}, $searched['items'][0]->{'order.base.product.quantity'} );
		$this->assertEquals( $saved['items']->{'order.base.product.flags'}, $searched['items'][0]->{'order.base.product.flags'} );
		$this->assertEquals( $saved['items']->{'order.base.product.status'}, $searched['items'][0]->{'order.base.product.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
