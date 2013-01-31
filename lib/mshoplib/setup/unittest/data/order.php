<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'order/base' => array (
		'19.95' => array ( 'customerid' => 'UTC001', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '19.95', 'shipping' => '6.50', 'rebate' => '0.00', 'comment' => 'This is a comment if an order. It can be added by the user.'),
		'636.00' => array ( 'customerid' => 'UTC001', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '636.00', 'shipping' => '31.00', 'rebate' => '0.00', 'comment' => 'This is another comment.'),
		'18.00' => array ( 'customerid' => 'UTC001', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '18.00', 'shipping' => '1.00', 'rebate' => '0.00', 'comment' => 'This is a bundle basket.'),
		'10.00' => array ( 'customerid' => 'UTC001', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '10.00', 'shipping' => '4.50', 'rebate' => '2.00', 'comment' => 'This is a comment if an order. It can be added by the user.'),
	),

	'order/base/address' => array (
		array ( 'baseid' => '19.95', 'type' => 'delivery', 'company' => 'Metaways', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'flag' => null ),
		array ( 'baseid' => '636.00', 'type' => 'delivery', 'company' => 'Metaways', 'salutation' => 'mrs', 'title' => 'Dr.', 'firstname' => 'Maria', 'lastname' => 'Mustertest', 'address1' => 'Pickhuben', 'address2' => '2', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'flag' => null ),
		array ( 'baseid' => '19.95', 'type' => 'payment', 'company' => null, 'salutation' => 'mr', 'title' => '', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Durchschnitt', 'address2' => '1', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332213', 'website' => 'www.metaways.net', 'flag' => null ),
		array ( 'baseid' => '636.00', 'type' => 'payment', 'company' => null, 'salutation' => 'mrs', 'title' => '', 'firstname' => 'Adelheid', 'lastname' => 'Mustertest', 'address1' => 'Königallee', 'address2' => '1', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332213', 'website' => 'www.metaways.net', 'flag' => null ),
		array ( 'baseid' => '10.00', 'type' => 'delivery', 'company' => 'Metaways', 'salutation' => 'mrs', 'title' => 'Dr.', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332212', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'flag' => null ),
		array ( 'baseid' => '10.00', 'type' => 'payment', 'company' => null, 'salutation' => 'mr', 'title' => '', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Durchschnitt', 'address2' => '2', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332212', 'email' => 'eshop@metaways.de', 'telefax' => '055544332213', 'website' => 'www.metaways.net', 'flag' => null ),
	),

	'order/base/product' => array (
		'CNE/19.95' => array ( 'baseid' => '19.95', 'prodid' => 'CNE', 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump1.jpg', 'amount' => 9, 'price' => '4.50', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'CNC/19.95' => array ( 'baseid' => '19.95', 'prodid' => 'CNC', 'prodcode' => 'CNC', 'suppliercode' => 'unitsupplier', 'name' => 'Cafe Noire Cappuccino', 'mediaurl' => 'somewhere/thump2.jpg', 'amount' => 3, 'price' => '6.00', 'shipping' => '0.50', 'rebate' => '0.00', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 2, 'status' => 1 ),
		'U:MD/19.95' => array ( 'baseid' => '19.95', 'prodid' => 'U:MD', 'prodcode' => 'U:MD', 'suppliercode' => 'unitsupplier', 'name' => 'Unittest: Monetary rebate', 'mediaurl' => 'somewhere/thump3.jpg', 'amount' => 1, 'price' => '-5.00', 'shipping' => '0.00', 'rebate' => '5.00', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 3, 'status' => 1 ),
		'ABCD/19.95' => array ( 'baseid' => '19.95', 'prodid' => 'ABCD', 'prodcode' => 'ABCD', 'suppliercode' => 'unitsupplier', 'name' => '16 discs', 'mediaurl' => 'somewhere/thump4.jpg', 'amount' => 1, 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '4.50', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 4, 'status' => 1 ),
		'CNE/636.00' => array ( 'baseid' => '636.00', 'prodid' => 'CNE', 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump5.jpg', 'amount' => 2, 'price' => '36.00', 'shipping' => '1.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'CNC/636.00' => array ( 'baseid' => '636.00', 'prodid' => 'CNC', 'prodcode' => 'CNC', 'suppliercode' => 'unitsupplier', 'name' => 'Cafe Noire Cappuccino', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'flags' => '0', 'pos' => 2, 'status' => 1 ),
			// product bundle test data
		'bld:zyx/18.00' => array ( 'baseid' => '18.00', 'type'=> 'bundle', 'prodcode' => 'bld:zyx', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '1200.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '17.00', 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'bld:EFG/18.00' => array ( 'baseid' => '18.00', 'ordprodid' => 'bld:zyx/18.00', 'type'=> 'product', 'prodcode' => 'bld:EFG', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '16.00', 'flags' => '0', 'pos' => 2, 'status' => 1 ),
		'bld:HIJ/18.00' => array ( 'baseid' => '18.00', 'ordprodid' => 'bld:zyx/18.00', 'type'=> 'product', 'prodcode' => 'bdl:HIJ', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '1200.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '17.00', 'flags' => '0', 'pos' => 3, 'status' => 1 ),
		'bld:hal/18.00' => array ( 'baseid' => '18.00', 'type'=> 'bundle', 'prodcode' => 'bld:hal', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '1200.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '17.00', 'flags' => '0', 'pos' => 4, 'status' => 1 ),
		'bld:EFX/18.00' => array ( 'baseid' => '18.00', 'ordprodid' => 'bld:hal/18.00', 'type'=> 'product', 'prodcode' => 'bld:EFX', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '16.00', 'flags' => '0', 'pos' => 5, 'status' => 1 ),
		'bld:HKL/18.00' => array ( 'baseid' => '18.00', 'ordprodid' => 'bld:hal/18.00', 'type'=> 'product', 'prodcode' => 'bdl:HKL', 'suppliercode' => 'unitsupplier', 'name' => 'Bundle Unittest2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '18.00', 'flags' => '0', 'pos' => 6, 'status' => 1 ),
		'CNE/10.00' => array ( 'baseid' => '10.00', 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump1.jpg', 'amount' => 3, 'price' => '4.50', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 3, 'status' => 1 ),
		'ABCD/10.00' => array ( 'baseid' => '10.00', 'prodcode' => 'ABCD', 'suppliercode' => 'unitsupplier', 'name' => '16 discs', 'mediaurl' => 'somewhere/thump4.jpg', 'amount' => 1, 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '4.50', 'taxrate' => '0.00', 'flags' => '0', 'pos' => 4, 'status' => 1 ),
	),
	'order/base/product/attr' => array (
		array ( 'ordprodid' => 'CNE/19.95', 'code' => 'width', 'value' => 33, 'name' => '33' ),
		array ( 'ordprodid' => 'CNE/19.95', 'code' => 'length', 'value' => 36, 'name' => '36' ),
		array ( 'ordprodid' => 'CNC/19.95', 'code' => 'size', 'value' => 's', 'name' => 'small' ),
		array ( 'ordprodid' => 'CNC/19.95', 'code' => 'color', 'value' => 'blue', 'name' => 'blau' ),
		array ( 'ordprodid' => 'U:MD/19.95', 'code' => 'size', 'value' => 's', 'name' => 'small' ),
		array ( 'ordprodid' => 'U:MD/19.95', 'code' => 'color', 'value' => 'white', 'name' => 'weiss' ),
		array ( 'ordprodid' => 'ABCD/19.95', 'code' => 'width', 'value' => 32, 'name' => '32' ),
		array ( 'ordprodid' => 'ABCD/19.95', 'code' => 'length', 'value' => 30, 'name' => '30' ),
		array ( 'ordprodid' => 'CNE/10.00', 'code' => 'width', 'value' => 32, 'name' => '32' ),
		array ( 'ordprodid' => 'CNE/10.00', 'code' => 'length', 'value' => 36, 'name' => '36' ),
		array ( 'ordprodid' => 'ABCD/10.00', 'code' => 'width', 'value' => 32, 'name' => '32' ),
		array ( 'ordprodid' => 'ABCD/10.00', 'code' => 'length', 'value' => 30, 'name' => '30' ),
	),

	'order/base/service' => array (
		'OGONE/19.95' => array ( 'baseid' => '19.95', 'servid' => 'OGONE1', 'type' => 'payment', 'code' => 'OGONE', 'name' => 'ogone', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/19.95' => array ( 'baseid' => '19.95', 'servid' => '73test', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
		'OGONE/636.00' => array ( 'baseid' => '636.00', 'servid' => 'OGONE1', 'type' => 'payment', 'code' => 'OGONE', 'name' => 'ogone', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/636.00' => array ( 'baseid' => '636.00', 'servid' => '73test', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
		'paypal/10.00' => array ( 'baseid' => '10.00', 'servid' => 'paypal1', 'type' => 'payment', 'code' => 'paypal', 'name' => 'paypal', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/10.00' => array ( 'baseid' => '10.00', 'servid' => '73test', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'mediaurl' => 'somewhere/thump1.jpg' ),
	),

	'order/base/service/attr' => array (
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'account owner', 'code' => 'ACOWNER', 'value' => 'test user'),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'account number', 'code' => 'ACSTRING', 'value' => 9876543),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'payment method', 'code' => 'NAME', 'value' => 'CreditCard'),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'reference id', 'code' => 'REFID', 'value' => 12345678),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'transaction date', 'code' => 'TXDATE', 'value' => '2009-08-18'),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'transaction account', 'code' => 'X-ACCOUNT', 'value' => 'Kraft02'),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'transaction status', 'code' => 'X-STATUS', 'value' => 9),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'ogone alias name', 'code' => 'ogone-alias-name', 'value' => 'aliasName'),
		array ( 'ordservid' => 'OGONE/19.95', 'name' => 'ogone alias value', 'code' => 'ogone-alias-value', 'value' => 'aliasValue'),
	),

	'order' => array (
		'2008-02-15 12:34:56' => array ( 'baseid' => '19.95', 'type' => 'web', 'datepayment' => '2008-02-15 12:34:56', 'datedelivery' => null, 'statuspayment' => 6, 'statusdelivery' => 4, 'flag' => 1, 'emailflag' => 1, 'relatedid' => null ),
		'2009-09-17 16:14:32' => array ( 'baseid' => '636.00', 'type' => 'phone', 'datepayment' => '2009-09-17 16:14:32', 'datedelivery' => null, 'statuspayment' => 6, 'statusdelivery' => 4, 'flag' => 0, 'emailflag' => 0, 'relatedid' => null ),
		'2011-03-27 11:11:14' => array ( 'baseid' => '10.00', 'type' => 'web', 'datepayment' => '2011-09-17 16:14:32', 'datedelivery' => null, 'statuspayment' => 5, 'statusdelivery' => 3, 'flag' => 0, 'emailflag' => 0, 'relatedid' => null ),

	),

	'order/status' => array (
		array ( 'parentid' => '2008-02-15 12:34:56', 'type' => 'typestatus', 'value' => 'shipped' ),
		array ( 'parentid' => '2009-09-17 16:14:32', 'type' => 'typestatus', 'value' => 'waiting' ),
		array ( 'parentid' => '2011-03-27 11:11:14', 'type' => 'status', 'value' => 'waiting' ),
	)
);
