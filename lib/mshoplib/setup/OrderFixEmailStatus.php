<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Fixes the old email flag values migrated from the order table.
 */
class MW_Setup_Task_OrderFixEmailStatus extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'update' => '
			UPDATE "mshop_order_status"
			SET "siteid"=(SELECT "siteid" FROM "mshop_order" mord WHERE mord."id"="parentid" LIMIT 1 ),
				"ctime"=NOW(), "mtime"=NOW(), "editor"=\'setup task\'
			WHERE "siteid" IS NULL
		',
		'change' => '
			UPDATE "mshop_order_status"
			SET "type"=\'email-delivery\', "value"=?
			WHERE "type"=?
		',
	);


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'OrderMigrateEmailflag' );
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
	 * Migrates the emailflag status values in order table to the order status table.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg( 'Fixing order email status values', 0 );

		if( $this->_schema->tableExists( 'mshop_order_status' ) === true )
		{
			$this->_execute( $stmts['update'] );

			$mapping = array(
				0 => 'email-deleted',
				1 => 'email-pending',
				2 => 'email-progress',
				3 => 'email-dispatched',
				4 => 'email-delivered',
				5 => 'email-lost',
				6 => 'email-refused',
				7 => 'email-returned',
			);

			$cntRows = 0;
			foreach( $mapping as $value => $type )
			{
				$stmt = $this->_conn->create( $stmts['change'] );
				$stmt->bind( 1, $value, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 2, $type );

				$result = $stmt->execute();
				$cntRows += $result->affectedRows();
				$result->finish();
			}

			if( $cntRows > 0 ) {
				$this->_status( sprintf( 'migrated (%1$d)', $cntRows ) );
			} else {
				$this->_status( 'OK' );
			}
		}
		else
		{
			$this->_status( 'OK' );
		}

	}

}
