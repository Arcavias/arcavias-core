<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14562 2011-12-23 11:32:48Z nsendetzky $
 */


/**
 * Default product manager.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Interface
{
	private $_searchConfig = array(
		'product.id'=> array(
			'code'=>'product.id',
			'internalcode'=>'mpro."id"',
			'label'=>'Product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.siteid'=> array(
			'code'=>'product.siteid',
			'internalcode'=>'mpro."siteid"',
			'label'=>'Product site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.typeid'=> array(
			'code'=>'product.typeid',
			'internalcode'=>'mpro."typeid"',
			'label'=>'Product type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.code'=> array(
			'code'=>'product.code',
			'internalcode'=>'mpro."code"',
			'label'=>'Product code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.label'=> array(
			'code'=>'product.label',
			'internalcode'=>'mpro."label"',
			'label'=>'Product label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.suppliercode'=> array(
			'code'=>'product.suppliercode',
			'internalcode'=>'mpro."suppliercode"',
			'label'=>'Product supplier code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.datestart'=> array(
			'code'=>'product.datestart',
			'internalcode'=>'mpro."start"',
			'label'=>'Product start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.dateend'=> array(
			'code'=>'product.dateend',
			'internalcode'=>'mpro."end"',
			'label'=>'Product end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.ctime'=> array(
			'code'=>'product.ctime',
			'internalcode'=>'mpro."ctime"',
			'label'=>'Product create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.mtime'=> array(
			'code'=>'product.mtime',
			'internalcode'=>'mpro."mtime"',
			'label'=>'Product modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.editor'=> array(
			'code'=>'product.editor',
			'internalcode'=>'mpro."editor"',
			'label'=>'Product editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.status'=> array(
			'code'=>'product.status',
			'internalcode'=>'mpro."status"',
			'label'=>'Product status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.contains' => array(
			'code'=>'product.contains()',
			'internalcode'=>'',
			'label'=>'Number of product list items, parameter(<domain>,<list type ID>,<reference IDs>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);

	private $_typeSearchConfig = array(
		'product.type.id' => array(
			'code'=>'product.type.id',
			'internalcode'=>'mproty."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_type" AS mproty ON ( mpro."typeid" = mproty."id" )' ),
			'label'=>'Product type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.type.siteid' => array(
			'code'=>'product.type.siteid',
			'internalcode'=>'mproty."siteid"',
			'label'=>'Product type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.type.code' => array(
			'code'=>'product.type.code',
			'internalcode'=>'mproty."code"',
			'label'=>'Product type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.domain' => array(
			'code'=>'product.type.domain',
			'internalcode'=>'mproty."domain"',
			'label'=>'Product type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.label' => array(
			'code'=>'product.type.label',
			'internalcode'=>'mproty."label"',
			'label'=>'Product type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.status' => array(
			'code'=>'product.type.status',
			'internalcode'=>'mproty."status"',
			'label'=>'Product type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.type.ctime'=> array(
			'code'=>'product.type.ctime',
			'internalcode'=>'mproty."ctime"',
			'label'=>'Product type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.mtime'=> array(
			'code'=>'product.type.mtime',
			'internalcode'=>'mproty."mtime"',
			'label'=>'Product type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.type.editor'=> array(
			'code'=>'product.type.editor',
			'internalcode'=>'mproty."editor"',
			'label'=>'Product type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listSearchConfig = array(
		'product.list.id'=> array(
			'code'=>'product.list.id',
			'internalcode'=>'mproli."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_product_list" AS mproli ON ( mpro."id" = mproli."parentid" )' ),
			'label'=>'Product list ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.siteid'=> array(
			'code'=>'product.list.siteid',
			'internalcode'=>'mproli."siteid"',
			'label'=>'Product list site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.parentid'=> array(
			'code'=>'product.list.parentid',
			'internalcode'=>'mproli."parentid"',
			'label'=>'Product list parent ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.domain'=> array(
			'code'=>'product.list.domain',
			'internalcode'=>'mproli."domain"',
			'label'=>'Product list domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.typeid' => array(
			'code'=>'product.list.typeid',
			'internalcode'=>'mproli."typeid"',
			'label'=>'Product list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.refid'=> array(
			'code'=>'product.list.refid',
			'internalcode'=>'mproli."refid"',
			'label'=>'Product list reference ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.datestart' => array(
			'code'=>'product.list.datestart',
			'internalcode'=>'mproli."start"',
			'label'=>'Product list start date/time',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.dateend' => array(
			'code'=>'product.list.dateend',
			'internalcode'=>'mproli."end"',
			'label'=>'Product list end date/time',
			'type'=> 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.position' => array(
			'code'=>'product.list.position',
			'internalcode'=>'mproli."pos"',
			'label'=>'Product list position',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.list.ctime'=> array(
			'code'=>'product.list.ctime',
			'internalcode'=>'mproli."ctime"',
			'label'=>'Product list create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.mtime'=> array(
			'code'=>'product.list.mtime',
			'internalcode'=>'mproli."mtime"',
			'label'=>'Product list modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.editor'=> array(
			'code'=>'product.list.editor',
			'internalcode'=>'mproli."editor"',
			'label'=>'Product list editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);

	private $_listTypeSearchConfig = array(
		'product.list.type.id' => array(
			'code'=>'product.list.type.id',
			'internalcode'=>'mprolity."id"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_product_list_type" AS mprolity ON ( mproli."typeid" = mprolity."id" )' ),
			'label'=>'Product list type ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.type.siteid' => array(
			'code'=>'product.list.type.siteid',
			'internalcode'=>'mprolity."siteid"',
			'label'=>'Product list type site ID',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.list.type.code' => array(
			'code'=>'product.list.type.code',
			'internalcode'=>'mprolity."code"',
			'label'=>'Product list type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.domain' => array(
			'code'=>'product.list.type.domain',
			'internalcode'=>'mprolity."domain"',
			'label'=>'Product list type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.label' => array(
			'code'=>'product.list.type.label',
			'internalcode'=>'mprolity."label"',
			'label'=>'Product list type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.status' => array(
			'code'=>'product.list.type.status',
			'internalcode'=>'mprolity."status"',
			'label'=>'Product list type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.list.type.ctime'=> array(
			'code'=>'product.list.type.ctime',
			'internalcode'=>'mprolity."ctime"',
			'label'=>'Product list type create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.mtime'=> array(
			'code'=>'product.list.type.mtime',
			'internalcode'=>'mprolity."mtime"',
			'label'=>'Product list type modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.list.type.editor'=> array(
			'code'=>'product.list.type.editor',
			'internalcode'=>'mprolity."editor"',
			'label'=>'Product list type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates the product manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct(MShop_Context_Item_Interface $context)
	{
		parent::__construct( $context );

		$date = date( 'Y-m-d H:i:00' );

		$this->_searchConfig['product.contains']['internalcode'] =
			'( SELECT COUNT(mproli2."parentid") FROM "mshop_product_list" AS mproli2
				WHERE mpro."id" = mproli2."parentid" AND :site
					AND mproli2."domain" = $1 AND mproli2."refid" IN ( $3 ) AND mproli2."typeid" = $2
					AND ( mproli2."start" IS NULL OR mproli2."start" <= \'' . $date . '\' )
					AND ( mproli2."end" IS NULL OR mproli2."end" >= \'' . $date . '\' ) )';

		$sites = $context->getLocale()->getSitePath();
		$this->_replaceSiteMarker( $this->_searchConfig['product.contains'], 'mproli2."siteid"', $sites, ':site' );
	}


	/**
	 * Create new product item object.
	 *
	 * @return MShop_Product_Item_Interface
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Adds a new product to the storage.
	 *
	 * @param MShop_Product_Item_Interface $product Product item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$locale = $context->getLocale();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'mshop/product/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $item->getSupplierCode() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getDateStart() );
			$stmt->bind( 8, $item->getDateEnd() );
			$stmt->bind( 9, date('Y-m-d H:i:s', time()) );// mtime
			$stmt->bind( 10, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 11, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 11, date('Y-m-d H:i:s', time()) ); // ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/product/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId( $id ); //so item is no longer modified
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


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		$path = 'mshop/product/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the product item for the given product ID.
	 *
	 * @param integer $id Unique ID to search for
	 * @return MShop_Product_Item_Interface Product item
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.id', $id, $ref );
	}


	/**
	 * Search for products based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = 'mshop/product/manager/default/item/search';
			$cfgPathCount =  'mshop/product/manager/default/item/count';
			$required = array( 'product' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[] = $row['typeid'];
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		if( count( $typeIds ) > 0 )
		{
			$typeManager = $this->getSubManager( 'type' );
			$search = $typeManager->createSearch();
			$search->setConditions( $search->compare( '==', 'product.type.id', array_unique( $typeIds ) ) );
			$typeItems = $typeManager->searchItems( $search );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'product' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$config = $this->_getContext()->getConfig();
			$path = 'classes/product/manager/submanagers';

			foreach ( $config->get($path, array( 'type', 'stock', 'list' )) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes(true));
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
		switch( $manager )
		{
			case 'list':
				$typeManager = $this->_getTypeManager( 'product', 'list/type', null, $this->_listTypeSearchConfig );
				return $this->_getListManager( 'product', $manager, $name, $this->_listSearchConfig, $typeManager );
			case 'type':
				return $this->_getTypeManager( 'product', $manager, $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'product', $manager, $name );
		}
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$curDate = date( 'Y-m-d H:i:00', time() );
			$object = $this->_createSearch( 'product' );

			$expr = array( $object->getConditions() );

			$temp = array();
			$temp[] = $object->compare( '==', 'product.datestart', null );
			$temp[] = $object->compare( '<=', 'product.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'product.dateend', null );
			$temp[] = $object->compare( '>=', 'product.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	* Returns the type search configurations array for the type manager.
	*
	* @return array associative array of the search code as key and the definitions as associative array
	*/
	protected function _getListTypeSearchConfig()
	{
		return $this->_listTypeSearchConfig;
	}


	/**
	* Returns the list search definitions for the list manager.
	*
	* @return array Associative array of the search code as key and the definition as associative array
	*/
	protected function _getListSearchConfig()
	{
		return $this->_listSearchConfig;
	}


	/**
	* Returns the type search configuration definitons for the type manager.
	*
	* @return array Associative array of the search code as key and the definition as associative array
	*/
	protected function _getTypeSearchConfig()
	{
		return $this->_typeSearchConfig;
	}


	/**
	 * Create new product item object initialized with given parameters.
	 *
	 * @param MShop_Product_Item_Interface $product Product item object
	 * @return array Associative list of key/value pairs suitable for product item constructor
	 */
	protected function _createArray( MShop_Product_Item_Interface $item )
	{
		return array(
			'id' => $item->getId(),
			'typeid' => $item->getTypeId(),
			'type' => $item->getType(),
			'status' => $item->getStatus(),
			'label' => $item->getLabel(),
			'start' => $item->getDateStart(),
			'end' => $item->getDateEnd(),
			'code' => $item->getCode(),
			'suppliercode' => $item->getSupplierCode(),
			'ctime' => $item->getTimeCreated(),
			'mtime' => $item->getTimeModified(),
			'editor' => $item->getEditor(),
		);
	}


	/**
	 * Create new product item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $textItems List of items implementing MShop_Text_Item_Interface
	 * @return MShop_Product_Item_Interface New product item
	 */
	protected function _createItem( array $values = array(), array $listitems = array(), array $textItems = array() )
	{
		return new MShop_Product_Item_Default( $values, $listitems, $textItems );
	}
}