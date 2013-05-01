<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Media_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Media_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Media_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.domain' => 'text' ) ) ) ),
			'sort' => 'media.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'media.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Text']['items'] ) );
		$this->assertEquals( 'Bildbeschreibung', $result['graph']['Text']['items'][0]->{'text.content'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1, 'media.list.type.domain' => 'text' );
		$mediaManager = new Controller_ExtJS_Media_Default( TestHelper::getContext() );
		$result = $mediaManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$mediaListTypeManager = Controller_ExtJS_Media_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $mediaListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'media.list.parentid' => $result['items'][0]->{'media.id'},
				'media.list.typeid' => $resultType['items'][0]->{'media.list.type.id'},
				'media.list.domain' => 'text',
				'media.list.refid' => -1,
				'media.list.datestart' => '2000-01-01 00:00:00',
				'media.list.dateend' => '2000-01-01 00:00:00',
				'media.list.position' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.refid' => -1 ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.list.id'} );
		$this->assertEquals( $saved['items']->{'media.list.id'}, $searched['items'][0]->{'media.list.id'});
		$this->assertEquals( $saved['items']->{'media.list.parentid'}, $searched['items'][0]->{'media.list.parentid'});
		$this->assertEquals( $saved['items']->{'media.list.typeid'}, $searched['items'][0]->{'media.list.typeid'});
		$this->assertEquals( $saved['items']->{'media.list.domain'}, $searched['items'][0]->{'media.list.domain'});
		$this->assertEquals( $saved['items']->{'media.list.refid'}, $searched['items'][0]->{'media.list.refid'});
		$this->assertEquals( $saved['items']->{'media.list.datestart'}, $searched['items'][0]->{'media.list.datestart'});
		$this->assertEquals( $saved['items']->{'media.list.dateend'}, $searched['items'][0]->{'media.list.dateend'});
		$this->assertEquals( $saved['items']->{'media.list.position'}, $searched['items'][0]->{'media.list.position'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
