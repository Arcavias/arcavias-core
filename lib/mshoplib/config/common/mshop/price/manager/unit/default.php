<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_price_unit"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_price_unit" (
				"siteid", "code", "label", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_price_unit"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mpriun."id", mpriun."siteid", mpriun."code",
				mpriun."label", mpriun."status", mpriun."mtime",
				mpriun."editor", mpriun."ctime"
			FROM "mshop_price_unit" AS mpriun
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpriun."id"
				FROM "mshop_price_unit" AS mpriun
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
