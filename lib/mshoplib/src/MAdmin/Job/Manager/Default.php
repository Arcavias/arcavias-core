<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Job
 * @version $Id: Default.php 14711 2012-01-05 12:52:13Z nsendetzky $
 */


/**
 * Default job manager implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class MAdmin_Job_Manager_Default
	extends MAdmin_Common_Manager_Abstract
	implements MAdmin_Job_Manager_Interface
{
	private $_searchConfig = array(
		'job.id'=> array(
			'code'=>'job.id',
			'internalcode'=>'majob."id"',
			'label'=>'Job ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.siteid'=> array(
			'code'=>'job.siteid',
			'internalcode'=>'majob."siteid"',
			'label'=>'Job site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.label'=> array(
			'code'=>'job.label',
			'internalcode'=>'majob."label"',
			'label'=>'Job label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.method'=> array(
			'code'=>'job.method',
			'internalcode'=>'majob."method"',
			'label'=>'Job method',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.parameter'=> array(
			'code'=>'job.parameter',
			'internalcode'=>'majob."parameter"',
			'label'=>'Job parameter',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.result'=> array(
			'code'=>'job.result',
			'internalcode'=>'majob."result"',
			'label'=>'Job result',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.status'=> array(
			'code'=>'job.status',
			'internalcode'=>'majob."status"',
			'label'=>'Job status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.ctime'=> array(
			'code'=>'job.ctime',
			'internalcode'=>'majob."ctime"',
			'label'=>'Job create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.mtime'=> array(
			'code'=>'job.mtime',
			'internalcode'=>'majob."mtime"',
			'label'=>'Job modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.editor'=> array(
			'code'=>'job.editor',
			'internalcode'=>'majob."editor"',
			'label'=>'Job editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Create new job item object.
	 *
	 * @return MAdmin_Job_Item_Interface
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->_createSearch( 'job' );
		}

		return parent::createSearch();
	}


	/**
	 * Adds a new job to the storage.
	 *
	 * @param MAdmin_Job_Item_Interface $item Job item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MAdmin_Job_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MAdmin_Job_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) {
			return;
		}

		$context = $this->_getContext();
		$config = $context->getConfig();
		$locale = $context->getLocale();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			$path = 'madmin/job/manager/default/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $conn->create( $config->get( $path, $path ) );
			$stmt->bind( 1, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getMethod() );
			$stmt->bind( 4, json_encode( $item->getParameter() ) );
			$stmt->bind( 5, json_encode( $item->getResult() ) );
			$stmt->bind( 6, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $context->getEditor() );
			$stmt->bind( 8, date( 'Y-m-d H:i:s', time() ) );

			if( $id !== null ) {
				$stmt->bind( 9, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$stmt->bind( 9, date('Y-m-d H:i:s', time()) );
			}

			$stmt->execute()->finish();

			if ( $fetch === true )
			{
				if( $id === null )
				{
					$path = 'madmin/job/manager/default/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId( $id ); // so item is no longer modified
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
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( 'madmin/job/manager/default/delete', 'madmin/job/manager/default/delete' ) );
	}


	/**
	 * Creates the job object for the given job id.
	 *
	 * @param integer $id Job ID to fetch job object for
	 * @return MAdmin_Job_Item_Interface
	 */
	public function getItem( $id, array $ref = array() )
	{
		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'job.id', $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MAdmin_Job_Exception( sprintf( 'Job with ID "%1$s" not found', $id ) );
		}

		return $item;
	}


	/**
	 * Search for jobs based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of jobs implementing MAdmin_Job_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$logger = $context->getLogger();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();
		$items = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'madmin/job/manager/default/search';
			$cfgPathCount =  'madmin/job/manager/default/count';
			$required = array( 'job' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$config = $row['parameter'];
				if( ( $row['parameter'] = json_decode( $row['parameter'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'madmin_job.parameter', $row['id'], $config );
					$logger->log( $msg, MW_Logger_Abstract::WARN );
				}

				$config = $row['result'];
				if( ( $row['result'] = json_decode( $row['result'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'madmin_job.result', $row['id'], $config );
					$logger->log( $msg, MW_Logger_Abstract::WARN );
				}

				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		return $items;
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
			$path = 'classes/job/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for job extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'job', $manager, $name );
	}


	/**
	 * Create new admin job item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return MAdmin_Job_Item_Interface
	 */
	protected function _createItem( array $values = array() )
	{
		return new MAdmin_Job_Item_Default( $values );
	}


	/**
	 * Returns the search result object for the given SQL statement.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $sql SQL-statement to execute
	 * @return MW_DB_Result_Interface Returns db result set from given sql statment
	 */
	protected function _getSearchResults( MW_DB_Connection_Interface $conn, $sql )
	{
		$context = $this->_getContext();
		$siteId = $context->getLocale()->getSiteId();

		$statement = $conn->create($sql);
		$statement->bind(1, $siteId, MW_DB_Statement_Abstract::PARAM_INT);

		$context->getLogger()->log( __METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG );

		return $statement->execute();
	}
}
