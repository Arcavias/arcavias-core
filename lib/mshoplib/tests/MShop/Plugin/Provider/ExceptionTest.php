<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @version $Id$
 */

class MShop_Plugin_Provider_ExceptionTest extends PHPUnit_Framework_TestCase
{

	private $_codes;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_ExceptionTest');
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
		$context = TestHelper::getContext();

		$this->_codes = array( 'something' => array( 'went', 'terribly', 'wrong' ) );

	}

	public function test()
	{
		try {
			throw new MShop_Plugin_Provider_Exception( 'msg', 13, null, $this->_codes );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$this->assertEquals( 13, $mppe->getCode() );
			$this->assertEquals( 'msg', $mppe->getMessage() );
			$this->assertEquals( $this->_codes, $mppe->getErrorCodes() );

		}

		try {
			throw new MShop_Plugin_Provider_Exception( 'msg2', 11, $mppe );
		}
		catch ( MShop_Plugin_Provider_Exception $e )
		{
			$this->assertEquals( array(), $e->getErrorCodes() );
			$this->assertEquals( $mppe, $e->getPrevious() );
		}
	}
}