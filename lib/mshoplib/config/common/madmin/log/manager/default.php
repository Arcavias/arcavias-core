<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14711 2012-01-05 12:52:13Z nsendetzky $
 */

return array(
	'delete' => '
		DELETE FROM "madmin_log"
		WHERE :cond
		AND siteid = ?
	',
	'insert' => '
		INSERT INTO "madmin_log" ("siteid", "facility", "timestamp", "priority", "message", "request")
		VALUES ( ?, ?, ?, ?, ?, ? )
	',
	'update' => '
		UPDATE "madmin_log"
		SET "siteid" = ?, "facility" = ?, "timestamp" = ?, "priority" = ?, "message" = ?, "request" = ?
		WHERE "id" = ?
	',
	'search' => '
		SELECT malog."id", malog."siteid", malog."facility", malog."timestamp",
			malog."priority", malog."message", malog."request"
		FROM "madmin_log" AS malog
		WHERE :cond
		/*-orderby*/ ORDER BY :order /*orderby-*/
		LIMIT :size OFFSET :start
	',
	'count' => '
		SELECT COUNT( malog."id" ) AS "count"
		FROM "madmin_log" AS malog
		WHERE :cond
	',
);
