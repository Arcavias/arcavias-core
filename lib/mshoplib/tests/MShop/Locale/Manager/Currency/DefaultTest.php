<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */


/**
 * Test class for MShop_Locale_Manager_Currency_Default.
 */
class MShop_Locale_Manager_Currency_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		$this->_object = new MShop_Locale_Manager_Currency_Default(TestHelper::getContext());
	}


	protected function tearDown()
	{
		$this->_object = null;
	}


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MShop_Locale_Manager_Currency_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf('MShop_Locale_Item_Currency_Interface', $this->_object->createItem());
	}


	public function testSaveUpdateDeleteItem()
	{
		// insert case
		$item = $this->_object->createItem();
		$item->setLabel( 'new name' );
		$item->setStatus( true );
		$item->setCode( 'XXX' );

		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		// update case
		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'new new name' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );


		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem($item->getId());
	}


	public function testGetItem()
	{
		$actual = $this->_object->getItem('EUR');

		$this->assertEquals('EUR', $actual->getId());
		$this->assertEquals('Euro', $actual->getLabel());
		$this->assertEquals(1, $actual->getStatus());
		$this->assertEquals('EUR', $actual->getCode());
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '==', 'locale.currency.id', 'EUR' );
		
		$expr[] = $search->compare( '==', 'locale.currency.label', 'Euro' );
		$expr[] = $search->compare( '==', 'locale.currency.code', 'EUR' );
		$expr[] = $search->compare( '==', 'locale.currency.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.currency.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.currency.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.currency.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		// search without base criteria, slice & total
		$search = $this->_object->createSearch();
		$search->setConditions($search->compare('~=', 'locale.currency.label', 'CFA'));
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals(1, count( $results ));
		$this->assertEquals(2, $total);

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSearchAttributes()
	{
		foreach ( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $attribute);
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}
