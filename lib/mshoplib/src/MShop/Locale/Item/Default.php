<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Common locale class containing the site, language and currency information.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Locale_Item_Interface
{
	private $_site;
	private $_sitePath;
	private $_siteSubTree;
	private $_values;


	/**
	 * Initializes the object with the locale values.
	 *
	 * @param array $values Values to be set on initialisation
	 * @param MShop_Locale_Item_Site_Interface|null $site Site object
	 * @param array $sitepath List of site IDs up to the root site item
	 * @param array $siteSubTree List of site IDs from all sites below the current site
	 */
	public function __construct( array $values = array( ), MShop_Locale_Item_Site_Interface $site = null,
		array $sitePath = array(), array $siteSubTree = array() )
	{
		parent::__construct('locale.', $values);

		$this->_values = $values;
		$this->_site = $site;
		$this->_sitePath = $sitePath;
		$this->_siteSubTree = $siteSubTree;
	}


	/**
	 * Returns the site item object.
	 *
	 * @return MShop_Locale_Item_Site_Interface Site item object
	 * @throws MShop_Locale_Exception if site object isn't available
	 */
	public function getSite()
	{
		if ( $this->_site === null ) {
			throw new MShop_Locale_Exception('No site item available');
		}

		return $this->_site;
	}


	/**
	 * Returns the list site IDs up to the root site item.
	 *
	 * @return array List of site IDs
	 */
	public function getSitePath()
	{
		return $this->_sitePath;
	}


	/**
	 * Returns the list site IDs of the whole site subtree.
	 *
	 * @return array List of site IDs
	 */
	public function getSiteSubTree()
	{
		return $this->_siteSubTree;
	}


	/**
	 * Returns the Site ID of the item.
	 *
	 * @return integer|null Site ID (or null for global site)
	 */
	public function getSiteId()
	{
		return ( isset( $this->_values['siteid'] ) ? (int) $this->_values['siteid'] : null );
	}


	/**
	 * Sets the identifier of the shop instance.
	 *
	 * @param ID of the shop instance.
	 */
	public function setSiteId( $id )
	{
		if ( $id === $this->getSiteId() ) { return; }

		$this->_values['siteid'] = (int) $id;
		$this->_sitePath = array( (int) $id );
		$this->_siteSubTree = array( (int) $id );

		/** @todo: Wrong site item shouldn't be available any more but causes problems in controller */
		// $this->_site = null;

		$this->setModified();
	}


	/**
	 * Returns the two letter ISO language code.
	 *
	 * @return string Language ID
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : '' );
	}


	/**
	 * Sets the language ID.
	 *
	 * @param string $langid Two letter ISO language code
	 */
	public function setLanguageId( $langid )
	{
		if ( $langid == $this->getLanguageId() ) { return; }

		if( ! empty( $langid ) && ( strlen( $langid ) !== 2 || ctype_alpha( $langid ) === false ) ) {
			throw new MShop_Locale_Exception( sprintf( 'Invalid characters in ISO language code "%1$s"', $langid ) );
		}

		$this->_values['langid'] = (string) $langid;
		$this->setModified();
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string Currency ID (e.g: EUR)
	 */
	public function getCurrencyId()
	{
		return ( isset( $this->_values['currencyid'] ) ? (string) $this->_values['currencyid'] : '' );
	}


	/**
	 * Sets the currency ID.
	 *
	 * @param string $currencyid Currency (e.g: EUR)
	 */
	public function setCurrencyId( $currencyid )
	{
		if ( $currencyid == $this->getCurrencyId() ) { return; }

		if( ! empty( $currencyid ) && ( strlen( $currencyid ) !== 3 || ctype_alpha( $currencyid ) === false ) ) {
			throw new MShop_Locale_Exception( sprintf( 'Invalid characters in ISO currency code "%1$s"', $currencyid ) );
		}

		$this->_values['currencyid'] = (string) $currencyid;
		$this->setModified();
	}


	/**
	 * Returns the position of the item.
	 *
	 * @param integer $pos Position of the item
	 */
	public function getPosition()
	{
		return ( isset( $this->_values['pos'] ) ? (int) $this->_values['pos'] : 0 );
	}


	/**
	 * Sets the position of the item.
	 *
	 * @param integer $pos Position of the item
	 */
	public function setPosition( $pos )
	{
		if ( $pos == $this->getPosition() ) { return; }

		$this->_values['pos'] = (int) $pos;
		$this->setModified();
	}


	/**
	 * Returns the status property of the locale item
	 *
	 * @return integer Returns the status of the locale item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status property
	 *
	 * @param integer $status The status of the locale item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['locale.languageid'] = $this->getLanguageId();
		$list['locale.currencyid'] = $this->getCurrencyId();
		$list['locale.position'] = $this->getPosition();
		$list['locale.status'] = $this->getStatus();

		return $list;
	}

}
