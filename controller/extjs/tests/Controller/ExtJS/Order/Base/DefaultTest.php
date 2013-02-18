<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


class Controller_ExtJS_Order_Base_DefaultTest extends MW_Unittest_Testcase
{
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Order_Base_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Order_Base_Default( TestHelper::getContext() );
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
				0 => array( '==' => (object) array( 'order.base.sitecode' => 'unittest' ) ),
				1 => array( '>' => (object) array( 'order.base.price' => 19 ) ),
				2 => array( '==' => (object) array( 'order.base.editor' => 'core:unittest' ) )
			) ),
			'sort' => 'order.base.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertGreaterThanOrEqual('', $result['items'][0]->{'order.base.customerid'});
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.id' => null,
				'order.base.customerid' => 'unituser',
				'order.base.comment' => 'FoooBar',
				'order.base.currencyid' => 'EUR',
				'order.base.languageid' => 'en',
				'order.base.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'order.base.customerid' => 'unituser',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.comment' => 'FoooBar' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.id'} );
		$this->assertEquals( $saved['items']->{'order.base.id'}, $searched['items'][0]->{'order.base.id'} );
		$this->assertEquals( $saved['items']->{'order.base.customerid'}, $searched['items'][0]->{'order.base.customerid'} );
		$this->assertEquals( $saved['items']->{'order.base.comment'}, $searched['items'][0]->{'order.base.comment'} );
		$this->assertEquals( $saved['items']->{'order.base.currencyid'}, $searched['items'][0]->{'order.base.currencyid'} );
		$this->assertEquals( $saved['items']->{'order.base.languageid'}, $searched['items'][0]->{'order.base.languageid'} );
		$this->assertEquals( $saved['items']->{'order.base.status'}, $searched['items'][0]->{'order.base.status'} );
		$this->assertEquals( $saved['items']->{'order.base.id'}, $searched['items'][0]->{'order.base.id'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

