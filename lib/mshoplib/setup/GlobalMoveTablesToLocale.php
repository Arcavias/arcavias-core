<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Renames global tables to locale.
 */
class MW_Setup_Task_GlobalMoveTablesToLocale extends MW_Setup_Task_Abstract
{

	private $_mysql = array(
		'mshop_global_currency' => array(
			'RENAME TABLE "mshop_global_currency" TO "mshop_locale_currency"',
		),
		'mshop_global_language' => array(
			'RENAME TABLE "mshop_global_language" TO "mshop_locale_language"',
		),
		'mshop_global_site' => array(
			'RENAME TABLE "mshop_global_site" TO "mshop_locale_site"',
			'ALTER TABLE "mshop_locale_site" DROP INDEX "unq_msglsite_code"',
			'ALTER TABLE "mshop_locale_site"
				ADD CONSTRAINT "unq_mslocsi_code" UNIQUE ("code")',
		),
	);

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array(
			'CatalogTreeToCatalog',
			'DiscountAddForeignKey',
			'MediaAddForeignKey',
			'OrderAddForeignKey',
			'OrderAddSiteId',
			'ProductHousingAddSiteid',
			'StatusToSmallInt',
			'TextAddForeignKey'
		);
	}

	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
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
		$this->_process($this->_mysql);
	}

	/**
	 * Renames all order tables if they exist.
	 *
	 * @param array $stmts Associative array of tables names and lists of SQL statements to execute.
	 */
	protected function _process( array $stmts )
	{
		$this->_msg('Renaming global tables', 0);
		$this->_status('');

		foreach ( $stmts as $table => $stmtList )
		{
			if ( $this->_schema->tableExists($table) )
			{
				$this->_msg(sprintf('Changing table "%1$s": ', $table), 1);
				$this->_executeList($stmtList);
				$this->_status('Ok');
			}
		}
	}

}
