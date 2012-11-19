<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_price"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_price" ( "siteid", "typeid", "currencyid", "domain", "label", "quantity",
				"value", "shipping", "rebate", "taxrate", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_price"
			SET "siteid" = ?, "typeid" = ?, "currencyid" = ?, "domain" = ?, "label" = ?, "quantity" = ?,
				"value" = ?, "shipping" = ?, "rebate" = ?, "taxrate" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id"=?
		',
		'search' => '
			SELECT DISTINCT mpri."id", mpri."siteid", mpri."typeid",mpri."currencyid", mpri."domain", mpri."label",
				mpri."quantity", mpri."value", mpri."shipping", mpri."rebate", mpri."taxrate", mpri."status",
				mpri."mtime", mpri."editor", mpri."ctime"
			FROM "mshop_price" AS mpri
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpri."id"
				FROM "mshop_price" AS mpri
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);