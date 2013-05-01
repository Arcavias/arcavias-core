<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

/**
 * Test class for MShop_Price_Item_Default.
 */
class MShop_Price_Item_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Price_Item_Default
	 * @access protected
	 */
	private $_object;
	private $_values;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Price_Item_DefaultTest');
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
		$this->_values = array(
			'id' => 199,
			'siteid'=>99,
			'typeid' => 2,
			'type' => 'default',
			'currencyid' => 'EUR',
			'domain' => 'product',
			'label' => 'Price label',
			'quantity' => 1500,
			'value' => 195.50,
			'shipping' => 19.95,
			'rebate' => 10.00,
			'taxrate' => 19.00,
			'status' => true,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Price_Item_Default( $this->_values );
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
		$this->assertEquals( 199, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertNull( $this->_object->getId());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'default', $this->_object->getType() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->_object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->_object->setTypeId(99);
		$this->assertEquals(99, $this->_object->getTypeId());

		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetCurrencyId()
	{
		$this->assertEquals( 'EUR', $this->_object->getCurrencyId() );
	}

	public function testSetCurrencyId()
	{
		$this->_object->setCurrencyId( 'USD' );
		$this->assertEquals( 'USD', $this->_object->getCurrencyId() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->_object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->_object->setDomain( 'service' );
		$this->assertEquals( 'service', $this->_object->getDomain() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'Price label', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel( 'special price' );
		$this->assertEquals( 'special price', $this->_object->getlabel() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetQuantity()
	{
		$this->assertEquals( 1500, $this->_object->getQuantity() );
	}

	public function testSetQuantity()
	{
		$this->_object->setQuantity( 2000 );
		$this->assertEquals( 2000, $this->_object->getQuantity() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetPrice()
	{
		$this->assertEquals( '195.50', $this->_object->getValue() );
	}

	public function testSetPrice()
	{
		$this->_object->setValue( 199.00 );
		$this->assertEquals( 199.00, $this->_object->getValue() );
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Price_Exception');
		$this->_object->setValue( '190,90' );
	}

	public function testGetShipping()
	{
		$this->assertEquals( '19.95', $this->_object->getShipping() );
	}

	public function testSetShipping()
	{
		$this->_object->setValue( '20.00' );
		$this->assertEquals( 20.00, $this->_object->getValue() );
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Price_Exception');
		$this->_object->setValue( '19,90' );
	}

	public function testGetRebate()
	{
		$this->assertEquals( '10.00', $this->_object->getRebate() );
	}

	public function testSetRebate()
	{
		$this->_object->setRebate( '20.00' );
		$this->assertEquals( 20.00, $this->_object->getRebate() );
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Price_Exception');
		$this->_object->setValue( '19,90' );
	}

	public function testgetTaxRate()
	{
		$this->assertEquals( '19.00', $this->_object->getTaxRate() );
	}

	public function testsetTaxRate()
	{
		$this->_object->setTaxRate( '22.00' );
		$this->assertEquals( 22.00, $this->_object->getTaxRate() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertEquals( 0, $this->_object->getStatus() );
		$this->assertTrue( $this->_object->isModified() );
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

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['price.id'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['price.typeid'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['price.siteid'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['price.label'] );
		$this->assertEquals( $this->_object->getCurrencyId(), $arrayObject['price.currencyid'] );
		$this->assertEquals( $this->_object->getQuantity(), $arrayObject['price.quantity'] );
		$this->assertEquals( $this->_object->getValue(), $arrayObject['price.value'] );
		$this->assertEquals( $this->_object->getShipping(), $arrayObject['price.shipping'] );
		$this->assertEquals( $this->_object->getRebate(), $arrayObject['price.rebate'] );
		$this->assertEquals( $this->_object->getTaxrate(), $arrayObject['price.taxrate'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['price.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['price.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['price.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['price.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
}
