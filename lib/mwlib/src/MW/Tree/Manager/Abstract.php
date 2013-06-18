<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 */


/**
 * Abstract tree manager class with basic methods.
 *
 * @package MW
 * @subpackage Tree
 */
abstract class MW_Tree_Manager_Abstract extends MW_Common_Manager_Abstract implements MW_Tree_Manager_Interface
{
	/**
	 * Returns only the requested node
	 */
	const LEVEL_ONE = 1;

	/**
	 * Returns the requested node and its children
	 */
	const LEVEL_LIST = 2;

	/**
	 * Returns all subnodes including the requested one
	 */
	const LEVEL_TREE = 3;


	private $_readOnly = false;


	/**
	 * Checks, whether a tree is read only.
	 *
	 * @return boolean True if tree is read-only, false if not
	 */
	public function isReadOnly()
	{
		return $this->_readOnly;
	}


	/**
	 * Sets this manager to read only.
	 *
	 * @param boolean $flag True if tree is read-only, false if not
	 */
	protected function _setReadOnly( $flag = true )
	{
		$this->_readOnly = (bool) $flag;
	}
}
