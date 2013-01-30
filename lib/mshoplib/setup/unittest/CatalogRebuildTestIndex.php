<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: CatalogRebuildTestIndex.php 14251 2011-12-09 13:36:27Z nsendetzky $
 */


/**
 * Rebuilds the catalog index.
 */
class MW_Setup_Task_CatalogRebuildTestIndex extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'LocaleAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Rebuilds the catalog index.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}


		$this->_msg('Rebuilding catalog index for test data', 0);

		$catalogIndexManager = MShop_Catalog_Manager_Factory::createManager( $this->_additional )->getSubManager( 'index' );

		$catalogIndexManager->rebuildIndex();
		$catalogIndexManager->optimize();

		$this->_status( 'done' );
	}
}
