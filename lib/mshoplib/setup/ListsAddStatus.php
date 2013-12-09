<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds status column to list tables and sets it to enable.
 */
class MW_Setup_Task_ListsAddStatus extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_attribute_list' => 'ALTER TABLE "mshop_attribute_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_catalog_list' => 'ALTER TABLE "mshop_catalog_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_customer_list' => 'ALTER TABLE "mshop_customer_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_media_list' => 'ALTER TABLE "mshop_media_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_price_list' => 'ALTER TABLE "mshop_price_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_product_list' => 'ALTER TABLE "mshop_product_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_service_list' => 'ALTER TABLE "mshop_service_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
		'mshop_text_list' => 'ALTER TABLE "mshop_text_list" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER "pos"',
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
	 * @return array List of task names
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
	 * Add column status to mshop_*_list tables.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Adding status column to all list tables', 0 );
		$this->_status( '' );

		foreach( $stmts as $table => $stmt )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->columnExists( $table, 'status' ) === false )
			{
				$this->_execute( $stmt );
				$this->_status( 'added' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}