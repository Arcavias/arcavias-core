<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Test class for MShop_Supplier_Item_Default.
 */
class MShop_Supplier_Item_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Supplier_Item_Default
	 * @access protected
	 */
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Supplier_Item_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$values = array(
			'id' => 541,
			'siteid'=>99,
			'label' => 'unitObject',
			'code' => 'unitCode',
			'status' => 4,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Supplier_Item_Default( $values );
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

	public function testGetId()
	{
		$this->assertEquals( 541, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue($this->_object->isModified());
		$this->assertNull( $this->_object->getId());
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'unitObject', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel( 'newName' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'newName', $this->_object->getLabel() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'unitCode', $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->_object->setCode( 'newCode' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'newCode', $this->_object->getCode() );
	}



	public function testGetStatus()
	{
		$this->assertEquals( 4, $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 0, $this->_object->getStatus() );
	}
	
	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		
		$this->assertEquals( 541, $arrayObject['supplier.id'] );
		$this->assertEquals( 99, $arrayObject['supplier.siteid'] );
		$this->assertEquals( 'unitObject', $arrayObject['supplier.label'] );
		$this->assertEquals( 'unitCode', $arrayObject['supplier.code'] );
		$this->assertEquals( 4, $arrayObject['supplier.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['supplier.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['supplier.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['supplier.editor'] );
	}
}
