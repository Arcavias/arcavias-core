<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Order_Base_Service_Attribute_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_Order_Base_Service_Attribute_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Order_Base_Service_Attribute_Default( TestHelper::getContext() );
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
					0 => (object) array( '==' => (object) array( 'order.base.service.attribute.code' => 'REFID' ) ),
					1 => (object) array( '==' => (object) array( 'order.base.service.attribute.editor' => 'core:unittest' ) ),
				)
			),
			'sort' => 'order.base.service.attribute.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'REFID', $result['items'][0]->{'order.base.service.attribute.code'} );
	}

	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$serviceManager = $baseManager->getSubManager( 'service' );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.code', 'OGONE' ) );
		$results = $serviceManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.service.attribute.ordservid' => $expected->getId(),
				'order.base.service.attribute.code' => 'FooBar',
				'order.base.service.attribute.value' => 'ValueTest',
				'order.base.service.attribute.name' => 'TestName'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.service.attribute.code' => 'FooBar' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.service.attribute.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.service.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.id'}, $searched['items'][0]->{'order.base.service.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.ordservid'}, $searched['items'][0]->{'order.base.service.attribute.ordservid'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.code'}, $searched['items'][0]->{'order.base.service.attribute.code'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.name'}, $searched['items'][0]->{'order.base.service.attribute.name'} );
		$this->assertEquals( $saved['items']->{'order.base.service.attribute.value'}, $searched['items'][0]->{'order.base.service.attribute.value'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

