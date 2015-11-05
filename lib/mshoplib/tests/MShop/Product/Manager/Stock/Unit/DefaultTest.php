<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Product_Stock_Unit_Default.
 */
class MShop_Product_Manager_Stock_Unit_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Product_Manager_Stock_Unit_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Stock_Unit_Interface', $this->_object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.stock.unit.code', 'unit_1' ),
			$search->compare( '==', 'product.stock.unit.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product stock unit item found' );
		}

		$item->setId(null);
		$item->setCode( 'unit test' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit test 2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.stock.unit.code', 'unit_1' ),
			$search->compare( '==', 'product.stock.unit.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search );

		if( ($expected = reset($result)) === false ) {
			throw new Exception( 'No product stock unit item found' );
		}

		$actual = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.stock.unit.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.unit.siteid', null );
		$expr[] = $search->compare( '==', 'product.stock.unit.code', 'unit_1' );
		$expr[] = $search->compare( '>=', 'product.stock.unit.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.stock.unit.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.stock.unit.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsTotal()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'product.stock.unit.code', 'unit' ),
			$search->compare( '==', 'product.stock.unit.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 2 );

		$total = 0;
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 4, $total );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}
