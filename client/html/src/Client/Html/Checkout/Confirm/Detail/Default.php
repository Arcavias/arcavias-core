<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of checkout detail summary HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Checkout_Confirm_Detail_Default
	extends Client_Html_Checkout_Standard_Summary_Detail_Default
{
	private $_cache;

	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
		}
		$view->detailBody = $html;

		/** client/html/checkout/confirm/detail/default/template-body
		 * Relative path to the HTML body template of the checkout confirm detail.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the layouts directory (usually in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/detail/default/template-header
		 */
		$tplconf = 'client/html/checkout/confirm/detail/default/template-body';
		$default = 'checkout/confirm/detail-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
		}
		$view->basketHeader = $html;

		/** client/html/checkout/confirm/detail/default/template-header
		 * Relative path to the HTML header template of the checkout confirm detail.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the layouts directory (usually
		 * in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/checkout/confirm/detail/default/template-body
		 */
		$tplconf = 'client/html/checkout/confirm/detail/default/template-header';
		$default = 'checkout/confirm/detail-header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}
	
	
	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		$view = parent::_setViewParams( $view, $tags, $expire );

		if( !isset( $this->_cache ) )
		{
			$basket = $view->standardBasket;

			$bTarget = $view->config( 'client/html/basket/standard/url/target' );
			$bCntl = $view->config( 'client/html/basket/standard/url/controller', 'basket' );
			$bAction = $view->config( 'client/html/basket/standard/url/action', 'index' );
			$bConfig = $view->config( 'client/html/basket/standard/url/config', array() );

			$target = $view->config( 'client/html/checkout/standard/url/target' );
			$cntl = $view->config( 'client/html/checkout/standard/url/controller', 'checkout' );
			$action = $view->config( 'client/html/checkout/standard/url/action', 'index' );
			$config = $view->config( 'client/html/checkout/standard/url/config', array() );

			$view->summaryTaxRates = $this->_getTaxRates( $basket );
			$view->summaryBasket = $view->standardBasket;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}