<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds product stock test data.
 */
class MW_Setup_Task_MShopAddWarehouseData extends MW_Setup_Task_Abstract
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopAddLocaleData' );
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
		// executed by tasks in sub-directories for specific sites
		// $this->_process();
	}


	/**
	 * Adds product stock test data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding warehouse data', 0 );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'default' . $ds . 'data' . $ds . 'warehouse.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product stock domain', $path ) );
		}

		$manager = MShop_Product_Manager_Factory::createManager( $this->_additional );
		$warehouseManager = $manager->getSubManager( 'stock' )->getSubManager( 'warehouse' );

		$num = $total = 0;
		$item = $warehouseManager->createItem();

		foreach( $data['warehouse'] as $key => $dataset )
		{
			$total++;

			$item->setId( null );
			$item->setCode( $dataset['code'] );
			$item->setLabel( $dataset['label'] );
			$item->setStatus( $dataset['status'] );

			try {
				$warehouseManager->saveItem( $item );
				$num++;
			} catch( MW_DB_Exception $e ) { ; }
		}

		$this->_status( $num > 0 ? $num . '/' . $total : 'OK' );
	}
}