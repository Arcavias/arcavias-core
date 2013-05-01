<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Order_Base_Product_Attribute_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_Order_Base_Product_Attribute_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Order_Base_Product_Attribute_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array(
				0 => (object) array( '==' => (object) array( 'order.base.product.attribute.code' => 'color' ) ),
				1 => (object) array( '==' => (object) array( 'order.base.product.attribute.value' => 'blue' ) ),
				2 => (object) array( '==' => (object) array( 'order.base.product.attribute.editor' => 'core:unittest' ) ),
			) ),
			'sort' => 'order.base.product.attribute.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'color', $result['items'][0]->{'order.base.product.attribute.code'} );
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
				'order.base.product.attribute.ordprodid' => $expected->getId(),
				'order.base.product.attribute.code' => 'color',
				'order.base.product.attribute.value' => 'purple',
				'order.base.product.attribute.name' => 'Lila'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.product.attribute.name' => 'Lila' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.product.attribute.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );
		
		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.product.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.id'}, $searched['items'][0]->{'order.base.product.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.code'}, $searched['items'][0]->{'order.base.product.attribute.code'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.value'}, $searched['items'][0]->{'order.base.product.attribute.value'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.name'}, $searched['items'][0]->{'order.base.product.attribute.name'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

