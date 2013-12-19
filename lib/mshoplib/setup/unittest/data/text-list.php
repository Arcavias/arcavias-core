<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'text/list/type' => array (
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'catalog/default' => array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'price/default' => array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		'media/align-left' => array( 'domain' => 'media', 'code' => 'align-left', 'label' => 'Align left', 'status' => 1 ),
		'media/align-right' => array( 'domain' => 'media', 'code' => 'align-right', 'label' => 'Align right', 'status' => 1 ),
		'media/align-top' => array( 'domain' => 'media', 'code' => 'align-top', 'label' => 'Align top', 'status' => 1 ),
		'media/align-bottom' => array( 'domain' => 'media', 'code' => 'align-bottom', 'label' => 'Align bottom', 'status' => 1 ),
	),

	'text/list' => array (
		array( 'parentid' => 'text/cafe_long_desc', 'typeid' => 'media/align-left', 'domain' => 'media', 'refid' => 'media/path/to/folder/example1.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'text/cafe_long_desc', 'typeid' => 'media/align-left', 'domain' => 'media', 'refid' => 'media/path/to/folder/example2.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'text/cafe_long_desc', 'typeid' => 'media/align-left', 'domain' => 'media', 'refid' => 'media/path/to/folder/example3.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'text/cafe_long_desc', 'typeid' => 'media/align-left', 'domain' => 'media', 'refid' => 'media/path/to/folder/example4.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 3, 'status' => 1 ),

		array( 'parentid' => 'text/tea_long_desc', 'typeid' => 'media/align-right', 'domain' => 'media', 'refid' => 'media/path/to/folder/example1.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2002-01-01 00:00:00', 'config' => array(), 'pos' => 3, 'status' => 1 ),
		array( 'parentid' => 'text/tea_long_desc', 'typeid' => 'media/align-right', 'domain' => 'media', 'refid' => 'media/path/to/folder/example2.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'text/tea_long_desc', 'typeid' => 'media/align-right', 'domain' => 'media', 'refid' => 'media/path/to/folder/example3.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'text/tea_long_desc', 'typeid' => 'media/align-right', 'domain' => 'media', 'refid' => 'media/path/to/folder/example4.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 0, 'status' => 1 ),

		array( 'parentid' => 'text/misc_long_desc', 'typeid' => 'media/align-top', 'domain' => 'media', 'refid' => 'media/path/to/folder/example1.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'text/misc_long_desc', 'typeid' => 'media/align-top', 'domain' => 'media', 'refid' => 'media/path/to/folder/example2.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'text/misc_long_desc', 'typeid' => 'media/align-top', 'domain' => 'media', 'refid' => 'media/path/to/folder/example3.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 3, 'status' => 1 ),
		array( 'parentid' => 'text/misc_long_desc', 'typeid' => 'media/align-top', 'domain' => 'media', 'refid' => 'media/path/to/folder/example4.jpg', 'start' => '2010-01-01 00:00:00', 'end' => '2002-01-01 00:00:00', 'config' => array(), 'pos' => 2, 'status' => 1 ),
	)
);