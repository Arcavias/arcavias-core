<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Address_Interface extends MShop_Common_Item_Interface
{
	/**
	 * Returns the reference id regarding to the product suppliercode of the address.
	 *
	 * @return string Address reference id
	 */
	public function getRefId();


	/**
	 * Sets the new reference id regarding to the product suppliercode of the address.
	 *
	 * @param string $refid New reference id of the address
	 * @return void
	 */
	public function setRefId($refid);


	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany();


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 * @return void
	 */
	public function setCompany($company);
	
	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatID();


	/**
	 * Sets a new vatid.
	 *
	 * @param string $vatid New vatid
	 * @return void
	 */
	public function setVatID($vatid);


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in MShop_Common_Item_Address_Abstract
	 */
	public function getSalutation();


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in MShop_Common_Item_Address_Abstract
	 * @return void
	 */
	public function setSalutation($salutation);


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle();


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 * @return void
	 */
	public function setTitle($title);


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname();


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 * @return void
	 */
	public function setFirstname($firstname);


	/**
	 * Returns the last name of the perosn.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname();


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 * @return void
	 */
	public function setLastname($lastname);


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1();


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 * @return void
	 */
	public function setAddress1($address1);


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2();


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 * @return void
	 */
	public function setAddress2($address2);


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3();


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 * @return void
	 */
	public function setAddress3($address3);


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal();


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 * @return void
	 */
	public function setPostal($postal);


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity();


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 * @return void
	 */
	public function setCity($city);


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState();


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 * @return void
	 */
	public function setState($state);


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId();


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countryid Unique ID of the country
	 * @return void
	 */
	public function setCountryId($countryid);


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string Unique ID of the language
	 */
	public function getLanguageId();


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 * @return void
	 */
	public function setLanguageId($langid);


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone();


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 * @return void
	 */
	public function setTelephone($telephone);


	/**
	 * Returns the email address.
	 *
	 * @return string Email address
	 */
	public function getEmail();


	/**
	 * Sets a new email address.
	 *
	 * @param string $email New email address
	 * @return void
	 */
	public function setEmail($email);


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax();


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 * @return void
	 */
	public function setTelefax($telefax);


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite();


	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 * @return void
	 */
	public function setWebsite($website);


	/**
	 * Returns the flag value.
	 *
	 * @return integer Generic flag value
	 */
	public function getFlag();


	/**
	 * Sets a new flag value.
	 *
	 * @param integer $flag New flag value
	 * @return void
	 */
	public function setFlag($flag);


	/**
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition();


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position New position of the address item
	 * @return void
	 */
	public function setPosition($position);


	/**
	 * Copies the values of the order address item into the address item.
	 *
	 * @param MShop_Order_Item_Base_Address_Interface $item Order address item
	 * @return MShop_Common_Item_Address_Interface The address item for method chaining
	 */
	public function copyFrom( MShop_Order_Item_Base_Address_Interface $item );

}
