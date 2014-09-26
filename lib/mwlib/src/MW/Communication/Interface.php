<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Communication
 */

/**
 * Common interface for communication with delivery and payment providers.
 *
 * @package MW
 * @subpackage Communication
 */
interface MW_Communication_Interface
{
	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param string $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload );
}