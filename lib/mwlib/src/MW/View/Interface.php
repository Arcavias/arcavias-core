<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * Common interface for all view classes.
 *
 * @method string|array config(string $name = null, string|array $default = null) Returns the config value for the given key
 * @method string date(string $date) Returns the formatted date
 * @method MW_View_Helper_Interface encoder() Returns the encoder object
 * @method string formparam(string|array $names) Returns the name for the HTML form parameter
 * @method MW_Mail_Message_Interface mail() Returns the e-mail message object
 * @method string number(integer|float|decimal $number, integer $decimals = 2) Returns the formatted number
 * @method string|array param(string|null $name, string|array $default) Returns the parameter value
 * @method string translate(string $domain, string $singular, string $plural = '', integer $number = 1) Returns the translated string or the original one if no translation is available
 * @method string url(string|null $target, string|null $controller = null, string|null $action = null, array $params = array(), array $trailing = array(), array $config = array()) Returns the URL assembled from the given arguments
 *
 * @package MW
 * @subpackage View
 */
interface MW_View_Interface
{
	/**
	 * Calls the view helper with the given name and arguments and returns it's output.
	 *
	 * @param string $name Name of the view helper
	 * @param array $args Arguments passed to the view helper
	 * @return mixed Output depending on the view helper
	 */
	public function __call( $name, array $args );

	/**
	 * Returns the value associated to the given key.
	 *
	 * @param string $key Name of the value that should be returned
	 * @return mixed Value associated to the given key
	 * @throws MW_View_Exception If the requested key isn't available
	 */
	public function __get( $key );

	/**
	 * Tests if a key with the given name exists.
	 *
	 * @param string $key Name of the value that should be tested
	 * @return boolean True if the key exists, false if not
	 */
	public function __isset( $key );

	/**
	 * Removes a key from the stored values.
	 *
	 * @param string $key Name of the value that should be removed
	 * @return void
	 */
	public function __unset( $key );

	/**
	 * Sets a new value for the given key.
	 *
	 * @param string $key Name of the value that should be set
	 * @param mixed $value Value associated to the given key
	 * @return void
	 */
	public function __set( $key, $value );

	/**
	 * Adds a view helper instance to the view.
	 *
	 * @param string $name Name of the view helper as called in the template
	 * @param MW_View_Helper_Interface $helper View helper instance
	 * @return void
	 */
	public function addHelper( $name, MW_View_Helper_Interface $helper );

	/**
	 * Assigns a whole set of values at once to the view.
	 * This method overwrites already existing key/value pairs set by the magic method.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return void
	 */
	public function assign( array $values );

	/**
	 * Returns the value associated to the given key or the default value if the key is not available.
	 *
	 * @param string $key Name of the value that should be returned
	 * @param mixed $default Default value returned if ths key is not available
	 * @return mixed Value associated to the given key or the default value
	 */
	public function get( $key, $default = null );

	/**
	 * Renders the output based on the given template file name and the key/value pairs.
	 *
	 * @param string $filename File name of the view template
	 * @return string Output generated by the template
	 * @throws MW_View_Exception If the template isn't found
	 */
	public function render( $filename );
}
