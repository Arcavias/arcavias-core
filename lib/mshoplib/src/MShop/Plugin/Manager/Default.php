<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
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

	private $_typeSearchConfig = array(
		'plugin.type.id'=> array(
			'code'=>'plugin.type.id',
			'internalcode'=>'mpluty."id"',
			'internaldeps'=> array( 'LEFT JOIN "mshop_plugin_type" AS mpluty ON ( mpluty."id" = mplu."typeid" )' ),
			'label'=>'Plugin type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.siteid'=> array(
			'code'=>'plugin.type.siteid',
			'internalcode'=>'mpluty."siteid"',
			'label'=>'Plugin type site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'plugin.type.code' => array(
			'code'=>'plugin.type.code',
			'internalcode'=>'mpluty."code"',
			'label'=>'Plugin type code',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.domain' => array(
			'code'=>'plugin.type.domain',
			'internalcode'=>'mpluty."domain"',
			'label'=>'Plugin type domain',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.label' => array(
			'code'=>'plugin.type.label',
			'internalcode'=>'mpluty."label"',
			'label'=>'Plugin type label',
			'type'=> 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.status' => array(
			'code'=>'plugin.type.status',
			'internalcode'=>'mpluty."status"',
			'label'=>'Plugin type status',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'plugin.type.mtime'=> array(
			'code'=>'plugin.type.mtime',
			'internalcode'=>'mpluty."mtime"',
			'label'=>'Plugin type modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.ctime'=> array(
			'code'=>'plugin.type.ctime',
			'internalcode'=>'mpluty."ctime"',
			'label'=>'Plugin type creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'plugin.type.editor'=> array(
			'code'=>'plugin.type.editor',
			'internalcode'=>'mpluty."editor"',
			'label'=>'Plugin type editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


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
	 * @return MShop_Plugin_Item_Interface Plugin item
	 * @throws MShop_Plugin_Exception If plugin isn't found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'plugin.id', $id, $ref );
	}


	/**
	 * Saves a new or modified plugin to the storage.
	 *
	 * @param MShop_Plugin_Item_Interface $plugin Plugin item
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
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			$path = 'mshop/plugin/manager/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getProvider() );
			$stmt->bind( 5, json_encode( $item->getConfig() ) );
			$stmt->bind( 6, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, $date );//mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 10, $date );//ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/plugin/manager/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$this->_plugins[$id] = $item;

			$dbm->release($conn);
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Deletes a plugin from the storage.
	 *
	 * @param integer $id Unique ID of the plugin in the storage
	 */
	public function deleteItem( $id )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement( $conn, 'mshop/plugin/manager/default/item/delete' );
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute()->finish();

			if( isset( $this->_plugins[$id] ) ) {
				unset( $this->_plugins[$id] );
			}

			$dbm->release($conn);
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
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
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;
			$cfgPathSearch = 'mshop/plugin/manager/default/item/search';
			$cfgPathCount =  'mshop/plugin/manager/default/item/count';
			$required = array( 'plugin' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				if ( ( $row['config'] = json_decode($row['config'], true) ) === null ) {
					$msg = sprintf('Invalid JSON as search result: %1$s', $row['config']);
					throw new MShop_Service_Exception($msg);
				}

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
			$search->setConditions( $search->compare( '==', 'plugin.type.id', array_unique( $typeIds ) ) );
			$typeItems = $typeManager->searchItems( $search );

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
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$path = 'classes/plugin/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array('type') ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
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
		switch( $manager )
		{
			case 'type':
				return $this->_getTypeManager( 'plugin', $manager, $name, $this->_typeSearchConfig );
			default:
				return $this->_getSubManager( 'plugin', $manager, $name );
		}
	}

}
