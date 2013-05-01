<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

class MAdmin_Job_Item_DefaultTest extends MW_Unittest_Testcase
{
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Job_Item_DefaultTest');
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
			'id' => 1,
			'siteid' => 2,
			'label' => 'unittest job',
			'method' => 'Product_Import_Text.importFile',
			'parameter' => array( 'items' => 'testfile.ext' ),
			'result' => array( 'items' => 'testfile2.ext' ),
			'status' => 1,
			'editor' => 'unittest',
			'mtime' => '2010-01-01 00:00:00',
			'ctime' => '2000-01-01 00:00:00',
		);

		$this->_object = new MAdmin_Job_Item_Default( $this->_values );
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
		$this->assertEquals( 1, $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 2, $this->_object->getSiteId() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest job', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'unittest job2' );
		$this->assertEquals( 'unittest job2', $this->_object->getLabel() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetMethod()
	{
		$this->assertEquals( 'Product_Import_Text.importFile', $this->_object->getMethod() );
	}


	public function testSetMethod()
	{
		$this->_object->setMethod( 'Controller.method' );
		$this->assertEquals( 'Controller.method', $this->_object->getMethod() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetParameter()
	{
		$this->assertEquals( array( 'items' => 'testfile.ext' ), $this->_object->getParameter() );
	}


	public function testSetParameter()
	{
		$this->_object->setParameter( array( 'items' => 'newfile.ext' ) );
		$this->assertEquals( array( 'items' => 'newfile.ext' ), $this->_object->getParameter() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetResult()
	{
		$this->assertEquals( array( 'items' => 'testfile2.ext' ), $this->_object->getResult() );
	}


	public function testSetResult()
	{
		$this->_object->setResult( array( 'items' => 'newfile.ext' ) );
		$this->assertEquals( array( 'items' => 'newfile.ext' ), $this->_object->getResult() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->_object->setStatus( -1 );
		$this->assertEquals( -1, $this->_object->getStatus() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unittest', $this->_object->getEditor() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->_object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2000-01-01 00:00:00', $this->_object->getTimeCreated() );
	}


	public function testToArray()
	{
		$list = $this->_object->toArray();

		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( 1, $list['job.id'] );
		$this->assertEquals( 2, $list['job.siteid'] );
		$this->assertEquals( 'unittest job', $list['job.label'] );
		$this->assertEquals( 'Product_Import_Text.importFile', $list['job.method'] );
		$this->assertEquals( json_encode( array( 'items' => 'testfile.ext' ) ), $list['job.parameter'] );
		$this->assertEquals( json_encode( array( 'items' => 'testfile2.ext' ) ), $list['job.result'] );
		$this->assertEquals( 1, $list['job.status'] );
		$this->assertEquals( 'unittest', $list['job.editor'] );
		$this->assertEquals( '2010-01-01 00:00:00', $list['job.mtime'] );
		$this->assertEquals( '2000-01-01 00:00:00', $list['job.ctime'] );
	}
}
