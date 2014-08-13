<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Product_Manager_List.
 */
class MShop_Product_Manager_List_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_editor = $this->_context->getEditor();
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context, 'Default' );
		$this->_object = $productManager->getSubManager( 'list', 'Default' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$search = $this->_object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'product.list.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->aggregate( $search, 'product.list.domain' );

		$this->assertEquals( 6, count( $result ) );
		$this->assertArrayHasKey( 'price', $result );
		$this->assertEquals( 21, $result['price'] );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.list.domain', 'text' ),
			$search->compare( '==', 'product.list.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No list item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testSaveUpdateDeleteItem()
	{
		$siteid = TestHelper::getContext()->getLocale()->getSiteId();

		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.list.siteid', $siteid ),
			$search->compare( '==', 'product.list.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId(null);
		$item->setDomain( 'unittest' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testMoveItemLastToFront()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first product list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last product list item' );
		}

		$this->_object->moveItem( $last->getId(), $first->getId() );

		$newFirst = $this->_object->getItem( $last->getId() );
		$newSecond = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $last->getId() );

		$this->assertEquals( 0, $newFirst->getPosition() );
		$this->assertEquals( 1, $newSecond->getPosition() );
	}


	public function testMoveItemFirstToLast()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first product list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second product list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last product list item' );
		}

		$this->_object->moveItem( $first->getId() );

		$newBefore = $this->_object->getItem( $last->getId() );
		$newLast = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newBefore->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testMoveItemFirstUp()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first product list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second product list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last product list item' );
		}

		$this->_object->moveItem( $first->getId(), $last->getId() );

		$newLast = $this->_object->getItem( $last->getId() );
		$newUp = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newUp->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$siteid = TestHelper::getContext()->getLocale()->getSiteId();


		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.siteid', $siteid ),
			$search->compare( '==', 'product.list.domain', 'media' ),
			$search->compare( '==', 'product.list.datestart', '2000-01-01 00:00:00' ),
			$search->compare( '==', 'product.list.dateend', '2100-01-01 00:00:00' ),
			$search->compare( '!=', 'product.list.config', null ),
			$search->compare( '==', 'product.list.position', 0 ),
			$search->compare( '==', 'product.list.status', 1 ),
			$search->compare( '==', 'product.list.editor', $this->_editor ),
			$search->compare( '==', 'product.list.type.code', 'unittype1' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->searchItems( $search );
		if( ( $listItem = reset( $result ) ) === false ) {
			throw new Exception( 'No list item found' );
		}


		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.list.id', null );
		$expr[] = $search->compare( '==', 'product.list.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'product.list.parentid', null );
		$expr[] = $search->compare( '!=', 'product.list.typeid', null );
		$expr[] = $search->compare( '==', 'product.list.domain', 'media' );
		$expr[] = $search->compare( '==', 'product.list.refid', $listItem->getRefId() );
		$expr[] = $search->compare( '==', 'product.list.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.list.dateend', '2100-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'product.list.config', null );
		$expr[] = $search->compare( '==', 'product.list.position', 0 );
		$expr[] = $search->compare( '==', 'product.list.status', 1 );
		$expr[] = $search->compare( '>=', 'product.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.list.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 2, count( $results ) );


		//search with base criteria
		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'product.list.domain', 'product' ),
			$search->compare( '==', 'product.list.editor', $this->_editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice(0, 4);
		$results = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals( 4, count( $results ) );
		$this->assertEquals( 12, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchRefItems()
	{
		$total = 0;
		$siteid = TestHelper::getContext()->getLocale()->getSiteId();

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.list.domain', array( 'attribute', 'media' ) ) );

		$result = $this->_object->searchRefItems( $search, array( 'text' ), $total );

		$this->assertArrayHasKey( 'attribute', $result );
		$this->assertArrayHasKey( 'media', $result );
		$this->assertArrayNotHasKey( 'price', $result );

		$this->assertEquals( 11, count( $result['attribute'] ) );
		$this->assertEquals( 8, count( $result['media'] ) );

		// this is the total of list items, not the total of referenced items
		// whose number might be lower due to duplicates
		$this->assertEquals( 37, $total );
	}


	protected function _getListItems()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context, 'Default' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.parentid', $item->getId() ),
			$search->compare( '==', 'product.list.domain', 'product' ),
			$search->compare( '==', 'product.list.editor', $this->_editor ),
			$search->compare( '==', 'product.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'product.list.position' ) ) );

		return $this->_object->searchItems( $search );
	}
}
