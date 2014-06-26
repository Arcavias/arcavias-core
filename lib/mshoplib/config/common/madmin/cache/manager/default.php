<?php

return array(
	'delete' => '
		DELETE FROM "madmin_cache" WHERE "siteid" = ? AND :cond
	',
	'deletebytag' => '
		DELETE FROM "madmin_cache" WHERE "siteid" = ? AND id IN (
			SELECT t."id" FROM "madmin_cache_tag"
			WHERE "tsiteid" = ? AND :cond
		)
	',
	'get' => '
		SELECT "id", "value", "expire" FROM "madmin_cache"
		WHERE "siteid" = ? AND :cond
	',
	'getbytag' => '
		SELECT "id", "value", "expire" FROM "madmin_cache"
		JOIN "madmin_cache_tag" ON "tid" = "id"
		WHERE "siteid" = ? AND "tsiteid" = ? AND :cond
	',
	'set' => '
		INSERT INTO "madmin_cache" (
			"id", "siteid", "expire", "value"
		) VALUES (
			?, ?, ?, ?
		)
	',
	'settag' => '
		INSERT INTO "madmin_cache_tag" (
			"tid", "tsiteid", "tname"
		) VALUES (
			?, ?, ?
		)
	',
	'search' => '
		SELECT "id", "value", "expire" FROM "madmin_cache"
		WHERE :cond
		LIMIT :size OFFSET :start
	',
	'count' => '
		SELECT COUNT(*) AS "count"
		FROM(
			SELECT DISTINCT "id"
			FROM "madmin_cache"
			WHERE :cond
			LIMIT 10000 OFFSET 0
		) AS list
	',
);