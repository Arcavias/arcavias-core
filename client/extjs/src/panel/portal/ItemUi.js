/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.portal');

MShop.panel.portal.ItemUi = Ext.extend(Ext.Panel, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',

	initComponent : function() {
		this.title = _('Overview');
		this.items = [ {
			xtype:'portal',
			region:'center',
			items:[{
				columnWidth: 0.5,
				style:'margin:5px',
				items:[{
					xtype: 'MShop.panel.log.listuismall',
					layout: 'fit',
					height: 400,
					border: true,
					collapsible : true,
					draggable : true
				}]
			},
			{
				columnWidth: 0.5,
				style:'margin:5px',
				items:[{
					xtype: 'MShop.panel.job.listuismall',
					layout: 'fit',
					height: 400,
					border: true,
					collapsible : true,
					draggable : true
				}]
			}]
		}];

		MShop.panel.portal.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.portal.itemui', MShop.panel.portal.ItemUi);

//hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.portal.itemui', MShop.panel.portal.ItemUi, 10);
