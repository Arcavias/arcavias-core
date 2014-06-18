<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MAdmin_Cache_Item_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 'product/id/1:detail-body',
			'siteid' => 1,
			'value' => 'test',
			'expire' => '2000-01-01 00:00:00',
			'tags' => array( 'tag:1', 'tag:2' ),
		);

		$this->_object = new MAdmin_Cache_Item_Default( $this->_values );
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
		$this->assertEquals( 'product/id/1:detail-body', $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testSetId()
	{
		$this->_object->setId( 'product/id/2:detail-header' );
		$this->assertEquals( 'product/id/2:detail-header', $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->_object->getSiteId() );
	}


	public function testGetValue()
	{
		$this->assertEquals( 'test', $this->_object->getValue() );
	}


	public function testSetValue()
	{
		$this->_object->setValue( 'test2' );
		$this->assertEquals( 'test2', $this->_object->getValue() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetTimeExpire()
	{
		$this->assertEquals( '2000-01-01 00:00:00', $this->_object->getTimeExpire() );
	}


	public function testSetTimeExpire()
	{
		$this->_object->setTimeExpire( '2100-01-01 00:00:00' );
		$this->assertEquals( '2100-01-01 00:00:00', $this->_object->getTimeExpire() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetTags()
	{
		$this->assertEquals( array( 'tag:1', 'tag:2' ), $this->_object->getTags() );
	}


	public function testSetTags()
	{
		$this->_object->setTags( array( 'tag:1', 'tag:3' ) );
		$this->assertEquals( array( 'tag:1', 'tag:3' ), $this->_object->getTags() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testToArray()
	{
		$list = $this->_object->toArray();

		$this->assertEquals( 4, count( $list ) );
		$this->assertEquals( 'product/id/1:detail-body', $list['cache.id'] );
		$this->assertEquals( 1, $list['cache.siteid'] );
		$this->assertEquals( 'test', $list['cache.value'] );
		$this->assertEquals( '2000-01-01 00:00:00', $list['cache.expire'] );
	}
}
