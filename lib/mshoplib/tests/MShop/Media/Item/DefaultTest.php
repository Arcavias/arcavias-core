<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

class MShop_Media_Item_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Media_Item_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 1,
			'siteid' => 123,
			'typeid' => 2,
			'type' => 'category',
			'domain' => 'test_dom',
			'label' => 'testPicture',
			'mimetype' => 'image/jpeg',
			'url' => 'http://www.url.com/test.jpg',
			'preview' => '/directory/test.jpg',
			'status' => 6,
			'langid' => 'de',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Media_Item_Default( $this->values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'test_dom', $this->_object->getDomain() );
	}


	public function testSetDomain()
	{
		$this->_object->setDomain( null );
		$this->assertEquals( null, $this->_object->getDomain() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'category', $this->_object->getType() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->_object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->_object->setTypeId( 3 );
		$this->assertEquals( 3, $this->_object->getTypeId() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testPicture', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'newPicture' );
		$this->assertEquals( 'newPicture', $this->_object->getLabel() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetMimeType()
	{
		$this->assertEquals( 'image/jpeg', $this->_object->getMimeType() );
	}


	public function testSetMimeType()
	{
		$this->_object->setMimeType( 'image/png' );
		$this->assertEquals( 'image/png', $this->_object->getMimeType() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.url.com/test.jpg', $this->_object->getUrl() );
	}


	public function testSetUrl()
	{
		$this->_object->setUrl( '/pictures/category.jpg' );
		$this->assertEquals( '/pictures/category.jpg', $this->_object->getUrl() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetPreview()
	{
		$this->assertEquals( '/directory/test.jpg', $this->_object->getPreview() );
	}


	public function testSetPreview()
	{
		$this->_object->setPreview( '/pictures/category.jpg' );
		$this->assertEquals( '/pictures/category.jpg', $this->_object->getPreview() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 6, $this->_object->getStatus() );
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

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['media.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['media.siteid'] );
		$this->assertEquals( $this->_object->getDomain(), $arrayObject['media.domain'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['media.label'] );
		$this->assertEquals( $this->_object->getLanguageId(), $arrayObject['media.languageid'] );
		$this->assertEquals( $this->_object->getMimeType(), $arrayObject['media.mimetype'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['media.typeid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['media.type'] );
		$this->assertEquals( $this->_object->getUrl(), $arrayObject['media.url'] );
		$this->assertEquals( $this->_object->getPreview(), $arrayObject['media.preview'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['media.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['media.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['media.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['media.editor'] );
	}

}