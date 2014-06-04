<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_catalog_index_price"
			WHERE :cond AND "siteid" = ?
		',
		'insert' => '
			INSERT INTO "mshop_catalog_index_price" (
				"prodid", "siteid", "priceid", "currencyid", "listtype",
				"type", "value", "costs", "rebate", "taxrate", "quantity",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'search' => '
			SELECT DISTINCT mpro."id"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpro."id"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
				LIMIT 1000 OFFSET 0
			) AS list
		',
	),
	'cleanup' => '
		DELETE FROM "mshop_catalog_index_price"
		WHERE "ctime" < ? AND "siteid" = ?
	',
);