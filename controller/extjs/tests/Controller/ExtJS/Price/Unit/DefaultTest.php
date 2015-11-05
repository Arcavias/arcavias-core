<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Price_Unit_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Price_Unit_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'price.unit.code' => '' ) ) ) ),
			'sort' => 'price.unit.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 5, $result['total'] );
		$this->assertEquals( 'default', $result['items'][0]->{'price.unit.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'price.unit.code' => 'test',
				'price.unit.label' => 'testLabel',
				'price.unit.domain' => 'price',
				'price.unit.staus' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.unit.code' => 'test' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.unit.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'price.unit.id'} );
		$this->assertEquals( $saved['items']->{'price.unit.id'}, $searched['items'][0]->{'price.unit.id'} );
		$this->assertEquals( $saved['items']->{'price.unit.code'}, $searched['items'][0]->{'price.unit.code'} );
		$this->assertEquals( $saved['items']->{'price.unit.domain'}, $searched['items'][0]->{'price.unit.domain'} );
		$this->assertEquals( $saved['items']->{'price.unit.label'}, $searched['items'][0]->{'price.unit.label'} );
		$this->assertEquals( $saved['items']->{'price.unit.status'}, $searched['items'][0]->{'price.unit.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
