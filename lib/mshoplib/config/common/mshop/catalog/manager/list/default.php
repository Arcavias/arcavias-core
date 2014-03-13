<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'aggregate' => '
			SELECT "key", COUNT("id") AS "count"
			FROM (
				SELECT DISTINCT :key AS "key", mcatli."id" AS "id"
				FROM "mshop_catalog_list" AS mcatli
				:joins
				WHERE :cond
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		',
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_catalog_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_catalog_list"( "parentid", "siteid", "typeid", "domain", "refid", "start", "end",
				"config", "pos", "status", "mtime", "editor", "ctime"  )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_catalog_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
				"config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_catalog_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_catalog_list"
			WHERE :cond
			AND siteid = ?
		',
		'move' => '
			UPDATE "mshop_catalog_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mcatli."id", mcatli."parentid", mcatli."siteid", mcatli."typeid", mcatli."domain", mcatli."refid",
				mcatli."start", mcatli."end", mcatli."config", mcatli."pos", mcatli."status", mcatli."mtime", mcatli."editor", mcatli."ctime"
			FROM "mshop_catalog_list" AS mcatli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcatli."id"
				FROM "mshop_catalog_list" AS mcatli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
