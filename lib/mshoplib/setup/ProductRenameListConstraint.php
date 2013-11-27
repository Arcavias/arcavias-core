<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames the mshop_service_list.parentid constraint.
 */
class MW_Setup_Task_ProductRenameListConstraint extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'fk_msproli_parentid' => '
			ALTER TABLE "mshop_product_list" DROP FOREIGN KEY "fk_msproli_parentid",
			ADD CONSTRAINT "fk_msproli_pid" FOREIGN KEY ("parentid")
			REFERENCES "mshop_product" ("id")
			ON DELETE CASCADE ON UPDATE CASCADE
		',
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
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process( $this->_mysql );
	}


	/**
	 * Adds and modifies indexes in the mshop_service tables.
	 *
	 * @param array $stmts List of SQL statements to execute for adding columns
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( sprintf( 'Renameing product list constraint' ), 0 );
		$this->_status('');

		$table = 'mshop_product_list';

		foreach ( $stmts AS $name => $stmt )
		{
			$this->_msg(sprintf('Checking constraint "%1$s": ', $name), 1);

			if( $this->_schema->tableExists( $table ) === true
				&& $this->_schema->constraintExists( $table, $name ) === true )
			{
				$this->_execute( $stmt );
				$this->_status( 'renamed' );
			}
			else
			{
				$this->_status( 'OK' );
			}
		}
	}

}