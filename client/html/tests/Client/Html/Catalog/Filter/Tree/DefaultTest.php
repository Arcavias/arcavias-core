<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1325 2012-10-21 15:41:26Z nsendetzky $
 */

class Client_Html_Catalog_Filter_Tree_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Catalog_Filter_Tree_DefaultTest');
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
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Filter_Tree_Default( TestHelper::getContext(), $paths );
		$this->_object->setView( TestHelper::getView() );
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


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();

		$this->assertContains( '<style type="text/css">', $output );
	}


	public function testGetHeaderWithID()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $node->getId() ) );
		$view->addHelper( 'param', $helper );

		$this->assertContains( '<style type="text/css">', $this->_object->getHeader() );
	}


	public function testGetBody()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );

		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $node->getChild( 1 )->getId() ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertContains( 'Groups', $output );
		$this->assertContains( 'Neu', $output );
		$this->assertContains( 'level-2', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}

}
