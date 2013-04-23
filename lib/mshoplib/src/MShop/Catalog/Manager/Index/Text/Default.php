<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Default.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */

/**
 * Submanager for text.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Index_Text_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Catalog_Manager_Index_Text_Interface
{
	private $_productManager;
	private $_submanagers = array();

	private $_searchConfig = array(
		'catalog.index.text.id' => array(
			'code'=>'catalog.index.text.id',
			'internalcode'=>':site AND mcatinte."textid"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_catalog_index_text" AS mcatinte ON mcatinte."prodid" = mpro."id"' ),
			'label'=>'Product index text ID',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'catalog.index.text.relevance' => array(
			'code'=>'catalog.index.text.relevance()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinte2."prodid")
				FROM "mshop_catalog_index_text" AS mcatinte2
				WHERE mpro."id" = mcatinte2."prodid" AND :site AND mcatinte2."listtype" = $1
				AND ( mcatinte2."langid" = $2 OR mcatinte2."langid" IS NULL ) AND POSITION( $3 IN mcatinte2."value" ) > 0 )',
			'label'=>'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type'=> 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'sort:catalog.index.text.relevance' => array(
			'code'=>'sort:catalog.index.text.relevance()',
			'internalcode'=>'( SELECT COUNT(DISTINCT mcatinte2."prodid")
				FROM "mshop_catalog_index_text" AS mcatinte2
				WHERE mpro."id" = mcatinte2."prodid" AND :site AND mcatinte2."listtype" = $1
				AND ( mcatinte2."langid" = $2 OR mcatinte2."langid" IS NULL ) AND POSITION( $3 IN mcatinte2."value" ) > 0 )',
			'label'=>'Product texts, parameter(<list type code>,<language ID>,<search term>)',
			'type'=> 'float',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'public' => false,
		),
		'catalog.index.text.value' => array(
			'code'=>'catalog.index.text.value()',
			'internalcode'=>':site AND mcatinte."listtype" = $1 AND ( mcatinte."langid" = $2 OR mcatinte."langid" IS NULL ) AND mcatinte."type" = $3 AND mcatinte."domain" = $4 AND mcatinte."value"',
			'label'=>'Product text by type, parameter(<list type code>,<language ID>,<text type code>,<domain>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'sort:catalog.index.text.value' => array(
			'code'=>'sort:catalog.index.text.value()',
			'internalcode'=>'mcatinte."value"',
			'label'=>'Sort product text by type, parameter(<list type code>,<language ID>,<text type code>,<domain>)',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		)
	);


	/**
	 * Initializes the manager instance.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$this->_productManager = MShop_Product_Manager_Factory::createManager( $context );


		$site = $context->getLocale()->getSitePath();
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );

		$search = $this->createSearch();
		$expr = array(
			$search->compare( '==', 'siteid', null ),
			$search->compare( '==', 'siteid', $site ),
		);
		$search->setConditions( $search->combine( '||', $expr ) );

		$string = $search->getConditionString( $types, array( 'siteid' => 'mcatinte."siteid"' ) );
		$this->_searchConfig['catalog.index.text.id']['internalcode'] =
		str_replace( ':site', $string, $this->_searchConfig['catalog.index.text.id']['internalcode'] );

		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.value'], 'mcatinte."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['catalog.index.text.relevance'], 'mcatinte2."siteid"', $site );
		$this->_replaceSiteMarker( $this->_searchConfig['sort:catalog.index.text.relevance'], 'mcatinte2."siteid"', $site );


		$confpath = 'mshop/catalog/manager/index/text/default/submanagers';

		foreach( $context->getConfig()->get( $confpath, array() ) as $domain ) {
			$this->_submanagers[ $domain ] = $this->getSubManager( $domain );
		}
	}


	/**
	 * Creates new text item object.
	 *
	 * @return MShop_Text_Item_Interface New product item
	 */
	public function createItem()
	{
		return $this->_productManager->createItem();
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		return $this->_productManager->createSearch( $default );
	}


	/**
	 * Removes an item from the index.
	 *
	 * @param integer $id Product ID
	 */
	public function deleteItem( $id )
	{
		$this->deleteItems( array( $id ) );
	}


	/**
	 * Removes multiple items from the index.
	 *
	 * @param array $ids list of Product IDs
	 */
	public function deleteItems( array $ids )
	{
		foreach( $this->_submanagers as $submanager ) {
			$submanager->deleteItems( $ids );
		}

		$context = $this->_getContext();
		$siteid = $context->getLocale()->getSiteId();

		$sql = $context->getConfig()->get( 'mshop/catalog/manager/index/text/default/item/delete' );

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'prodid', $ids ) );

		$types = array( 'prodid' => MW_DB_Statement_Abstract::PARAM_STR );
		$translations = array( 'prodid' => '"prodid"' );

		$cond = $search->getConditionString( $types, $translations );
		$sql = str_replace( ':cond', $cond, $sql );

		try
		{
			$dbm = $context->getDatabaseManager();
			$conn = $dbm->acquire();

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the text item for the given ID
	 *
	 * @param integer $id Id of item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Text_Item_Interface Item object
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_productManager->getItem( $id, $ref );
	}


	/**
	 * Returns a list of objects describing the available criterias for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		$list = array_merge( $list, $this->_productManager->getSearchAttributes( false ) );

		if( $withsub === true )
		{
			foreach( $this->_submanagers as $submanager ) {
				$list = array_merge( $list, $submanager->getSearchAttributes( $withsub ) );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'catalog', 'index/text/' . $manager, $name );
	}


	/**
	 * Optimizes the index if necessary.
	 * Execution of this operation can take a very long time and shouldn't be
	 * called through a web server enviroment.
	 */
	public function optimize()
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$path = 'mshop/catalog/manager/index/text/default/optimize';
			foreach( $context->getConfig()->get( $path, array() ) as $sql ) {
				$conn->create( $sql )->execute()->finish();
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}


		foreach( $this->_submanagers as $submanager ) {
			$submanager->optimize();
		}
	}


	/**
	 * Rebuilds the catalog index text for searching products or specified list of products.
	 * This can be a long lasting operation.
	 *
	 * @param array $items List of product items implementing MShop_Product_Item_Interface
	 */
	public function rebuildIndex( array $items = array() )
	{
		if( empty( $items ) ) { return; }

		MW_Common_Abstract::checkClassList( 'MShop_Product_Item_Interface', $items );

		$context = $this->_getContext();
		$locale = $context->getLocale();
		$siteid = $context->getLocale()->getSiteId();
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );


		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			foreach ( $items as $item )
			{
				$listTypes = array();
				foreach( $item->getListItems( 'text' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}

				$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/text/default/item/insert' );

				foreach( $item->getRefItems( 'text' ) as $refItem )
				{
					if( !isset( $listTypes[ $refItem->getId() ] ) ) {
						$msg = sprintf( 'List type for text item with ID "%1$s" not available', $refItem->getId() );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[ $refItem->getId() ] as $listType )	{
						$this->_saveText( $stmt, $item->getId(), $siteid, $refItem->getId(), $refItem->getLanguageId(), $listType, $refItem->getType(), 'product', $refItem->getContent(), $date, $editor );
					}
				}

				$names = $item->getRefItems( 'text', 'name' );

				if( empty( $names ) ) {
					$this->_saveText( $stmt, $item->getId(), $siteid, null, $locale->getLanguageId(), 'default', 'name', 'product', $item->getLabel(), $date, $editor );
				}
			}

			$dbm->release( $conn );

		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		$this->_saveAttributeTexts( $items );

		foreach( $this->_submanagers as $submanager ) {
			$submanager->rebuildIndex( $items );
		}
	}


	/**
	 * Stores a new item in the index.
	 *
	 * @param MShop_Common_Item_Interface $item Product item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$this->rebuildIndex( array( $item ) );
	}


	/**
	 * Searches for items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Total number of items matched by the given criteria
	 * @return array List of items implementing MShop_Product_Item_Interface with ids as keys
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $ids = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/text/default/item/search';
			$cfgPathCount = 'mshop/catalog/manager/index/text/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			$ids = array();
			while( ( $row = $results->fetch() ) !== false )	{
				$ids[] = $row['id'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		$search = $this->_productManager->createSearch();
		$search->setConditions( $search->compare('==', 'product.id', $ids) );
		$products = $this->_productManager->searchItems( $search, $ref, $total );

		foreach( $ids as $id )
		{
			if( isset( $products[$id] ) ) {
				$items[ $id ] = $products[ $id ];
			}
		}

		return $items;
	}


	/**
	 * Returns product IDs and texts that matches the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @return array Associative list of the product ID as key and the product text as value
	 */
	public function searchTexts( MW_Common_Criteria_Interface $search )
	{
		$list = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/catalog/manager/index/text/default/text/search';
			$required = array( 'product' );

			$total = null;
			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, '', $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$list[ $row['prodid'] ] = $row['value'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $list;
	}


	/**
	 * Saves texts associated with attributes to catalog_index_text.
	 *
	 * @param array $items List of product items implementing MShop_Product_Item_Interface
	 */
	protected function _saveAttributeTexts( array $items )
	{
		$attrIds = array();
		$prodIds = array();
		foreach( $items as $item )
		{
			foreach( $item->getRefItems( 'attribute', null, 'default' ) as $attrItem ) {
				$prodIds[$attrItem->getId()][] = $item->getId();
			}
		}

		$attrManager = MShop_Attribute_Manager_Factory::createManager( $this->_getContext() );
		$search = $attrManager->createSearch(true);
		$expr = array(
			$search->compare( '==', 'attribute.id', array_keys( $prodIds ) ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		$attributeItems = $attrManager->searchItems( $search, array('text') );


		$context = $this->_getContext();
		$locale = $context->getLocale();
		$siteid = $context->getLocale()->getSiteId();
		$editor = $context->getEditor();
		$date = date( 'Y-m-d H:i:s' );


		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();
		$stmt = $this->_getCachedStatement( $conn, 'mshop/catalog/manager/index/text/default/item/insert' );

		try
		{
			foreach ( $attributeItems as $item )
			{
				$listTypes = array();
				foreach( $item->getListItems( 'text', 'default' ) as $listItem ) {
					$listTypes[ $listItem->getRefId() ][] = $listItem->getType();
				}



				foreach( $item->getRefItems( 'text' ) as $refItem )
				{
					if( !isset( $listTypes[ $refItem->getId() ] ) ) {
						$msg = sprintf( 'List type for text item with ID "%1$s" not available', $refItem->getId() );
						throw new MShop_Catalog_Exception( $msg );
					}

					foreach( $listTypes[ $refItem->getId() ] as $listType )
					{
						foreach( $prodIds[$item->getId()] as $idx => $productId ) {
							$this->_saveText( $stmt, $productId, $siteid, $refItem->getId(), $refItem->getLanguageId(), $listType, $refItem->getType(), 'attribute', $refItem->getContent(), $date, $editor );
						}
					}
				}

				$names = $item->getRefItems( 'text', 'name' );

				if( empty( $names ) ) {
					$this->_saveText( $stmt, $prodIds[$item->getId()], $siteid, null, $locale->getLanguageId(), 'default', 'name', 'attribute', $item->getLabel(), $date, $editor );
				}
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	protected function _saveText( $stmt, $id, $siteid, $refid, $lang, $listtype, $reftype, $domain, $label, $date, $editor )
	{
		$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
		$stmt->bind( 2, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
		$stmt->bind( 3, $refid );
		$stmt->bind( 4, $lang );
		$stmt->bind( 5, $listtype );
		$stmt->bind( 6, $reftype );
		$stmt->bind( 7, $domain );
		$stmt->bind( 8, $label );
		$stmt->bind( 9, $date );//mtime
		$stmt->bind( 10, $editor, MW_DB_Statement_Abstract::PARAM_STR );
		$stmt->bind( 11, $date );//ctime
		$stmt->execute()->finish();
	}
}