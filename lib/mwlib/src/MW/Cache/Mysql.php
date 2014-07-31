<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * MySQL database cache class.
 *
 * @package MW
 * @subpackage Cache
 */
class MW_Cache_Mysql
	extends MW_Cache_DB
	implements MW_Cache_Interface
{
	private $_sql;
	private $_dbm;
	private $_dbname;
	private $_siteid;


	/**
	 * Initializes the object instance.
	 *
	 * The config['search] array must contain these key/array pairs suitable for MW_Common_Criteria_Attribute_Default:
	 *	[cache.id] => Array containing the codes/types/labels for the unique ID
	 *	[cache.siteid] => Array containing the codes/types/labels for the site ID
	 *	[cache.value] => Array containing the codes/types/labels for the cached value
	 *	[cache.expire] => Array containing the codes/types/labels for the expiration date
	 *	[cache.tag.name] => Array containing the codes/types/labels for the tag name
	 *
	 * The config['sql] array must contain these statement:
	 *	[delete] =>
	 *		DELETE FROM cachetable WHERE siteid = ? AND :cond
	 *	[deletebytag] =>
	 *		DELETE FROM cachetable WHERE siteid = ? AND id IN (
	 *			SELECT tid FROM cachetagtable WHERE tsiteid = ? AND :cond
	 *		)
	 *	[get] =>
	 *		SELECT id, value, expire FROM cachetable WHERE siteid = ? AND :cond
	 *	[getbytag] =>
	 *		SELECT id, value, expire FROM cachetable
	 *		JOIN cachetagtable ON tid = id
	 *		WHERE siteid = ? AND tsiteid = ? AND :cond
	 *	[set] =>
	 *		INSERT INTO cachetable ( id, siteid, expire, value ) VALUES ( ?, ?, ?, ? ) ON DUPLICATE KEY UPDATE
	 *	[settag] =>
	 *		INSERT INTO cachetagtable ( tid, tsiteid, tname ) VALUES :tuples ON DUPLICATE KEY UPDATE
	 *
	 * For using a different database connection, the name of the database connection
	 * can be also given in the "config" parameter. In this case, use e.g.
	 *  config['dbname'] = 'db-cache'
	 *
	 * If a site ID is given, the cache is partitioned for different
	 * sites. This also includes access control so cached values can be only
	 * retrieved from the same site. Specify a site ID with
	 *  config['siteid'] = 123
	 *
	 * @param array $config Associative list with SQL statements, search attribute definitions and database name
	 * @param MW_DB_Manager_Interface $dbm Database manager
	 */
	public function __construct( array $config, MW_DB_Manager_Interface $dbm )
	{
		parent::__construct( $config, $dbm );

		$this->_dbname = ( isset( $config['dbname'] ) ? $config['dbname'] : 'db' );
		$this->_siteid = ( isset( $config['siteid'] ) ? $config['siteid'] : null );
		$this->_sql = $config['sql'];
		$this->_dbm = $dbm;
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @inheritDoc
	 *
	 * @param array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param array $tags Associative list of key/tag or key/tags pairs that should be
	 * 	associated to the values identified by their key. The value associated
	 * 	to the key can either be a tag string or an array of tag strings
	 * @param array $expires Associative list of key/datetime pairs.
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function setList( array $pairs, array $tags = array(), array $expires = array() )
	{
		$type = ( count( $pairs ) > 1 ? MW_DB_Connection_Abstract::TYPE_PREP : MW_DB_Connection_Abstract::TYPE_SIMPLE );
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$conn->begin();
			$stmt = $conn->create( $this->_sql['set'], $type );

			foreach( $pairs as $key => $value )
			{
				$date = ( isset( $expires[$key] ) ? $expires[$key] : null );

				$stmt->bind( 1, $key );
				$stmt->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 3, $date );
				$stmt->bind( 4, $value );
				$stmt->execute()->finish();

				if( isset( $tags[$key] ) )
				{
					$parts = array();
					$stmtTagPart = $conn->create( '( ?, ?, ? )' );

					foreach( (array) $tags[$key] as $name )
					{
						$stmtTagPart->bind( 1, $key );
						$stmtTagPart->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
						$stmtTagPart->bind( 3, $name );

						$parts[] = (string) $stmtTagPart;
					}

					if( !empty ( $parts ) )
					{
						$stmtTag = $conn->create( str_replace( ':tuples', join( ',', $parts ), $this->_sql['settag'] ) );
						$stmtTag->execute()->finish();
					}
				}
			}

			$conn->commit();
			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$conn->rollback();
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}
}
