<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of order history basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Account_History_Detail_Basket_Default
	extends Client_Html_Common_Summary_Detail_Default
	implements Client_Html_Interface
{
	private $_cache;


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
			$view = parent::_setViewParams( $view );

			$view->summaryTaxRates = $this->_getTaxRates( $view->summaryBasket );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}