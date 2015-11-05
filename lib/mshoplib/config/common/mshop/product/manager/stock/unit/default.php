<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_product_stock_unit"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_product_stock_unit" (
				"siteid", "code", "label", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_product_stock_unit"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mprostun."id", mprostun."siteid", mprostun."code",
				mprostun."label", mprostun."status", mprostun."mtime",
				mprostun."editor", mprostun."ctime"
			FROM "mshop_product_stock_unit" AS mprostun
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprostun."id"
				FROM "mshop_product_stock_unit" AS mprostun
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
