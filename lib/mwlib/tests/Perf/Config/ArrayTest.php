<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Perf_Config_ArrayTest extends MW_Unittest_Testcase
{
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Perf_Config_ArrayTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testArray()
	{
		$start = microtime( true );

		$paths = array(
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'one',
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'two',
		);

		for( $i = 0; $i < 1000; $i++ )
		{
			$conf = new MW_Config_Array( $paths );

			$conf->get( 'test/db/host' );
			$conf->get( 'test/db/username' );
			$conf->get( 'test/db/password' );
		}

		$stop = microtime( true );
		echo "\n    config array: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
