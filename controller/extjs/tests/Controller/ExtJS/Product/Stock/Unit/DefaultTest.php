<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_Stock_Unit_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Controller_ExtJS_Product_Stock_Unit_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'product.stock.unit.code' => 'unit' ) ) ) ),
			'sort' => 'product.stock.unit.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertEquals( 'unit_1', $result['items'][0]->{'product.stock.unit.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.stock.unit.code' => 'test',
				'product.stock.unit.label' => 'label',
				'product.stock.unit.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.unit.code' => 'test' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.stock.unit.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.stock.unit.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.unit.id'}, $searched['items'][0]->{'product.stock.unit.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.unit.code'}, $searched['items'][0]->{'product.stock.unit.code'} );
		$this->assertEquals( $saved['items']->{'product.stock.unit.label'}, $searched['items'][0]->{'product.stock.unit.label'} );
		$this->assertEquals( $saved['items']->{'product.stock.unit.status'}, $searched['items'][0]->{'product.stock.unit.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
