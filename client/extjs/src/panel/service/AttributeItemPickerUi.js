/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: AttributeItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.service');

// hook media picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'service/list/type/attribute'
	},
	listConfig : {
		domain : 'service',
		prefix : 'attribute.'
	}
}, 40);
