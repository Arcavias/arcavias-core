<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Abstract class for all locale manager implementations.
 *
 * @package MShop
 * @subpackage Locale
 */
abstract class MShop_Locale_Manager_Abstract
	extends MShop_Common_Manager_Abstract
{
	/**
	 * Only current site.
	 * Use only the current site ID, not inherited ones or IDs of sub-sites.
	 */
	const SITE_ONE = 0;

	/**
	 * Current site up to root site.
	 * Use all site IDs from the current site up to the root site.
	 */
	const SITE_PATH = 1;

	/**
	 * Current site and sub-sites.
	 * Use all site IDs from the current site and its sub-sites.
	 */
	const SITE_SUBTREE = 2;

	/**
	 * Constant for all other constants.
	 * Use all site IDs from the current site up to the root site but also the
	 * sub-sites of the current site.
	 */
	const SITE_ALL = 3;
}
