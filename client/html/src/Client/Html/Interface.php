<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Common interface for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
interface Client_Html_Interface
{
	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null );

	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @return string|null String including HTML tags for the header or null in case of an error
	 * @todo 2015.03 Add $uid, $tags and $expire parameter to make them mandatory
	 */
	public function getHeader();

	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @return string|null HTML code or null in case of an error
	 * @todo 2015.03 Add $uid, $tags and $expire parameter to make them mandatory
	 */
	public function getBody();

	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView();

	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 */
	public function setView( MW_View_Interface $view );

	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 */
	public function isCachable( $what );

	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @return string Modified body content
	 */
	public function modifyBody( $content );

	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @return string Modified header content
	 */
	public function modifyHeader( $content );

	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 */
	public function process();
}
