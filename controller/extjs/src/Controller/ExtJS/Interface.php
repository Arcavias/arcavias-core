<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Interface.php 14265 2011-12-11 16:57:33Z nsendetzky $
 */


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Interface
{
	/**
	 * Deletes a list of an items.
	 *
	 * @param stdClass $params Associative array containing the required values
	 */
	public function deleteItems( stdClass $params );

	/**
	 * Creates a new item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the required values
	 */
	public function saveItems( stdClass $params );

	/**
	 * Retrieves all items matching the given criteria.
	 *
	 * @param stdClass $params Associative array containing the parameters
	 * @return array List of associative arrays with item properties and total number of items
	 */
	public function searchItems( stdClass $params );

	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription();

	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema();

	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema();
}
