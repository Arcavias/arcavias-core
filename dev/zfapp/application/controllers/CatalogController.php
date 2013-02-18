<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogController.php 1357 2012-10-30 11:20:09Z nsendetzky $
 */

/**
 * Product controller
 */
class CatalogController extends Application_Controller_Action_Abstract
{
	public function indexAction()
	{
		$this->_forward( 'list' );
	}


	public function listsimpleAction()
	{
		$startaction = microtime( true );

		$mshop = $this->_getMShop();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $mshop->getCustomPaths( 'client/html' );

		$this->view->listsimple = Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths, 'Simple' );
		$this->view->listsimple->setView( $this->_createView() );
		$this->_helper->layout()->disableLayout();

		$this->view->listsimple->getBody();

		$msg = 'Simple list total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	/**
	 * Shows the catalog with or without given search, pagination criteria
	 */
	public function listAction()
	{
		$startaction = microtime( true );

		$mshop = $this->_getMShop();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $mshop->getCustomPaths( 'client/html' );

		$this->view->filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
		$this->view->filter->setView( $this->_createView() );

		$this->view->searchfilter = $this->view->filter->getSubClient( 'search' );
		$this->view->searchfilter->setView( $this->_createView() );

		$this->view->list = Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths );
		$this->view->list->setView( $this->_createView() );

		$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$this->view->minibasket->setView( $this->_createView() );

		$this->render( 'list' );


		$msg = 'Product::catalog total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	public function detailAction()
	{
		$startaction = microtime( true );


		$mshop = $this->_getMShop();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $mshop->getCustomPaths( 'client/html' );

		$this->view->filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
		$this->view->filter->setView( $this->_createView() );

		$this->view->searchfilter = $this->view->filter->getSubClient( 'search' );
		$this->view->searchfilter->setView( $this->_createView() );

		$this->view->detail = Client_Html_Catalog_Detail_Factory::createClient( $context, $templatePaths );
		$this->view->detail->setView( $this->_createView() );

		$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$this->view->minibasket->setView( $this->_createView() );

		$this->render( 'detail' );


		$msg = 'Product::detail total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
