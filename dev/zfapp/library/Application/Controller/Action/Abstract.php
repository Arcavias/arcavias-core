<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: Abstract.php 1357 2012-10-30 11:20:09Z nsendetzky $
 */

abstract class Application_Controller_Action_Abstract extends Zend_Controller_Action
{
	private $_mshop;


	public function init()
	{
		parent::init();

		$this->config = Zend_Registry::get('config');

		if ( !isset( $this->config['defaultLimit'] ) ) {
			$this->defaultLimit = 24;
		} else {
			$this->defaultLimit = (int) $this->config['defaultLimit'];
		}

		$basescript = $this->getFrontController()->getBaseUrl();
		$pathstart = dirname( $basescript );
		$viewParts = array(
			'basescript' => $basescript,
			'pathstart' => $pathstart,
			'defaultLimit' => $this->defaultLimit,
		);
		$this->view->assign( $viewParts );


		$params = $this->_getAllParams();

		/*
		 * prepare parameters for the router and view
		 */
		if ( !isset( $params['site'] ) ) {
			$this->_setParam( 'site', $this->config['defaultSite'] );
			$site = $params['site'] = $this->config['defaultSite'];
		} else {
			$site = $params['site'];
		}


		$mshop = $this->_getMShop();
		$ctx = new MShop_Context_Item_Default();

		$configPaths = $mshop->getConfigPaths( 'mysql' );
		$configPaths[] = ZFAPP_ROOT . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Zend( new Zend_Config( array(), true ), $configPaths );
		if( function_exists( 'apc_store' ) === true ) {
			$conf = new MW_Config_Decorator_APC( $conf );
		}
		$ctx->setConfig( $conf );

		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );

		$session = new MW_Session_PHP();
		$ctx->setSession( $session );

		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );

		$logger = MAdmin_Log_Manager_Factory::createManager( $ctx );
		$ctx->setLogger( $logger );

		$localeManager = MShop_Locale_Manager_Factory::createManager($ctx);
		$localeItem = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale($localeItem);

		Zend_Registry::set('ctx', $ctx);


		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $ctx );
		Zend_Registry::set('MShop_Catalog_Manager', $catalogManager);

		$catIdRoot = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE )->getId();
		$this->_setParam( 'catid-root', $catIdRoot );
		$params['catid-root'] = $catIdRoot;

		if ( !isset( $params['f-catalog-id'] ) ) {
			$this->_setParam( 'f-catalog-id', $catIdRoot );
			$params['f-catalog-id'] = $catIdRoot;
		}

		$this->view->params = $params;
	}


	protected function _createView()
	{
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->setGlobalParam( 'site', $this->_getParam( 'site' ) );

		$baseurl = dirname( dirname( $this->getFrontController()->getBaseUrl() ) );

		$config = array(
			'baseurl-content' => dirname( $baseurl ) . '/images/',
			'baseurl-template' => dirname( dirname( $baseurl ) ) . '/client/html/lib/',
			'catalog-list-target' => 'routeDefault',
			'catalog-detail-target' => 'routeDefault',
			'basket-target' => 'routeDefault',
		);

		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Zend( $view, $router );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Translate_Default( $view, new MW_Translation_None( 'en_GB' ) );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, $this->_getAllParams() );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Date_Default( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$helper = new MW_View_Helper_FormParam_Default( $view );
		$view->addHelper( 'formparam', $helper );

		return $view;
	}


	protected function _getMShop()
	{
		if( !isset( $this->_mshop ) ) {
			$this->_mshop = new MShop();
		}

		return $this->_mshop;
	}
}
