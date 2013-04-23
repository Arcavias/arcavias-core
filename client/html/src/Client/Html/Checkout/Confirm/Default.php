<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of confirm checkout HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Confirm_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/checkout/confirm/default/subparts';
	private $_subPartNames = array( 'basic' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		try
		{
			$view = $this->_setViewParams( $this->getView() );

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getBody();
			}
			$view->confirmBody = $html;
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}

		$tplconf = 'client/html/checkout/confirm/default/template-body';
		$default = 'checkout/confirm/body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		try
		{
			$view = $this->_setViewParams( $this->getView() );

			$html = '';
			foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader();
			}
			$view->confirmHeader = $html;
		}
		catch( Exception $e )
		{
			$this->_getContext()->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return '';
		}

		$tplconf = 'client/html/checkout/confirm/default/template-header';
		$default = 'checkout/confirm/header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'checkout/confirm/' . $type, $name );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return false;
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();

		if( ( $orderid = $this->_getOrderId() ) === null ) {
			return;
		}


		try
		{
			$context = $this->_getContext();
			$sorderid = $context->getSession()->get( 'arcavias/orderid' );

			$orderManager = MShop_Order_Manager_Factory::createManager( $context );
			$orderBaseManager = $orderManager->getSubManager( 'base' );
			$orderServiceManager = $orderBaseManager->getSubManager( 'service' );
			$serviceManager = MShop_Service_Manager_Factory::createManager( $context );


			$search = $orderManager->createSearch();
			$expr = array(
				$search->compare( '==', 'order.id', $orderid ),
				$search->compare( '==', 'order.id', $sorderid )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$orderItems = $orderManager->searchItems( $search );

			if( ( $orderItem = reset( $orderItems ) ) === false ) {
				throw new Client_Html_Exception( sprintf( 'Invalid order ID "%1$s"', $orderid ) );
			}


			$search = $orderServiceManager->createSearch();
			$expr = array(
				$search->compare( '==', 'order.base.service.baseid', $orderItem->getBaseId() ),
				$search->compare( '==', 'order.base.service.type', 'payment' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			foreach( $orderServiceManager->searchItems( $search ) as $service )
			{
				$serviceItem = $serviceManager->getItem( $service->getServiceId() );
				$serviceManager->getProvider( $serviceItem )->updateSync( $view->param() );
			}


			$this->_process( $this->_subPartPath, $this->_subPartNames );

			// Clear basket
			if( $orderItem->getPaymentStatus() > MShop_Order_Item_Abstract::PAY_REFUSED ) {
				$orderBaseManager->setSession( $orderBaseManager->createItem() );
			}
		}
		catch( Client_Html_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'client/html', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( Controller_Frontend_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( MShop_Exception $e )
		{
			$view = $this->getView();
			$error = array( $this->_getContext()->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
		catch( Exception $e )
		{
			$context = $this->_getContext();
			$context->getLogger()->log( $e->getMessage() . PHP_EOL . $e->getTraceAsString() );

			$view = $this->getView();
			$error = array( $context->getI18n()->dt( 'client/html', 'A non-recoverable error occured' ) );
			$view->confirmErrorList = $view->get( 'confirmErrorList', array() ) + $error;
		}
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			if( ( $orderid = $this->_getOrderId() ) !== null )
			{
				$context = $this->_getContext();
				$sorderid = $context->getSession()->get( 'arcavias/orderid' );

				$orderManager = MShop_Order_Manager_Factory::createManager( $context );


				$search = $orderManager->createSearch();
				$expr = array(
					$search->compare( '==', 'order.id', $orderid ),
					$search->compare( '==', 'order.id', $sorderid )
				);
				$search->setConditions( $search->combine( '&&', $expr ) );

				$orderItems = $orderManager->searchItems( $search );

				if( ( $orderItem = reset( $orderItems ) ) === false ) {
					throw new Client_Html_Exception( sprintf( 'Invalid order ID "%1$s"', $orderid ) );
				}

				$view->confirmOrderItem = $orderItem;
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}


	/**
	 * Returns the order ID from the parameters or null.
	 *
	 * @return string|null Order ID or null if no order ID is available
	 */
	protected function _getOrderId()
	{
		foreach( array( 'arcavias', 'plain' ) as $key )
		{
			if( isset( $_GET[$key] ) ) {
				return $_GET[$key];
			}
		}

		return null;
	}
}
