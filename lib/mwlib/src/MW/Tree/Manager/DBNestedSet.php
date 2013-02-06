<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 * @version $Id: DBNestedSet.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Tree manager using nested sets stored in a database.
 *
 * @package MW
 * @subpackage Tree
 */
class MW_Tree_Manager_DBNestedSet extends MW_Tree_Manager_Abstract
{
	private $_searchConfig = array();
	private $_dbm;
	private $_config;


	/**
	 * Initializes the tree manager.
	 *
	 * The config['search] array must contain these key/array pairs suitable for MW_Common_Criteria_Attribute_Default:
	 *	[id] => Array describing unique ID codes/types/labels
	 *	[label] => Array describing codes/types/labels for descriptive labels
	 *	[status] => Array describing codes/types/labels for status values
	 *	[level] => Array describing codes/types/labels for height levels of tree nodes
	 *	[left] => Array describing codes/types/labels for nodes left values
	 *	[right] => Array describing codes/types/labels for nodes right values
	 *
	 * The config['sql] array must contain these statement:
	 *	[delete] =>
	 *		DELETE FROM treetable WHERE type = <type> AND left >= ? AND right <= ?
	 *	[get] =>
	 *		SELECT node.*
	 *		FROM treetable AS parent, treetable AS node
	 *		WHERE node.left >= parent.left AND node.left <= parent.right
	 *		AND parent.id = ? AND node.level <= parent.level + ?
	 *		AND type = <type> AND :cond
	 *		ORDER BY node.left
	 *	[insert] =>
	 *		INSERT INTO treetable ( type, label, code, level, left, right ) VALUES ( <type>, ?, ?, ?, ?, ? )
	 *	[move-left] =>
	 *		UPDATE treetable
	 *		SET left = left + ?, level = level + ?
	 *		WHERE type = <type> AND left >= ? AND left <= ?
	 *	[move-right] =>
	 *		UPDATE treetable
	 *		SET right = right + ?
	 *		WHERE type = <type> AND right >= ? AND right <= ?
	 *	[search] =>
	 *		SELECT * FROM treetable
	 *		WHERE type = <type> AND left >= ? AND right <= ? AND :cond
	 *		ORDER BY :order
	 *	[update] =>
	 *		UPDATE treetable SET label = ?, code = ? WHERE type = <type> AND id = ?
	 *	[newid] =>
	 *		SELECT LAST_INSERT_ID()
	 *
	 * @param array $config Associative array holding the SQL statements
	 * @param MW_DB_Manager_Interface $resource Database manager
	 */
	public function __construct( array $config, $resource )
	{
		if( !( $resource instanceof MW_DB_Manager_Interface ) ) {
			throw new MW_Tree_Exception( 'Given resource isn\'t a database manager object' );
		}

		if( !isset( $config['search'] ) ) {
			throw new MW_Tree_Exception( 'Search config is missing' );
		}

		if( !isset( $config['sql'] ) ) {
			throw new MW_Tree_Exception( 'SQL config is missing' );
		}

		$this->_checkSearchConfig( $config['search'] );
		$this->_checkSqlConfig( $config['sql'] );

		$this->_searchConfig = $config['search'];
		$this->_config = $config['sql'];
		$this->_dbm = $resource;
	}


	/**
	 * Returns a list of attributes which can be used in the search method.
	 *
	 * @return array List of search attribute objects implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes()
	{
		$attributes = array();

		foreach( $this->_searchConfig as $values ) {
			$attributes[] = new MW_Common_Criteria_Attribute_Default( $values );
		}

		return $attributes;
	}


	/**
	 * Creates a new search object for storing search criterias.
	 *
	 * @return MW_Common_Criteria_Interface Search object instance
	 */
	public function createSearch()
	{
		$conn = $this->_dbm->acquire();
		$search = new MW_Common_Criteria_SQL( $conn );
		$this->_dbm->release( $conn );

		return $search;
	}


	/**
	 * Creates a new node object.
	 *
	 * @return MW_Tree_Node_Interface Empty node object
	 */
	public function createNode()
	{
		return $this->_createNode();
	}


