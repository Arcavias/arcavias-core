<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout basket summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Standard_Summary_Basket_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartPath = 'client/html/checkout/standard/summary/basket/default/subparts';
	private $_subPartNames = array();


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->basketBody = $html;

		$tplconf = 'client/html/checkout/standard/summary/basket/default/template-body';
		$default = 'checkout/standard/summary-basket-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string String including HTML tags for the header
	 */
	public function getHeader()
	{
		$view = $this->_setViewParams( $this->getView() );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader();
		}
		$view->basketHeader = $html;

		$tplconf = 'client/html/checkout/standard/summary/basket/default/template-header';
		$default = 'checkout/standard/summary-basket-header-default.html';

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
		return $this->_createSubClient( 'checkout/standard/summary/basket/' . $type, $name );
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
		$this->_process( $this->_subPartPath, $this->_subPartNames );
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
			$prices = array();
			$taxrates = array();
			$basket = $view->standardBasket;


			foreach( $basket->getProducts() as $product )
			{
				$price = $product->getPrice();

				if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
					$taxrates[ $price->getTaxrate() ] += ( $price->getValue() + $price->getShipping() ) * $product->getQuantity();
				} else {
					$taxrates[ $price->getTaxrate() ] = ( $price->getValue() + $price->getShipping() ) * $product->getQuantity();
				}
			}

			try
			{
				$price = $basket->getService( 'delivery' )->getPrice();

				if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
					$taxrates[ $price->getTaxrate() ] += $price->getValue() + $price->getShipping();
				} else {
					$taxrates[ $price->getTaxrate() ] = $price->getValue() + $price->getShipping();
				}
			}
			catch( Exception $e ) { ; }

			try
			{
				$price = $basket->getService( 'payment' )->getPrice();

				if( isset( $taxrates[ $price->getTaxrate() ] ) ) {
					$taxrates[ $price->getTaxrate() ] += $price->getValue() + $price->getShipping();
				} else {
					$taxrates[ $price->getTaxrate() ] = $price->getValue() + $price->getShipping();
				}
			}
			catch( Exception $e ) { ; }


			$view->basketTaxRates = $taxrates;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}