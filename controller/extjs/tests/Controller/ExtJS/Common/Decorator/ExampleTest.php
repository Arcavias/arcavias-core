<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_ExtJS_Common_Decorator_ExampleTest.
 */
class Controller_ExtJS_Common_Decorator_ExampleTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		$context = TestHelper::getContext();

		$controller = Controller_ExtJS_Admin_Job_Factory::createController( $context );
		$this->_object = new Controller_ExtJS_Common_Decorator_Example( $context, $controller );
	}


	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCall()
	{
		$this->_object->additionalMethod();
	}


	public function testDeleteItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->deleteItems( new stdClass() );
	}


	public function testSaveItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->saveItems( new stdClass() );
	}


	public function testSearchItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->searchItems( new stdClass() );
	}


	public function testGetServiceDescription()
	{
		$this->_object->getServiceDescription();
	}


	public function testGetItemSchema()
	{
		$this->_object->getItemSchema();
	}


	public function testGetSearchSchema()
	{
		$this->_object->getSearchSchema();
	}

}