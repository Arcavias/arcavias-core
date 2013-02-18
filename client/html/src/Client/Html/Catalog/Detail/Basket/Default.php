<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of catalog detail basket section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Basket_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_cache;
	private $_subPartNames = array( 'attribute' );
	private $_subPartPath = 'client/html/catalog/detail/basket/default/subparts';


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

		$tplconf = 'client/html/catalog/detail/basket/default/template-body';
		$default = 'catalog/detail/basket-body-default.html';

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

		$tplconf = 'client/html/catalog/detail/basket/default/template-header';
		$default = 'catalog/detail/basket-header-default.html';

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
		return $this->_createSubClient( 'catalog/detail/basket/' . $type, $name );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$configurables = $view->detailProductItem->getRefItems( 'attribute' );

			$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_getContext() );
			$search = $attributeManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'attribute.id', array_keys( $configurables ) ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$attributeTypes = array();
			$attrDomains = array( 'text', 'price' );

			foreach( $attributeManager->searchItems( $search, $attrDomains ) as $id => $attribute ) {
				$attributeTypes[ $attribute->getType() ][$id] = $attribute;
			}

			$view->detailBasketAttributes = $attributeTypes;

			$this->_cache = $view;
		}

		return $this->_cache;
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what )
	{
		return $this->_isCachable( $what, $this->_subPartPath, $this->_subPartNames );
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
}