<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_text_type" ("siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_text_type"
			SET "siteid"=?, "code"=?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_text_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mtexty."id", mtexty."siteid", mtexty."code", mtexty."domain", mtexty."label",
				mtexty."status", mtexty."mtime", mtexty."editor", mtexty."ctime"
			FROM "mshop_text_type" mtexty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtexty."id"
				FROM "mshop_text_type" mtexty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	)
);
