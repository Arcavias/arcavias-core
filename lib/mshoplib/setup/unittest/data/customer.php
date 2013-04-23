<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: customer.php 865 2012-06-28 14:42:53Z nsendetzky $
 */

return array (
	'customer' => array (
		'customer/UTC001' => array( 'label' => 'unitCustomer001', 'code' => 'UTC001', 'status' => 1, 'company' => 'Metaways', 'salutation' => 'mr', 'title' => 'Dr', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de' ),
		'customer/UTC002' => array( 'label' => 'unitCustomer002', 'code' => 'UTC002', 'status' => 1, 'company' => '', 'salutation' => '', 'title' => '', 'firstname' => '', 'lastname' => '', 'address1' => '', 'address2' => '', 'address3' => '', 'postal' => '', 'city' => '', 'state' => '', 'countryid' => null, 'langid' => 'de', 'telephone' => '', 'email' => '', 'telefax' => '', 'website' => '' ),
		'customer/UTC003' => array( 'label' => 'unitCustomer003', 'code' => 'UTC003', 'status' => 0, 'company' => '', 'salutation' => '', 'title' => '', 'firstname' => '', 'lastname' => '', 'address1' => '', 'address2' => '', 'address3' => '', 'postal' => '', 'city' => '', 'state' => '', 'countryid' => null, 'langid' => 'de', 'telephone' => '', 'email' => '', 'telefax' => '', 'website' => '' ),
	),

	'customer/address' => array (
		array ( 'refid' => 'customer/UTC001', 'company' => 'Metaways', 'salutation' => 'mr', 'title' => 'Dr', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'flag' => 0, 'pos' => '0' ),
		array ( 'refid' => 'customer/UTC002', 'company' => 'Metaways GmbH', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Good', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'flag' => 0, 'pos' => '1' ),
		array ( 'refid' => 'customer/UTC002', 'company' => 'Metaways GmbH', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Good', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '11099', 'city' => 'Berlin', 'state' => 'Berlin', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332221', 'email' => 'eshop@metaways.de', 'telefax' => '055544333212', 'website' => 'www.metaways.de', 'flag' => 0, 'pos' => '1' ),
		array ( 'refid' => 'customer/UTC003', 'company' => 'unitcompany', 'salutation' => 'company', 'title' => 'unittitle', 'firstname' => 'unitfirstname', 'lastname' => 'unitlastname', 'address1' => 'unitaddress1', 'address2' => 'unitaddress2', 'address3' => 'unitaddress3', 'postal' => 'unitpostal', 'city' => 'unitcity', 'state' => 'unitstate', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '1234567890', 'email' => 'unit@email', 'telefax' => '1234567891', 'website' => 'unit.web.site', 'flag' => 0, 'pos' => '2' ),
	),
);