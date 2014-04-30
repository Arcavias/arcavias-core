<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'plugin' => array (
		array ( 'typeid' => 'plugin/order', 'label' => 'Adds addresses/delivery/payment to basket', 'provider' => 'Autofill', 'config' => array("autofill.useorder" => 1, "autofill.orderaddress" => 1, "autofill.orderservice" => 1, "autofill.delivery" => 1, "autofill.payment" => 0), 'position' => 0, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for required addresses (billing/delivery)', 'provider' => 'AddressesAvailable', 'config' => array("payment" => 1, "delivery" => ''), 'position' => 1, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for required services (delivery/payment)', 'provider' => 'ServicesAvailable', 'config' => array("payment" => 1, "delivery" => 1), 'position' => 2, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for deleted products', 'provider' => 'ProductGone', 'config' => array(), 'position' => 3, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for changed product prices', 'provider' => 'ProductPrice', 'config' => array(), 'position' => 4, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for products out of stock', 'provider' => 'ProductStock', 'config' => array(), 'position' => 5, 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for necessary basket limits', 'provider' => 'BasketLimits', 'config' => array("min-products" => 1, "max-products" => 100, "min-value" => array("EUR" => "1.00"), "max-value" => array("EUR" => "10000.00")), 'position' => 6, 'status' => 0 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Limits maximum amount of products', 'provider' => 'ProductLimit', 'config' => array("single-number-max" => 10, "total-number-max" => 100, "single-value-max" => array("EUR" => "1000.00"), "total-value-max" => array("EUR" => "10000.00")), 'position' => 7, 'status' => 0 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Free shipping above threshold', 'provider' => 'Shipping', 'config' => array("threshold" => array ("EUR" =>"1.00" )), 'position' => 8, 'status' => 0 ),
	)
);