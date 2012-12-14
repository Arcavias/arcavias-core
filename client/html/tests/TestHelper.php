<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TestHelper.php 1326 2012-10-21 15:41:46Z nsendetzky $
 */


class TestHelper
{
	private static $_view;
	private static $_mshop;
	private static $_context = array();


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

		return self::$_context[$site];
	}


	public static function getView()
	{
		if( !isset( self::$_view ) )
		{
			self::$_view = new MW_View_Default();

			$helper = new MW_View_Helper_Translate_Default( self::$_view, new MW_Translation_None( 'en_GB' ) );
			self::$_view->addHelper( 'translate', $helper );

			$helper = new MW_View_Helper_Url_Default( self::$_view, 'baseurl' );
			self::$_view->addHelper( 'url', $helper );

			$helper = new MW_View_Helper_Number_Default( self::$_view, '.', '' );
			self::$_view->addHelper( 'number', $helper );

			$helper = new MW_View_Helper_Date_Default( self::$_view, 'Y-m-d' );
			self::$_view->addHelper( 'date', $helper );

			$helper = new MW_View_Helper_Config_Default( self::$_view, array() );
			self::$_view->addHelper( 'config', $helper );

			$helper = new MW_View_Helper_Parameter_Default( self::$_view, array() );
			self::$_view->addHelper( 'param', $helper );

			$helper = new MW_View_Helper_FormParam_Default( self::$_view );
			self::$_view->addHelper( 'formparam', $helper );
		}

		return self::$_view;
	}


	public static function getHtmlTemplatePaths()
	{
		return self::_getMShop()->getCustomPaths( 'client/html' );
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

		$conf = new MW_Config_Zend( new Zend_Config( array(), true ), $paths );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$writer = new Zend_Log_Writer_Stream( $site . '.log');
		$zlog = new Zend_Log($writer);
		$filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
		$zlog->addFilter($filter);

		$logger = new MW_Logger_Zend( $zlog );
		$ctx->setLogger( $logger );


		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( 'core:client/html' );

		return $ctx;
	}
}
