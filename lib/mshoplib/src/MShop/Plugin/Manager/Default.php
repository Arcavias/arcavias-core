<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Default plugin manager implementation.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Manager_Default
	extends MShop_Plugin_Manager_Abstract
	implements MShop_Plugin_Manager_Interface
{
	private $_plugins = array();

	private $_searchConfig = array(
		'plugin.id' => array(
			'label' => 'Plugin ID',
			'code' => 'plugin.id',
			'internalcode' => 'mplu."id"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.siteid' => array(
			'label' => 'Plugin site ID',
			'code' => 'plugin.siteid',
			'internalcode' => 'mplu."siteid"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.typeid' => array(
			'label' => 'Plugin type ID',
			'code' => 'plugin.typeid',
			'internalcode' => 'mplu."typeid"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'plugin.label' => array(
			'label' => 'Plugin label',
			'code' => 'plugin.label',
			'internalcode' => 'mplu."label"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.provider' => array(
			'label' => 'Plugin provider',
			'code' => 'plugin.provider',
			'internalcode' => 'mplu."provider"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.config' => array(
			'label' => 'Plugin config',
			'code' => 'plugin.config',
			'internalcode' => 'mplu."config"',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.position' => array(
			'label' => 'Plugin position',
			'code' => 'plugin.position',
			'internalcode' => 'mplu."pos"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.status' => array(
			'label' => 'Plugin status',
			'code' => 'plugin.status',
			'internalcode' => 'mplu."status"',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.mtime'=> array(
			'code'=>'plugin.mtime',
			'internalcode'=>'mplu."mtime"',
			'label'=>'Plugin modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.ctime'=> array(
			'code'=>'plugin.ctime',
			'internalcode'=>'mplu."ctime"',
			'label'=>'Plugin creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.editor'=> array(
			'code'=>'plugin.editor',
			'internalcode'=>'mplu."editor"',
			'label'=>'Plugin editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/plugin/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/plugin/manager/default/item/delete' );
	}


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-plugin' );
	}


	/**
	 * Creates a new plugin object.
	 *
	 * @return MShop_Plugin_Item_Interface New plugin object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	* Registers plugins to the given publisher.
	*
	* @param MW_Observer_Publisher_Interface $publisher Publisher object
	*/
	public function register(MW_Observer_Publisher_Interface $publisher, $domain)
	{
		if( !empty( $this->_plugins ) )
		{
			foreach( $this->_plugins as $plugin ) {
				$plugin->register( $publisher );
			}

			return;
		}

		$search = $this->createSearch( true );

		$expr = array();
		$expr[] = $search->compare( '==', 'plugin.type.code', $domain);
		$expr[] = $search->getConditions();

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'plugin.position' ) ) );

		$pluginItems = $this->searchItems( $search );

		$interface = 'MShop_Plugin_Provider_Interface';

		$context = $this->_getContext();

		foreach( $pluginItems as $pluginItem )
		{
			$domain = $pluginItem->getType();
			$providernames = explode( ',', $pluginItem->getProvider() );

			if( ( $providername = array_shift( $providernames ) ) === null )
			{
				$msg = sprintf( 'Provider in "%1$s" not available', $providernames );
				throw new MShop_Service_Exception( $msg );
			}

			if ( ctype_alnum( $domain ) === false )
			{
				$context->getLogger()->log(
					sprintf( 'Invalid characters in domain name "%1$s"', $domain ), MW_Logger_Abstract::WARN
				);
				continue;
			}

			if ( ctype_alnum( $providername ) === false )
			{
				$context->getLogger()->log(
					sprintf( 'Invalid characters in provider name "%1$s"', $providername ), MW_Logger_Abstract::WARN
				);
				continue;
			}


			$classname = 'MShop_Plugin_Provider_' . ucfirst( $domain ) . '_' . $providername;
			$filename = 'MShop/Plugin/Provider/' . ucfirst( $domain ) . '/' . $providername . '.php';

			if ( class_exists( $classname ) === false )
			{
				$context->getLogger()->log(
					sprintf( 'Class "%1$s" not available', $classname ), MW_Logger_Abstract::WARN
				);
				continue;
			}

			$provider = new $classname( $context, $pluginItem );

			if ( ( $provider instanceof $interface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
				throw new MShop_Service_Exception( $msg );
			}


			$provider = $this->_addPluginDecorators( $pluginItem, $provider, $providernames );

			$config = $context->getConfig();
			$decorators = $config->get( 'mshop/plugin/provider/' . $pluginItem->getType() . '/decorators', array() );

			$provider = $this->_addPluginDecorators( $pluginItem, $provider, $decorators );


			$this->_plugins[$pluginItem->getId()] = $provider;
			$provider->register( $publisher );
		}
	}


	/**
	 * Returns plugin item specified by the given ID.
	 *
	 * @param integer $id Unique ID of the plugin item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Plugin_Item_Interface Returns the plugin item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'plugin.id', $id, $ref );
	}


	/**
	 * Saves a new or modified plugin to the storage.
	 *
	 * @param MShop_Common_Item_Interface $item Plugin item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Plugin_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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

			if( $id === null )
			{
				/** mshop/plugin/manager/default/item/insert
				 * Inserts a new plugin record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the plugin item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/plugin/manager/default/item/update
				 * @see mshop/plugin/manager/default/item/newid
				 * @see mshop/plugin/manager/default/item/delete
				 * @see mshop/plugin/manager/default/item/search
				 * @see mshop/plugin/manager/default/item/count
				 */
				$path = 'mshop/plugin/manager/default/item/insert';
			}
			else
			{
				/** mshop/plugin/manager/default/item/update
				 * Updates an existing plugin record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the plugin item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/plugin/manager/default/item/insert
				 * @see mshop/plugin/manager/default/item/newid
				 * @see mshop/plugin/manager/default/item/delete
				 * @see mshop/plugin/manager/default/item/search
				 * @see mshop/plugin/manager/default/item/count
				 */
				$path = 'mshop/plugin/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getProvider() );
			$stmt->bind( 5, json_encode( $item->getConfig() ) );
			$stmt->bind( 6, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, $date ); //mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); //ctime
			}

			$result = $stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/plugin/manager/default/item/newid
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_mplu_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mplu_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/plugin/manager/default/item/insert
				 * @see mshop/plugin/manager/default/item/update
				 * @see mshop/plugin/manager/default/item/delete
				 * @see mshop/plugin/manager/default/item/search
				 * @see mshop/plugin/manager/default/item/count
				 */
				$path = 'mshop/plugin/manager/default/item/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$this->_plugins[$id] = $item;

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
		/** mshop/plugin/manager/default/item/delete
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the plugin database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/plugin/manager/default/item/insert
		 * @see mshop/plugin/manager/default/item/update
		 * @see mshop/plugin/manager/default/item/newid
		 * @see mshop/plugin/manager/default/item/search
		 * @see mshop/plugin/manager/default/item/count
		 */
		$path = 'mshop/plugin/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searches for plugin items matching the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of plugin items implementing MShop_Service_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'plugin' );
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;

			/** mshop/plugin/manager/default/item/search
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the plugin
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * If the records that are retrieved should be ordered by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/plugin/manager/default/item/insert
			 * @see mshop/plugin/manager/default/item/update
			 * @see mshop/plugin/manager/default/item/newid
			 * @see mshop/plugin/manager/default/item/delete
			 * @see mshop/plugin/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/plugin/manager/default/item/search';

			/** mshop/plugin/manager/default/item/count
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the plugin
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/plugin/manager/default/item/insert
			 * @see mshop/plugin/manager/default/item/update
			 * @see mshop/plugin/manager/default/item/newid
			 * @see mshop/plugin/manager/default/item/delete
			 * @see mshop/plugin/manager/default/item/search
			 */
			$cfgPathCount =  'mshop/plugin/manager/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$config = $row['config'];
				if( ( $row['config'] = json_decode( $row['config'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'plugin.config', $row['id'], $config );
					$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
				}

				$map[ $row['id'] ] = $row;
				$typeIds[ $row['typeid'] ] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'plugin.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'plugin' );
	}


	/**
	 * Creates a criteria object for searching.
	 *
	 * @param boolean $default Prepopulate object with default criterias
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return parent::_createSearch('plugin');
		}

		return parent::createSearch();
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/plugin/manager/submanagers
		 * List of manager names that can be instantiated by the plugin manager
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
		$path = 'classes/plugin/manager/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array( 'type' ), $withsub );
	}


	/**
	 * Creates a new plugin object.
	 *
	 * @return MShop_Plugin_Item_Interface New plugin object
	 */
	public function _createItem( array $values = array() )
	{
		return new MShop_Plugin_Item_Default( $values );
	}


	/**
	 * Returns a new manager for plugin extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/plugin/manager/name
		 * Class name of the used plugin manager implementation
		 *
		 * Each default plugin manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Plugin_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Plugin_Manager_Myplugin
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/plugin/manager/name = Myplugin
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyPlugin"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/plugin/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the plugin manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the plugin manager.
		 *
		 *  mshop/plugin/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the plugin manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/decorators/global
		 * @see mshop/plugin/manager/decorators/local
		 */

		/** mshop/plugin/manager/decorators/global
		 * Adds a list of globally available decorators only to the plugin manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the plugin manager.
		 *
		 *  mshop/plugin/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the plugin controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/decorators/excludes
		 * @see mshop/plugin/manager/decorators/local
		 */

		/** mshop/plugin/manager/decorators/local
		 * Adds a list of local decorators only to the plugin manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the plugin manager.
		 *
		 *  mshop/plugin/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the plugin
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/plugin/manager/decorators/excludes
		 * @see mshop/plugin/manager/decorators/global
		 */

		return $this->_getSubManager( 'plugin', $manager, $name );
	}
}