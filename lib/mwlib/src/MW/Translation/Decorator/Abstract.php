<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Translation
 */


/**
 * Base class for all translator decorators.
 *
 * @package MW
 * @subpackage Translation
 */
abstract class MW_Translation_Decorator_Abstract
	extends MW_Translation_Abstract
	implements MW_Translation_Decorator_Interface
{
	private $_object;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Translation_Interface $object Translation object or decorator
	 */
	public function __construct( MW_Translation_Interface $object )
	{
		$this->_object = $object;
	}


	/**
	 * Returns the translated string.
	 *
	 * @param string $domain Translation domain
	 * @param string $string String to be translated
	 * @return string The translated string
	 */
	public function dt( $domain, $string )
	{
		return $this->_object->dt( $domain, $string );
	}


	/**
	 * Returns the translated string by the given plural and quantity.
	 *
	 * @param string $domain Translation domain
	 * @param string $singular String in singular form
	 * @param string $plural String in plural form
	 * @param integer $number Quantity to chose the correct plural form for languages with plural forms
	 * @return string Returns the translated singular or plural form of the string depending on the given number.
	 */
	public function dn( $domain, $singular, $plural, $number )
	{
		return $this->_object->dn( $domain, $singular, $plural, $number );
	}


	/**
	 * Returns all locale string of the given domain.
	 *
	 * @param string $domain Translation domain
	 * @return array Associative list with original string as key and translation
	 * 	as value or an associative list with index => translation as value if
	 * 	plural forms are available
	 */
	public function getAll( $domain )
	{
		return $this->_object->getAll( $domain );
	}


	/**
	 * Returns the current locale string.
	 *
	 * @return string ISO locale string
	 */
	public function getLocale()
	{
		return $this->_object->getLocale();
	}


	/**
	 * Returns the wrapped translation object.
	 *
	 * @return MW_Translation_Interface Translation object
	 */
	protected function _getObject()
	{
		return $this->_object;
	}
}
