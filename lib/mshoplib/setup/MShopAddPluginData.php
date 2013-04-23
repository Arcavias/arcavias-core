<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TablesAddDefaultData.php 14336 2011-12-14 14:38:50Z nsendetzky $
 */


/**
 * Adds default records plugin to table.
 */
class MW_Setup_Task_MShopAddPluginData extends MW_Setup_Task_Abstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array();
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
	}


	/**
	 * Adds locale data.
	 */
	protected function _process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->_additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->_msg( 'Adding default plugin data', 0 );
		$this->_status( '' );


		$ds = DIRECTORY_SEPARATOR;
		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $this->_additional, 'Default' );


		$filename = dirname( __FILE__ ) . $ds . 'default'.  $ds . 'data'. $ds . 'plugin.php';

		if( ( $data = include( $filename ) ) == false ) {
			throw new MW_Setup_Exception( sprintf( 'No data file "%1$s" found', $filename ) );
		}

		if( isset( $data['plugin'] ) ) {
			$this->_addPluginData( $pluginManager, $data['plugin'] );
		}
	}


	/**
	 * Adds plugin data.
	 *
	 * @param MShop_Common_Manager_Interface $pluginManager Plugin manager
	 * @param array $data Associative list of plugin data
	 */
	protected function _addPluginData( MShop_Common_Manager_Interface $pluginManager, array $data )
	{
		$this->_msg( 'Adding data for MShop plugins', 1 );

		$types = array();
		$manager = $pluginManager->getSubManager( 'type' );

		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
			$types[ 'plugin/' . $item->getCode() ] = $item;
		}

		$num = $total = 0;
		$item = $pluginManager->createItem();

		foreach( $data as $key => $dataset )
		{
			$total++;

			if( !isset( $types[ $dataset['typeid'] ] ) ) {
				throw new Exception( sprintf( 'No plugin type "%1$s" found', $dataset['typeid'] ) );
			}

			$item->setId( null );
			$item->setTypeId( $types[ $dataset['typeid'] ]->getId() );
			$item->setProvider( $dataset['provider'] );
			$item->setLabel( $dataset['label'] );
			$item->setConfig( $dataset['config'] );
			$item->setStatus( $dataset['status'] );

			try {
				$pluginManager->saveItem( $item );
				$num++;
			} catch( Exception $e ) { ; }
		}

		$this->_status( $num > 0 ? $num . '/' . $total : 'OK' );
	}
}