	/**
	 * Deletes a node and its descendants from the storage.
	 *
	 * @param integer|null $id Delete the node with the ID and all nodes below
	 */
	public function deleteNode( $id = null )
	{
		$node = $this->getNode( $id, MW_Tree_Manager_Abstract::LEVEL_ONE );

		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( $this->_config['delete'] );
			$stmt->bind( 1, $node->left, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $node->right, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$diff = $node->right - $node->left + 1;

			$stmt = $conn->create( $this->_config['move-left'] );
			$stmt->bind( 1, -$diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $node->right + 1, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$stmt = $conn->create( $this->_config['move-right'] );
			$stmt->bind( 1, -$diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $node->right + 1, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$this->_dbm->release($conn);
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns a node and its descendants depending on the given resource.
	 *
	 * @param integer|null $id Retrieve nodes starting from the given ID
	 * @param integer $level One of the level constants from MW_Tree_Manager_Abstract
	 * @param MW_Common_Criteria_Interface|null $criteria Optional criteria object with conditions
	 * @return MW_Tree_Node_Interface Node, maybe with subnodes
	 */
	public function getNode( $id = null, $level = MW_Tree_Manager_Abstract::LEVEL_TREE, MW_Common_Criteria_Interface $condition = null )
	{
		if( $id === null )
		{
			if( ( $node = $this->_getRootNode() ) === null ) {
				throw new MW_Tree_Exception( 'No root node available' );
			}

			if( $level === MW_Tree_Manager_Abstract::LEVEL_ONE ) {
				return $node;
			}
		}
		else
		{
			$node = $this->_getNodeById( $id );

			if( $level === MW_Tree_Manager_Abstract::LEVEL_ONE ) {
				return $node;
			}
		}


		$id = $node->getId();

		$numlevel = $this->_getLevelFromConstant( $level );
		$search = $this->createSearch();

		if( $condition !== null )
		{
			$expr = array(
				$search->getConditions(),
				$condition->getConditions()
			);
			$search->setConditions( $search->combine('&&', $expr) );
		}

		$types = $this->_getSearchTypes( $this->_searchConfig );
		$translations = $this->_getSearchTranslations( $this->_searchConfig );
		$conditions = $search->getConditionString( $types, $translations );


		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_config['get'] ) );
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $numlevel, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) === false ) {
				throw new MW_Tree_Exception( sprintf( 'No node with ID "%1$d" found for base "%2$s"', $id, $base ) );
			}

			$node = $this->_createNode( $row );
			$this->_createTree( $result, $node );

			$this->_dbm->release($conn);
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}

