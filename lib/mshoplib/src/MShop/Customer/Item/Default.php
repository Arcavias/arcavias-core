<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 */


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Customer_Item_Interface
{
	private $_billingaddress;
	private $_salt;
	private $_values;

	/**
	 * Initializes the customer item object
	 *
	 * @param array $values List of attributes that belong to the customer item
	 */
	public function __construct( MShop_Common_Item_Address_Interface $address, array $values = array(),
		array $listItems = array(), array $refItems = array(), $salt = '' )
	{
		parent::__construct('customer.', $values, $listItems, $refItems);

		$this->_values = $values;

		if( isset( $values['salutation'] ) ) {
			$address->setSalutation( (string) $values['salutation'] );
		}

		if( isset( $values['company'] ) ) {
			$address->setCompany( (string) $values['company'] );
		}

		if( isset( $values['vatid'] ) ) {
			$address->setVatID( (string) $values['vatid'] );
		}

		if( isset( $values['title'] ) ) {
			$address->setTitle( (string) $values['title'] );
		}

		if( isset( $values['firstname'] ) ) {
			$address->setFirstname( (string) $values['firstname'] );
		}

		if( isset( $values['lastname'] ) ) {
			$address->setLastname( (string) $values['lastname'] );
		}

		if( isset( $values['address1'] ) ) {
			$address->setAddress1( (string) $values['address1'] );
		}

		if( isset( $values['address2'] ) ) {
			$address->setAddress2( (string) $values['address2'] );
		}

		if( isset( $values['address3'] ) ) {
			$address->setAddress3( (string) $values['address3'] );
		}

		if( isset( $values['postal'] ) ) {
			$address->setPostal( (string) $values['postal'] );
		}

		if( isset( $values['city'] ) ) {
			$address->setCity( (string) $values['city'] );
		}

		if( isset( $values['state'] ) ) {
			$address->setState( (string) $values['state'] );
		}

		if( isset( $values['langid'] ) ) {
			$address->setLanguageId( (string) $values['langid'] );
		}

		if( isset( $values['countryid'] ) ) {
			$address->setCountryId( (string) $values['countryid'] );
		}

		if( isset( $values['telephone'] ) ) {
			$address->setTelephone( (string) $values['telephone'] );
		}

		if( isset( $values['email'] ) ) {
			$address->setEmail( (string) $values['email'] );
		}

		if( isset( $values['telefax'] ) ) {
			$address->setTelefax( (string) $values['telefax'] );
		}

		if( isset( $values['website'] ) ) {
			$address->setWebsite( (string) $values['website'] );
		}

		$this->_billingaddress = $address;
		$this->_salt = $salt;
	}


	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 */
	public function setLabel( $value )
	{
		if ( $value == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus( $value )
	{
		if ( $value == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the code of the customer item.
	 *
	 * @return string Code of the customer item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 */
	public function setCode( $value )
	{
		$this->_checkCode( $value );

		if ( $value == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the billingaddress of the customer item.
	 *
	 * @return MShop_Common_Item_Address_Interface
	 */
	public function getPaymentAddress()
	{
		return $this->_billingaddress;
	}


	/**
	 * Sets the billingaddress of the customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address Billingaddress of the customer item
	 */
	public function setPaymentAddress( MShop_Common_Item_Address_Interface $address )
	{
		if ( $address === $this->_billingaddress && $address->isModified() === false ) { return; }

		$this->_billingaddress = $address;
		$this->setModified();
	}


	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string
	 */
	public function getBirthday()
	{
		return ( isset( $this->_values['birthday'] ) ? (string) $this->_values['birthday'] : null );
	}


	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param date $value Birthday of the customer item
	 */
	public function setBirthday( $value )
	{
		if ( $value === $this->getBirthday() ) { return; }

		if( $value !== null )
		{
			$this->_checkDateOnlyFormat( $value );
			$value = (string) $value;
		}

		$this->_values['birthday'] = $value;
		$this->setModified();
	}


	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return ( isset( $this->_values['password'] ) ? (string) $this->_values['password'] : '' );
	}


	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 */
	public function setPassword( $value )
	{
		$password = sha1( (string) $value . $this->_salt );
		if ( $password == $this->getPassword() ) { return; }

		$this->_values['password'] = $password;
		$this->setModified();
	}


	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified()
	{
		return ( isset( $this->_values['vdate'] ) ? (string) $this->_values['vdate'] : null );
	}


	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
	 */
	public function setDateVerified( $value )
	{
		if( $value === $this->getDateVerified() ) { return; }

		$this->_checkDateOnlyFormat( $value );

		$this->_values['vdate'] = ( $value ? (string) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();
		$list['customer.birthday'] = $this->getBirthday();
		$list['customer.status'] = $this->getStatus();
		$list['customer.password'] = $this->getPassword();
		$list['customer.dateverified'] = $this->getDateVerified();
		$list['customer.salutation'] = $this->getPaymentAddress()->getSalutation();
		$list['customer.company'] = $this->getPaymentAddress()->getCompany();
		$list['customer.vatid'] = $this->getPaymentAddress()->getVatID();
		$list['customer.title'] = $this->getPaymentAddress()->getTitle();
		$list['customer.firstname'] = $this->getPaymentAddress()->getFirstname();
		$list['customer.lastname'] = $this->getPaymentAddress()->getLastname();
		$list['customer.address1'] = $this->getPaymentAddress()->getAddress1();
		$list['customer.address2'] = $this->getPaymentAddress()->getAddress2();
		$list['customer.address3'] = $this->getPaymentAddress()->getAddress3();
		$list['customer.postal'] = $this->getPaymentAddress()->getPostal();
		$list['customer.city'] = $this->getPaymentAddress()->getCity();
		$list['customer.state'] = $this->getPaymentAddress()->getState();
		$list['customer.languageid'] = $this->getPaymentAddress()->getLanguageId();
		$list['customer.countryid'] = $this->getPaymentAddress()->getCountryId();
		$list['customer.telephone'] = $this->getPaymentAddress()->getTelephone();
		$list['customer.email'] = $this->getPaymentAddress()->getEmail();
		$list['customer.telefax'] = $this->getPaymentAddress()->getTelefax();
		$list['customer.website'] = $this->getPaymentAddress()->getWebsite();
		return $list;
	}


	/**
	 * Implements deep copies for clones.
	 */
	public function __clone()
	{
		$this->_billingaddress = clone $this->_billingaddress;
	}


	/**
	 * Tests if the date param represents an ISO format
	 *
	 * @param string ISO date in yyyy-mm-dd format
	 */
	protected function _checkDateOnlyFormat( $date )
	{
		if( $date !== null && preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', $date ) !== 1 ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD" expected.', $date ) );
		}
	}
}
