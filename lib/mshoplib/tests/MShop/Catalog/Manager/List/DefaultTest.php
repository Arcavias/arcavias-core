<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14874 2012-01-15 17:19:41Z nsendetzky $
 */


/**
 * Test class for MShop_Catalog_Manager_List.
 */
class MShop_Catalog_Manager_List_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;

	/**
	 * @var string
	 * @access protected
	 */
	protected $_editor = '';

	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_List_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_object = $catalogManager->getSubManager('list');
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


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
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
		$search = $this->_object->createSearch();
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

		$context = TestHelper::getContext();

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

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

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

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testMoveItem()
	{
		// test newpos < oldpos
		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.position', 0 ),
			$search->compare( '==', 'catalog.list.domain', 'text' ),
			$search->compare( '==', 'catalog.list.type.code', 'unittype1' ),
			$search->compare( '==', 'catalog.list.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->_object->searchItems( $search );

		if( ( $first = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$firstId = $first->getId();
		$firstParentId = $first->getParentId();

		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.list.parentid', $firstParentId ),
			$search->compare( '==', 'catalog.list.domain', 'text' ),
			$search->compare( '==', 'catalog.list.type.code', 'unittype1' ),
			$search->compare( '==', 'catalog.list.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'catalog.list.position' ) ) );
		$results = $this->_object->searchItems($search);

		if( ( $second = end($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$secondId = $second->getId();
		$this->_object->moveItem( $firstId, $secondId );

		$first = $this->_object->getItem( $firstId );
		$second = $this->_object->getItem( $secondId );

		$results = $this->_object->searchItems($search);
		if( ( $secondSearch = end($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		if( ( $firstSearch = prev($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$this->assertEquals( $first, $firstSearch );
		$this->assertEquals( $second, $secondSearch );

		// test newpos < oldpos
		if( ( $third = reset($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$thirdId = $third->getId();
		$this->_object->moveItem( $firstId, $thirdId );

		$first = $this->_object->getItem( $firstId );
		$third = $this->_object->getItem( $thirdId );

		$results = $this->_object->searchItems($search);
		if( ( $firstSearch = reset($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		if( ( $thirdSearch = next($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$this->assertEquals( $first, $firstSearch );
		$this->assertEquals( $third, $thirdSearch );

		// test with ref=null
		$this->_object->moveItem( $firstId );

		$first = $this->_object->getItem( $firstId );

		$results = $this->_object->searchItems($search);
		if( ( $firstSearch = end($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$this->assertEquals( $first, $firstSearch );

		// reset database
		if( ( $third = reset($results) ) === false ) {
			$msg = 'No catalog list item with domain "%1$s" and parentid "%2$d" found';
			throw new Exception( sprintf( $msg, 'text', $firstParentId ) );
		}

		$thirdId = $third->getId();
		$this->_object->moveItem( $firstId, $thirdId );
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'catalog.list.id', null );
		$expr[] = $search->compare( '!=', 'catalog.list.siteid', null );
		$expr[] = $search->compare( '!=', 'catalog.list.parentid', null );
		$expr[] = $search->compare( '==', 'catalog.list.domain', 'product' );
		$expr[] = $search->compare( '!=', 'catalog.list.typeid', null );
		$expr[] = $search->compare( '>', 'catalog.list.refid', 0 );
		$expr[] = $search->compare( '==', 'catalog.list.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.dateend', '2099-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.position', 0 );
		$expr[] = $search->compare( '>=', 'catalog.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'catalog.list.type.id', null );
		$expr[] = $search->compare( '!=', 'catalog.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.list.type.code', 'new' );
		$expr[] = $search->compare( '==', 'catalog.list.type.domain', 'product' );
		$expr[] = $search->compare( '>', 'catalog.list.type.label', '' );
		$expr[] = $search->compare( '==', 'catalog.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'catalog.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.type.editor', $this->_editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'catalog.list.type.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems($search);
		$this->assertEquals( 45, count( $results ) );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

}
