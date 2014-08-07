<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds type/code column to catalog index attribute table.
 */
class MW_Setup_Task_CatalogAddIndexTypeCode extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'type' => array(
			'ALTER TABLE "mshop_catalog_index_attribute" ADD "type" VARCHAR(32) NOT NULL AFTER "listtype"',
		),
		'code' => array(
			'ALTER TABLE "mshop_catalog_index_attribute" ADD "code" VARCHAR(32) NOT NULL AFTER "type"',
		)
	);
	
	
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
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array('TablesCreateMShop');
	}
	
	
	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}
	
	/**
	 * Add column to table if it doesn't exist.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( $stmts )
	{
		$this->_msg( 'Adding reference ID columns to catalog index tables', 0 );
		$this->_status( '' );
	
		if( $this->_schema->tableExists( 'mshop_catalog_index_attribute' ) === true )
		{
			foreach( $stmts as $id => $sql )
			{
				$this->_msg( sprintf( 'Checking table "%1$s" for column "%2$s"', 'mshop_catalog_index_attribute', $id ), 1 );
				
				if( $this->_schema->columnExists( 'mshop_catalog_index_attribute', $id ) === false )
				{
					$this->_executeList( $sql );
					$this->_status( 'added' );
				} 
				else 
				{
					$this->_status( 'OK' );
				}
			}
		}
		else
		{
			$this->_status( 'OK' );
		}
	}
}