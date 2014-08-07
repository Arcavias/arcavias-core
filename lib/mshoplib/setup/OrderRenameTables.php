<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Move order_* tables to order_base_*.
 */
class MW_Setup_Task_OrderRenameTables extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_order_address' => array(
			'RENAME TABLE "mshop_order_address" TO "mshop_order_base_address"',
		),
		'mshop_order_discount' => array(
			'RENAME TABLE "mshop_order_discount" TO "mshop_order_base_discount"',
		),
		'mshop_order_product' => array(
			'RENAME TABLE "mshop_order_product" TO "mshop_order_base_product"',
		),
		'mshop_order_service' => array(
			'RENAME TABLE "mshop_order_service" TO "mshop_order_base_service"',
		),
		'mshop_order_service_attribute' => array(
			'RENAME TABLE "mshop_order_service_attribute" TO "mshop_order_base_service_attr"',
		),
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
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Renaming order tables', 0 ); $this->_status( '' );

		foreach( $stmts as $table => $stmtList )
		{
			$this->_msg( sprintf( 'Checking table "%1$s": ', $table ), 1 );

			if( $this->_schema->tableExists( $table ) === true )
			{
				$this->_executeList( $stmtList );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}
}
