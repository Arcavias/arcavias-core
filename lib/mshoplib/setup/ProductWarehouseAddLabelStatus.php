<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds label and status to warehouse table.
 */
class MW_Setup_Task_ProductWarehouseAddLabelStatus extends MW_Setup_Task_Abstract
{
	private $_mysql = array(
		'mshop_product_warehouse' => array(
			'label'  => 'ALTER TABLE "mshop_product_warehouse" ADD "label" VARCHAR(255) NOT NULL',
			'status' => 'ALTER TABLE "mshop_product_warehouse" ADD "status" SMALLINT NOT NULL DEFAULT 0 AFTER label',
		),
	);

	private $_update = array(
		'mshop_product_warehouse' => array(
			'status' => 'UPDATE "mshop_product_warehouse" SET status = 1 WHERE label = \'\'',
			'label' => 'UPDATE "mshop_product_warehouse" SET label = code WHERE label = \'\'',
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
		$this->_msg(sprintf('Adding label and status columns for product warehouse'), 0);
		$this->_status( '' );

		foreach ($this->_mysql as $table => $columns) {

			if ($this->_schema->tableExists($table)) {

				foreach ($columns as $column => $stmt) {

					$this->_msg(sprintf('Checking column "%1$s.%2$s": ', $table, $column), 1);

					if (!$this->_schema->columnExists($table, $column)) {
						$this->_execute($stmt);
						$this->_status('added');
					} else {
						$this->_status('OK');
					}
				}
			}
		}


		foreach ($this->_update as $table => $columns) {

			if ($this->_schema->tableExists($table)) {

				foreach ($columns as $column => $stmt) {

					$this->_msg(sprintf('Update column "%1$s.%2$s": ', $table, $column), 1);

					if ($this->_schema->columnExists($table, $column)) {
						$this->_execute($stmt);
						$this->_status('updated');
					}
				}
			}
		}

	}
}