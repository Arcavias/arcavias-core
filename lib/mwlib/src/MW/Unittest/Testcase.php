<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage Unittest
 * @version $Id: Testcase.php 423 2012-03-21 11:34:14Z spopp $
 */


/**
 * Unit test case class which irons out differences between phpunit 3.4 and 3.6
 *
 * @package MW
 * @subpackage Unittest
 */
class MW_Unittest_Testcase extends PHPUnit_Framework_TestCase
{
	private static $_methodExists = array();


	/**
	* Checks if given method exists in current version of PHPUnit.
	*
	* @param string $method Method to check
	* @return boolean
	*/
	private static function _checkMethod( $method )
	{
		if( !isset( self::$_methodExists[ $method ] ) ) {
			self::$_methodExists[ $method ] = method_exists( 'PHPUnit_Framework_TestCase', $method );
		}

		return self::$_methodExists[ $method ];
	}


	/**
	 * Calls assertType() of parent class if available.
	 * Available from PHPUnit <= 3.5
	 *
	 * @param mixed $expected Expected value
	 * @param mixed $actual Actual value
	 * @param string $message Message to print if assertion is wrong
	 * @throws Exception If assertType() method is not available
	 */
	public static function assertType( $expected, $actual, $message = '' )
	{
		if( self::_checkMethod( 'assertType' ) ) {
			parent::assertType( $expected, $actual, $message );
		}
		else {
			throw new Exception('assertType() is removed since phpunit >= 3.5');
		}
	}


	/**
	 * Calls assertType() or assertInternalType() depending on the PHPUnit version.
	 * Available from PHPUnit >= 3.5
	 *
	 * @param mixed $expected Expected value
	 * @param mixed $actual Actual value
	 * @param string $message Message to print if assertion is wrong
	 */
	public static function assertInternalType( $expected, $actual, $message = '' )
	{
		if( self::_checkMethod( 'assertInternalType' ) ) {
			parent::assertInternalType( $expected, $actual, $message );
		}
		else {
			parent::assertType( $expected, $actual, $message );
		}

	}


	/**
	 * Calls assertType() or assertInstanceOf() depending on the PHPUnit version.
	 * Available from PHPUnit >= 3.5
	 *
	 * @param mixed $expected Expected value
	 * @param mixed $actual Actual value
	 * @param string $message Message to print if assertion is wrong
	 */
	public static function assertInstanceOf( $expected, $actual, $message = '' )
	{
		if( self::_checkMethod( 'assertInstanceOf' ) ) {
			parent::assertInstanceOf( $expected, $actual, $message );
		}
		else {
			parent::assertType( $expected, $actual, $message );
		}
	}


	/**
	 * Calls assertEmpty() or assertThat() depending on the PHPUnit version.
	 * Available from PHPUnit >= 3.5
	 *
	 * @param mixed $actual Actual value
	 * @param string $message Message to print if assertion is wrong
	 */
	public static function assertEmpty( $actual, $message = '' )
	{
		if( self::_checkMethod( 'assertEmpty' ) ) {
			parent::assertEmpty( $actual, $message );
		}
		else {
			parent::assertThat($actual, parent::isEmpty(), $message);
		}
	}
}
