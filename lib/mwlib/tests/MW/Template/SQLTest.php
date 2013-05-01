<?php

/**
 * Test class for MW_Session_CMSLite.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Template_SQLTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Template_CMSLite
	 * @access protected
	 */
	private $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$template = 'SELECT * FROM /*-FROM*/table/*FROM-*/';

		$this->_object = new MW_Template_SQL( $template );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	public function testToString()
	{
		$template = $this->_object->get('FROM');
		$this->assertInstanceOf( 'MW_Template_Interface', $template );

		$this->assertEquals( 'table', $template->str() );
	}
}