		return $node;
	}


	/**
	 * Inserts a new node before the given reference node to the parent in the storage.
	 *
	 * @param MW_Tree_Node_Interface $node New node that should be inserted
	 * @param mixed $parentId ID of the parent node where the new node should be inserted below (null for root node)
	 * @param mixed $refId ID of the node where the node should be inserted before (null to append)
	 */
	public function insertNode( MW_Tree_Node_Interface $node, $parentId = null, $refId = null )
	{
		$base = null;
		$node->parentid = $parentId;

		if( $refId !== null )
		{
			$refNode = $this->getNode( $refId, MW_Tree_Manager_Abstract::LEVEL_ONE );
			$node->left = $refNode->left;
			$node->right = $refNode->left + 1;
			$node->level = $refNode->level;
		}
		else if( $parentId !== null )
		{
			$parentNode = $this->getNode( $parentId, MW_Tree_Manager_Abstract::LEVEL_ONE );
			$node->left = $parentNode->right;
			$node->right = $parentNode->right + 1;
			$node->level = $parentNode->level + 1;
		}
		else
		{
			$node->left = 1;
			$node->right = 2;
			$node->level = 0;
			$node->parentid = 0;

			if( ( $root = $this->_getRootNode( '-' ) ) !== null )
			{
				$node->left = $root->right + 1;
				$node->right = $root->right + 2;
			}
		}


		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( $this->_config['move-left'] );
			$stmt->bind( 1, 2, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $node->left, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 4, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$stmt = $conn->create( $this->_config['move-right'] );
			$stmt->bind( 1, 2, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $node->left, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$stmt = $conn->create( $this->_config['insert'] );
			$stmt->bind( 1, $node->getLabel(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 2, $node->getCode(), MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 3, $node->getStatus(), MW_DB_Statement_Abstract::PARAM_BOOL );
			$stmt->bind( 4, (int) $node->parentid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $node->level, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 6, $node->left, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $node->right, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();


			$result = $conn->create( $this->_config['newid'] )->execute();

			if( ( $row = $result->fetch( MW_DB_Result_Abstract::FETCH_NUM ) ) === false ) {
				throw new MW_Tree_Exception( sprintf( 'No new record ID available' ) );
			}
			$result->finish();

			$node->setId( $row[0] );

			$this->_dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Moves an existing node to the new parent in the storage.
	 *
	 * @param mixed $id ID of the node that should be moved
	 * @param mixed $oldParentId ID of the old parent node which currently contains the node that should be removed
	 * @param mixed $newParentId ID of the new parent node where the node should be moved to
	 * @param mixed $newRefId ID of the node where the node should be inserted before (null to append)
	 */
	public function moveNode( $id, $oldParentId, $newParentId, $newRefId = null )
	{
		$node = $this->getNode( $id, MW_Tree_Manager_Abstract::LEVEL_ONE );
		$diff = $node->right - $node->left + 1;

		if( $newRefId !== null )
		{
			$refNode = $this->getNode( $newRefId, MW_Tree_Manager_Abstract::LEVEL_ONE );

			$leveldiff = $refNode->level - $node->level;

			$openNodeLeftBegin = $refNode->left;
			$openNodeRightBegin = $refNode->left + 1;

			if( $refNode->left < $node->left )
			{
				$moveNodeLeftBegin = $node->left + $diff;
				$moveNodeLeftEnd = $node->right + $diff - 1;
				$moveNodeRightBegin = $node->left + $diff + 1;
				$moveNodeRightEnd = $node->right + $diff;
				$movesize = $refNode->left - $node->left - $diff;
			}
			else
			{
				$moveNodeLeftBegin = $node->left;
				$moveNodeLeftEnd = $node->right - 1;
				$moveNodeRightBegin = $node->left + 1;
				$moveNodeRightEnd = $node->right;
				$movesize = $refNode->left - $node->left;
			}

			$closeNodeLeftBegin = $node->left + $diff;
			$closeNodeRightBegin = $node->left + $diff;
		}
		else
		{
			$refNode = $this->getNode( $newParentId, MW_Tree_Manager_Abstract::LEVEL_ONE );

			$leveldiff = $refNode->level - $node->level + 1;

			$openNodeLeftBegin = $refNode->right + 1;
			$openNodeRightBegin = $refNode->right;

			if( $refNode->right < $node->right )
			{
				$moveNodeLeftBegin = $node->left + $diff;
				$moveNodeLeftEnd = $node->right + $diff - 1;
				$moveNodeRightBegin = $node->left + $diff + 1;
				$moveNodeRightEnd = $node->right + $diff;
				$movesize = $refNode->right - $node->left - $diff;
			}
			else
			{
				$moveNodeLeftBegin = $node->left;
				$moveNodeLeftEnd = $node->right - 1;
				$moveNodeRightBegin = $node->left + 1;
				$moveNodeRightEnd = $node->right;
				$movesize = $refNode->right - $node->left;
			}

			$closeNodeLeftBegin = $node->left + $diff;
			$closeNodeRightBegin = $node->left + $diff;
		}


		$conn = $this->_dbm->acquire();

		try
		{
			$stmtLeft = $conn->create( $this->_config['move-left'], MW_DB_Connection_Abstract::TYPE_PREP );
			$stmtRight = $conn->create( $this->_config['move-right'], MW_DB_Connection_Abstract::TYPE_PREP );

			// open gap for inserting node or subtree

			$stmtLeft->bind( 1, $diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 2, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 3, $openNodeLeftBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 4, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->execute()->finish();

			$stmtRight->bind( 1, $diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 2, $openNodeRightBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 3, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->execute()->finish();

			// move node or subtree to the new gap

			$stmtLeft->bind( 1, $movesize, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 2, $leveldiff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 3, $moveNodeLeftBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 4, $moveNodeLeftEnd, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->execute()->finish();

			$stmtRight->bind( 1, $movesize, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 2, $moveNodeRightBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 3, $moveNodeRightEnd, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->execute()->finish();

			// close gap opened by moving the node or subtree to the new location

			$stmtLeft->bind( 1, -$diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 2, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 3, $closeNodeLeftBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->bind( 4, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtLeft->execute()->finish();

			$stmtRight->bind( 1, -$diff, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 2, $closeNodeRightBegin, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->bind( 3, 0x7FFFFFFF, MW_DB_Statement_Abstract::PARAM_INT );
			$stmtRight->execute()->finish();

			$this->_dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Stores the values of the given node to the storage.
	 *
	 * This method does only store values like the node label but doesn't change
	 * the tree layout by adding, moving or deleting nodes.
	 *
	 * @param MW_Tree_Node_Interface $node Tree node object
	 */
	public function saveNode( MW_Tree_Node_Interface $node )
	{
		if( $node->getId() === null ) {
			throw new MW_Tree_Exception( sprintf( 'Unable to save newly created nodes, use insert method instead' ) );
		}

		if( $node->isModified() === false ) {
			return;
		}

		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( $this->_config['update'] );
			$stmt->bind( 1, $node->getLabel() );
			$stmt->bind( 2, $node->getCode() );
			$stmt->bind( 3, $node->getStatus() );
			$stmt->bind( 4, $node->getId() );
			$result = $stmt->execute()->finish();

			$this->_dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Retrieves a list of nodes from the storage matching the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer|null $id Search nodes starting at the node with the given ID
	 * @return array List of nodes implementing MW_Tree_Node_Interface
	 */
	public function searchNodes( MW_Common_Criteria_Interface $search, $id = null )
	{
		$left =  1;
		$right = 0x7FFFFFFF;

		if( $id !== null )
		{
			$node = $this->_getNodeById( $id );

			$left =  $node->left;
			$right = $node->right;
		}

		$types = $this->_getSearchTypes( $this->_searchConfig );
		$translations = $this->_getSearchTranslations( $this->_searchConfig );
		$conditions = $search->getConditionString( $types, $translations );
		$sortations = $search->getSortationString( $types, $translations );

		$sql = str_replace(
			array( ':cond', ':order' ),
			array( $conditions, $sortations ),
			$this->_config['search']
		);

		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $left, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $right, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			try
			{
				$nodes = array();
				while( ( $row = $result->fetch() ) !== false ) {
					$nodes[$row['id']] = $this->_createNode( $row );
				}
			}
			catch( Exception $e )
			{
				$result->finish();
				throw $e;
			}

			$this->_dbm->release($conn);
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}

		return $nodes;
	}


	/**
	 * Returns a list if node IDs, that are in the path of given node ID.
	 *
	 * @param integer $id ID of node to get the path for
	 * @return array Associative list of nodes implementing MW_Tree_Node_Interface with IDs as keys
	 */
	public function getPath( $id )
	{
		$result = array();
		$node = $this->getNode( $id, MW_Tree_Manager_Abstract::LEVEL_ONE );

		$search = $this->createSearch();

		$expr[] = $search->compare( '<=', $this->_searchConfig['left']['code'], $node->left );
		$expr[] = $search->compare( '>=', $this->_searchConfig['right']['code'], $node->right );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', $this->_searchConfig['left']['code'] ) ) );

		$results = $this->searchNodes( $search );

		foreach ( $results as $item ) {
			$result[$item->getId()] = $item;
		}

		return $result;
	}


	/**
	 * Checks if all required search configurations are available.
	 *
	 * @param array $config Associative list of search configurations
	 * @throws MW_Tree_Exception If one ore more search configurations are missing
	 */
	protected function _checkSearchConfig( array $config )
	{
		$required = array( 'id', 'label', 'status', 'level', 'left', 'right' );

		foreach( $required as $key => $entry )
		{
			if( isset( $config[$entry] ) ) {
				unset( $required[$key] );
			}
		}

		if( count( $required ) > 0 )
		{
			$msg = 'Search config in given configuration are missing: "%1$s"';
			throw new MW_Tree_Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}


	/**
	 * Checks if all required SQL statements are available.
	 *
	 * @param array $config Associative list of SQL statements
	 * @throws MW_Tree_Exception If one ore more SQL statements are missing
	 */
	protected function _checkSqlConfig( array $config )
	{
		$required = array(
			'delete', 'get', 'insert', 'move-left',
			'move-right', 'search', 'update', 'newid'
		);

		foreach( $required as $key => $entry )
		{
			if( isset( $config[$entry] ) ) {
				unset( $required[$key] );
			}
		}

		if( count( $required ) > 0 )
		{
			$msg = 'SQL statements in given configuration are missing: "%1$s"';
			throw new MW_Tree_Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}


	/**
	 * Creates a new node object.
	 *
	 * @param array List of attributes that should be stored in the new node
	 * @param array List of children implementing MW_Tree_Node_Interface
	 * @return MW_Tree_Node_Interface Empty node object
	 */
	protected function _createNode( array $values = array(), array $children = array() )
	{
		return new MW_Tree_Node_DBNestedSet( $values, $children );
	}


	/**
	 * Creates a tree from the result set returned by the database.
	 *
	 * @param MW_DB_Result_Interface $result Database result
	 * @param MW_Tree_Node_Interface $node Current node to add children to
	 * @return MW_Tree_Node_Interface Parent node containing the children
	 */
	protected function _createTree( MW_DB_Result_Interface $result, MW_Tree_Node_Interface $node )
	{
		while( ( $record = $result->fetch() ) !== false )
		{
			$newNode = $this->_createNode( $record );

			while( $this->_isChild( $newNode, $node ) )
			{
				$node->addChild( $newNode );

				if( ( $newNode = $this->_createTree( $result, $newNode ) ) === false ) {
					return false;
				}
			}

			return $newNode;
		}

		return false;
	}


	/**
	 * Tests if the first node is a child of the second node.
	 *
	 * @param MW_Tree_Node_Interface $node Node to test
	 * @param MW_Tree_Node_Interface $parent Parent node
	 * @return boolean True if not is a child of the second node, false if not
	 */
	protected function _isChild( MW_Tree_Node_Interface $node, MW_Tree_Node_Interface $parent )
	{
		return $node->__get('left') > $parent->__get('left') && $node->__get('right') < $parent->__get('right');
	}


	/**
	 * Converts the level constant to the depth of the tree.
	 *
	 * @param integer $level Level constant from MW_Tree_Manager_Abstract
	 * @throws MW_Tree_Exception if level constant is invalid
	 */
	protected function _getLevelFromConstant( $level )
	{
		switch( $level )
		{
			case MW_Tree_Manager_Abstract::LEVEL_ONE:
				return 0;
			case MW_Tree_Manager_Abstract::LEVEL_LIST:
				return 1;
			case MW_Tree_Manager_Abstract::LEVEL_TREE:
				return 0x7FFFFFFF; // max. possible level
			default:
				throw new MW_Tree_Exception( sprintf( 'Invalid level constant "%1$d"', $level ) );
		}
	}


	/**
	 * Returns a single node identified by its ID.
	 *
	 * @param string $id Unique ID
	 * @throws MW_Tree_Exception If node is not found
	 * @throws Exception If anything unexcepted occurs
	 * @return MW_Tree_Node_Interface Tree node
	 */
	protected function _getNodeById( $id )
	{
		$conn = $this->_dbm->acquire();

		try
		{
			$stmt = $conn->create( str_replace( ':cond', '1=1', $this->_config['get'] ) );
			$stmt->bind( 1, $id, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, 0, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) === false ) {
				throw new MW_Tree_Exception( sprintf( 'No node with ID "%1$d" found', $id ) );
			}

			$node = $this->_createNode( $row );

			$this->_dbm->release($conn);
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn );
			throw $e;
		}

		return $node;
	}


	/**
	 * Returns the first tree root node depending on the sorting direction.
	 *
	 * @param string $sort Sort direction, '+' is ascending, '-' is descending
	 * @return MW_Tree_Node_Interface|null Tree root node
	 */
	protected function _getRootNode( $sort = '+' )
	{
		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', $this->_searchConfig['level']['code'], 0 ) );
		$search->setSortations( array( $search->sort( $sort, $this->_searchConfig['left']['code'] ) ) );
		$nodes = $this->searchNodes( $search );

		if( ( $node = reset( $nodes ) ) !== false ) {
			return $node;
		}

		return null;
	}
}
