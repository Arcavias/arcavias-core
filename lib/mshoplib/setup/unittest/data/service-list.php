<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: service-list.php 865 2012-06-28 14:42:53Z nsendetzky $
 */

return array (
	'service/list/type' => array (
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'catalog/default' => array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'price/default' => array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),

		'text/unittype1' => array( 'domain' => 'text', 'code' => 'unittype1', 'label' => 'Unit type 1', 'status' => 1 ),
		'text/unittype2' => array( 'domain' => 'text', 'code' => 'unittype2', 'label' => 'Unit type 2', 'status' => 1 ),
		'text/unittype3' => array( 'domain' => 'text', 'code' => 'unittype3', 'label' => 'Unit type 3', 'status' => 1 ),
		'text/unittype4' => array( 'domain' => 'text', 'code' => 'unittype4', 'label' => 'Unit type 4', 'status' => 1 ),
	),

	'service/list' => array (
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'price/default', 'domain' => 'price', 'refid' => 'price/service/default/12.95/1.99', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'price/default', 'domain' => 'price', 'refid' => 'price/service/default/2.95/0.00', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text1', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text2', 'start' => null, 'end' => null, 'pos' => 1 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text3', 'start' => '2008-02-17 12:34:58', 'end' => '2010-01-01 23:59:59', 'pos' => 2 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/service_text4', 'start' => null, 'end' => null, 'pos' => 3 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/service_text5', 'start' => null, 'end' => null, 'pos' => 4 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/service_text6', 'start' => null, 'end' => null, 'pos' => 5 ),
		array( 'parentid' => 'service/delivery/unitcode', 'typeid' => 'media/default', 'domain' => 'media', 'refid' => 'media/service_image1', 'start' => null, 'end' => null, 'pos' => 0 ),

		array( 'parentid' => 'service/payment/unitpaymentcode', 'typeid' => 'price/default', 'domain' => 'price', 'refid' => 'price/service/default/12.95/1.99', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/payment/unitpaymentcode', 'typeid' => 'price/default', 'domain' => 'price', 'refid' => 'price/service/default/2.95/0.00', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/payment/unitpaymentcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text1', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'service/payment/unitpaymentcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text2', 'start' => null, 'end' => null, 'pos' => 1 ),
		array( 'parentid' => 'service/payment/unitpaymentcode', 'typeid' => 'text/unittype1', 'domain' => 'text', 'refid' => 'text/service_text3', 'start' => '2008-02-17 12:34:58', 'end' => '2010-01-01 23:59:59', 'pos' => 2 ),
	)
);