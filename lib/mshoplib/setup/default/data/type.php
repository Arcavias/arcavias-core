<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id$
 */

return array (
	'attribute/type' => array (
		array( 'domain' => 'product', 'code' => 'color', 'label' => 'Color', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'size', 'label' => 'Size', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'width', 'label' => 'Width', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'length', 'label' => 'Length', 'status' => 1 ),
	),

	'attribute/list/type' => array (
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'catalog/list/type' => array (
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1 ),
	),

	'customer/list/type' => array (
		array( 'domain' => 'product', 'code' => 'favorite', 'label' => 'Favorite', 'status' => 1 ),
	),

	'media/type' => array (
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'stage', 'label' => 'Stage', 'status' => 1 ),
	),

	'media/list/type' => array (
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'plugin/type' => array (
		array ( 'domain' => 'plugin', 'code' => 'order', 'label' => 'Order', 'status' => 1 )
	),

	'price/type' => array(
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'price/list/type' => array(
		array( 'domain' => 'customer', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'product/type' => array (
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Article', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'select', 'label' => 'Selection', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'bundle', 'label' => 'Bundle', 'status' => 1 ),
	),

	'product/list/type' => array(
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'config', 'label' => 'Configurable', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'variant', 'label' => 'Variant', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product/tag', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'suggestion', 'label' => 'Suggestion', 'status' => 1 ),
	),

	'product/tag/type' => array(
		array( 'domain' => 'product/tag', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'service/type' => array (
		array( 'domain' => 'service', 'code' => 'payment', 'label' => 'Payment', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'delivery', 'label' => 'Delivery', 'status' => 1 ),
	),

	'service/list/type' => array (
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'text/type' => array (
		array( 'domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'quote', 'label' => 'Quote', 'status' => 1 ),
	),

	'text/list/type' => array (
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	)

);