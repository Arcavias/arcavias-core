<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 * @version $Id: Default.php 1324 2012-10-21 13:17:19Z nsendetzky $
 */


/**
 * Default implementation of catalog tree filter section in HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Filter_Tree_Default
	extends Client_Html_Abstract
	implements Client_Html_Interface
{
	private $_subPartPath = 'client/html/catalog/filter/tree/default/subparts';
	private $_subPartNames = array();
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string HTML code
	 */
	public function getBody()
	{
		$view = $this->_setViewParams( $this->getView() );

		$navHelper = new MW_View_Helper_NavTree_Default( $view );
		$view->addHelper( 'navtree', $navHelper );

		$html = '';
		foreach( $this->_getSubClients( $this->_subPartPath, $this->_subPartNames ) as $subclient ) {
			$html .= $subclient->setView( $view )->getBody();
		}
		$view->treeBody = $html;

		$tplconf = 'client/html/catalog/filter/tree/default/template-body';
		$default = 'catalog/filter/tree-body-default.html';

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
		$view->treeHeader = $html;

		$tplconf = 'client/html/catalog/filter/tree/default/template-header';
		$default = 'catalog/filter/tree-header-default.html';

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
		return $this->_createSubClient( 'catalog/filter/tree/' . $type, $name );
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
			$manager = MShop_Catalog_Manager_Factory::createManager( $this->_getContext() );

			$catItems = $manager->getPath( $view->param( 'f-catalog-id' ) );

			$startid = $view->config( 'catalog/filter/tree/startid' );
			$ref = array( 'text', 'media', 'attribute' );
			$level = MW_Tree_Manager_Abstract::LEVEL_TREE;

			$parentIds = array_keys( $catItems );
			$parentIds[] = 0; // root node

			$search = $manager->createSearch();
			$expr = $search->compare( '==', 'catalog.parentid', $parentIds );

			if( ( $levels = $view->config( 'catalog/filter/tree/levels-always' ) ) !== null ) {
				$expr = $search->combine( '||', array( $expr, $search->compare( '<=', 'catalog.level', $levels ) ) );
			}

			if( ( $levels = $view->config( 'catalog/filter/tree/levels-only' ) ) !== null ) {
				$expr = $search->combine( '&&', array( $expr, $search->compare( '<=', 'catalog.level', $levels ) ) );
			}

			$search->setConditions( $expr );

			$view->treeCatalogTree = $manager->getTree( $startid, $ref, $level, $search );
			$view->treeCatalogPath = $catItems;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}
