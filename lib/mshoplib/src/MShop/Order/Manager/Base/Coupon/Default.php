<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Order base dicount manager class.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Coupon_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Base_Coupon_Interface
{
	private $_searchConfig = array(
		'order.base.coupon.id'=> array(
			'code'=>'order.base.coupon.id',
			'internalcode'=>'mordbaco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_coupon" AS mordbaco ON ( mordba."id" = mordbaco."baseid" )' ),
			'label'=>'Order base coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.siteid'=> array(
			'code'=>'order.base.coupon.siteid',
			'internalcode'=>'mordbaco."siteid"',
			'label'=>'Order base coupon site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.baseid'=> array(
			'code'=>'order.base.coupon.baseid',
			'internalcode'=>'mordbaco."baseid"',
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.coupon.ordprodid'=> array(
			'code'=>'order.base.coupon.productid',
			'internalcode'=>'mordbaco."ordprodid"',
			'label'=>'Order coupon product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.coupon.code'=> array(
			'code'=>'order.base.coupon.code',
			'internalcode'=>'mordbaco."code"',
			'label'=>'Order base coupon code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.mtime'=> array(
			'code'=>'order.base.coupon.mtime',
			'internalcode'=>'mordbaco."mtime"',
			'label'=>'Order base coupon modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.ctime'=> array(
			'code'=>'order.base.coupon.ctime',
			'internalcode'=>'mordbaco."ctime"',
			'label'=>'Order base coupon creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.coupon.editor'=> array(
			'code'=>'order.base.coupon.editor',
			'internalcode'=>'mordbaco."editor"',
			'label'=>'Order base coupon editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
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
		$path = 'classes/order/manager/base/coupon/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/base/coupon/default/item/delete' );
	}


	/**
	 * Creates a new order base coupon object.
	 *
	 * @return MShop_Order_Item_Base_Coupon_Interface New order coupon object
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Returns the order coupon item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @return MShop_Order_Item_Base_Coupon_Interface Item for the given ID
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.coupon.id', $id, $ref );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param MShop_Common_Item_Interface $item Item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Coupon_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if ( !$item->isModified() ) {
			return;
		}

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/order/manager/base/coupon/default/item/';
			$path .=  ( $id === null ? 'insert' : 'update' );

			$stmt = $this->_getCachedStatement($conn, $path);

			$stmt->bind( 1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getProductId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, $item->getCode() );
			$stmt->bind( 5, date( 'Y-m-d H:i:s' ) );
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 7, date( 'Y-m-d H:i:s' ) );// ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/coupon/default/item/newid';
					$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
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
		$path = 'mshop/order/manager/base/coupon/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/base/coupon/submanagers
		 * List of manager names that can be instantiated by the order base coupon manager
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
		$path = 'classes/order/manager/base/coupon/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Return a list of items implementing MShop_Order_Item_Base_Coupon_Interface
	 * @throws MShop_Order_Exception If creation of an item fails
	 */
	public function searchItems(MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null)
	{
		$items = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/coupon/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/coupon/default/item/count';
			$required = array( 'order.base.coupon' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

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
	 * Returns a new sub manager specified by its name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager object
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/coupon/name
		 * Class name of the used order base coupon manager implementation
		 *
		 * Each default order base coupon manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Coupon_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Coupon_Mycoupon
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/coupon/name = Mycoupon
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCoupon"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/coupon/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base coupon manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base coupon manager.
		 *
		 *  mshop/order/manager/base/coupon/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base coupon manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/coupon/decorators/global
		 * @see mshop/order/manager/base/coupon/decorators/local
		 */

		/** mshop/order/manager/base/coupon/decorators/global
		 * Adds a list of globally available decorators only to the order base coupon manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base coupon manager.
		 *
		 *  mshop/order/manager/base/coupon/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/coupon/decorators/excludes
		 * @see mshop/order/manager/base/coupon/decorators/local
		 */

		/** mshop/order/manager/base/coupon/decorators/local
		 * Adds a list of local decorators only to the order base coupon manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base coupon manager.
		 *
		 *  mshop/order/manager/base/coupon/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/coupon/decorators/excludes
		 * @see mshop/order/manager/base/coupon/decorators/global
		 */

		return $this->_getSubManager( 'order', 'base/coupon/' . $manager, $name );
	}


	/**
	 * Create new order base coupon item object initialized with given parameters.
	 *
	 * @return MShop_Order_Item_Base_Coupon_Default New item
	 */
	protected function _createItem(array $values = array())
	{
		return new MShop_Order_Item_Base_Coupon_Default($values);
	}
}
