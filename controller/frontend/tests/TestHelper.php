<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */


class TestHelper
{
	private static $_mshop;
	private static $_context;


	public static function bootstrap()
	{
		$mshop = self::_getMShop();

		$includepaths = $mshop->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return clone self::$_context[$site];
	}


	private static function _getMShop()
	{
		if( !isset( self::$_mshop ) )
		{
			require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'MShop.php';
			spl_autoload_register( 'MShop::autoload' );

			self::$_mshop = new MShop( array(), false );
		}

		return self::$_mshop;
	}


	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$mshop = self::_getMShop();


		$paths = $mshop->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$writer = new Zend_Log_Writer_Stream('unittests.log');
		$zlog = new Zend_Log($writer);
		$filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
		$zlog->addFilter($filter);

		$logger = new MW_Logger_Zend( $zlog );
		$ctx->setLogger( $logger );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( 'core:controller/frontend' );

		return $ctx;
	}
}
