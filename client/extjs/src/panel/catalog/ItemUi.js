/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14630 2011-12-29 14:59:00Z nsendetzky $
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',
	siteidProperty : 'catalog.siteid',

	initComponent : function() {
		this.title = _( 'Catalog item details' );

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.catalog.ItemUi',
			plugins : ['ux.itemregistry'],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.catalog.ItemUi.BasicPanel',
				plugins : ['ux.itemregistry'],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'code',
							allowBlank : false,
							emptyText : _('Category code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'label',
							allowBlank : false,
							emptyText : _( 'Category name (required)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'catalog.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'catalog.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'catalog.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.catalog.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('catalog.config') : {} )
				} ]
			} ]
		} ];
		
		this.store.on('beforesave', this.onBeforeSave, this);
		
		MShop.panel.catalog.ItemUi.superclass.initComponent.call( this );
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['label'] : 'new';
		this.setTitle( 'Catalog: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.catalog.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	
	onBeforeSave: function( store, data ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.catalog.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( key.trim() !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['catalog.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['catalog.config'] = config;
		}
	},

	
	onSaveItem: function() {
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false )
		{
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false )
		{
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.record.beginEdit();
		this.record.set( 'catalog.label', this.mainForm.getForm().findField( 'label' ).getValue() );
		this.record.set( 'catalog.status', this.mainForm.getForm().findField( 'status' ).getValue() );
		this.record.set( 'catalog.code', this.mainForm.getForm().findField( 'code' ).getValue() );
		this.record.endEdit();

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	}
});

Ext.reg( 'MShop.panel.catalog.itemui', MShop.panel.catalog.ItemUi );