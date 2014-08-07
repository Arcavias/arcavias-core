<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default order manager base product attribute.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Product_Attribute_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Product_Attribute_Interface
{
	private $_searchConfig = array(
		'order.base.product.attribute.id' => array(
			'code'=>'order.base.product.attribute.id',
			'internalcode'=>'mordbaprat."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_product_attr" AS mordbaprat ON ( mordbapr."id" = mordbaprat."ordprodid" )' ),
			'label'=>'Order base product attribute ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.siteid' => array(
			'code'=>'order.base.product.attribute.siteid',
			'internalcode'=>'mordbaprat."siteid"',
			'label'=>'Order base product attribute site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.attributeid' => array(
			'code'=>'order.base.product.attribute.attributeid',
			'internalcode'=>'mordbaprat."attrid"',
			'label'=>'Order base product attribute original ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'order.base.product.attribute.productid' => array(
			'code'=>'order.base.product.attribute.productid',
			'internalcode'=>'mordbaprat."ordprodid"',
			'label'=>'Order base product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.product.attribute.type' => array(
			'code'=>'order.base.product.attribute.type',
			'internalcode'=>'mordbaprat."type"',
			'label'=>'Order base product attribute type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.code' => array(
			'code'=>'order.base.product.attribute.code',
			'internalcode'=>'mordbaprat."code"',
			'label'=>'Order base product attribute code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.value' => array(
			'code'=>'order.base.product.attribute.value',
			'internalcode'=>'mordbaprat."value"',
			'label'=>'Order base product attribute value',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.name' => array(
			'code'=>'order.base.product.attribute.name',
			'internalcode'=>'mordbaprat."name"',
			'label'=>'Order base product attribute name',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.mtime' => array(
			'code'=>'order.base.product.attribute.mtime',
			'internalcode'=>'mordbaprat."mtime"',
			'label'=>'Order base product attribute modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.product.attribute.ctime'=> array(
			'code'=>'order.base.product.attribute.ctime',
			'internalcode'=>'mordbaprat."ctime"',
			'label'=>'Order base product attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'order.base.product.attribute.editor'=> array(
			'code'=>'order.base.product.attribute.editor',
			'internalcode'=>'mordbaprat."editor"',
			'label'=>'Order base product attribute editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-order' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/product/attribute/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/base/product/attribute/default/item/delete' );
	}


	/**
	 * Creates a new order base product attribute object.
	 *
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface New media object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface Returns order base product attribute item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.product.attribute.id', $id, $ref );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param MShop_Order_Item_Base_Product_Attribute_Interface $item New item that should
     * be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Product_Attribute_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			$path = 'mshop/order/manager/base/product/attribute/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getAttributeId() );
			$stmt->bind( 3, $item->getProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getType() );
			$stmt->bind( 5, $item->getCode() );
			$stmt->bind( 6, $item->getValue() );
			$stmt->bind( 7, $item->getName() );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if ( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/product/attribute/default/item/newid';
					$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch(Exception $e)
		{
			$dbm->release( $conn, $dbname );
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
		$path = 'mshop/order/manager/base/product/attribute/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes($withsub = true)
	{
		/** classes/order/manager/base/product/attribute/submanagers
		 * List of manager names that can be instantiated by the order base product attribute manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'classes/order/manager/base/product/attribute/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/product/attribute/name
		 * Class name of the used order base product attribute manager implementation
		 *
		 * Each default order base product attribute manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Product_Attribute_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Product_Attribute_Myattribute
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/product/attribute/name = Myattribute
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAttribute"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/product/attribute/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base product attribute manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/global
		 * @see mshop/order/manager/base/product/attribute/decorators/local
		 */

		/** mshop/order/manager/base/product/attribute/decorators/global
		 * Adds a list of globally available decorators only to the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/excludes
		 * @see mshop/order/manager/base/product/attribute/decorators/local
		 */

		/** mshop/order/manager/base/product/attribute/decorators/local
		 * Adds a list of local decorators only to the order base product attribute manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base product attribute manager.
		 *
		 *  mshop/order/manager/base/product/attribute/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/product/attribute/decorators/excludes
		 * @see mshop/order/manager/base/product/attribute/decorators/global
		 */

		return $this->_getSubManager( 'order', 'base/product/attribute/' . $manager, $name );
	}


	/**
	 * Searches for order product attributes based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of products implementing MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$context = $this->_getContext();
		$logger = $context->getLogger();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = array();

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/product/attribute/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/product/attribute/default/item/count';
			$required = array( 'order.base.product.attribute' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[ $row['id'] ] = $this->_createItem( $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates new order base product attribute item object initialized with given parameters.
	 *
	 * @param array $values Associative array of order product attribute values
	 * @return MShop_Order_Item_Base_Product_Attribute_Interface
	 */
	protected function _createItem(array $values = array())
	{
		return new MShop_Order_Item_Base_Product_Attribute_Default( $values );
	}
}
