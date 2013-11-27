/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel' );

MShop.panel.AbstractUsedByListUi = Ext.extend( Ext.Panel, {

	/**
	 * @cfg {String} recordName (required)
	 */
	recordName: null,

	/**
	 * @cfg {String} idProperty (required)
	 */
	idProperty: null,

	/**
	 * @cfg {String} siteidProperty (required)
	 */
	siteidProperty: null,

	/**
	 * @cfg {String} itemUi xtype
	 */
	itemUiXType : null,

	/**
	 * @cfg {Object} sortInfo (optional)
	 */
	sortInfo: null,

	/**
	 * @cfg {String} autoExpandColumn (optional)
	 */
	autoExpandColumn: null,

	/**
	 * @cfg {Object} storeConfig (optional)
	 */
	storeConfig: null,

	/**
	 * @cfg {Object} gridConfig (optional)
	 */
	gridConfig: null,
	
	
	grid: null,

	/**
	 * @cfg {String} parentIdProperty (required)
	 */
	parentIdProperty: null,

	/**
	 * @cfg {String} parentDomainPorperty (required)
	 */
	parentDomainPorperty: null,

	/**
	 * @cfg {String} parentRefIdProperty (required)
	 */
	parentRefIdProperty: null,
	
	/**
	 * 
	 * @cfg {Object} stores ParentItems
	 */
	parentStore: null,

	layout: 'fit',

	initComponent : function()
	{
		this.initStore();

		this.listTypeStore = MShop.GlobalStoreMgr.get( this.recordName + '_Type', 'Product' );
		this.productTypeStore = MShop.GlobalStoreMgr.get( 'Product_Type', 'Product' );
		
		MShop.panel.AbstractUsedByListUi.superclass.initComponent.call( this );
	},
	
	initStore: function()
	{
		this.store = new Ext.data.DirectStore( Ext.apply( {
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord( this.recordName ),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter( {
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			baseParams: {
				start: 0,
				limit: 50
			},
			sortInfo: this.sortInfo
		}, this.storeConfig ) );
		
		this.store.on( 'load', this.onLoaded, this );
		this.store.on( 'beforeload', this.onBeforeLoad, this );
		this.store.on( 'exception', this.onStoreException, this );
		this.store.on( 'beforewrite', this.onBeforeWrite, this );
	},

	afterRender: function()
	{
		MShop.panel.AbstractUsedByListUi.superclass.afterRender.apply( this, arguments );

		this.ParentItemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		if ( !this.store.autoLoad ) {
			this.store.load();
		}
		
		colModel = new Ext.grid.ColumnModel(
			this.getColumns()
		);
		
		this.grid = new Ext.grid.GridPanel( {
			border: false,
			loadMask: true,
			store: this.store,
			autoExpandColumn: this.autoExpandColumn,
			cm: colModel,
			bbar: {
				xtype: 'MShop.elements.pagingtoolbar',
				store: this.store
			}
		} );

		this.grid.on( 'rowdblclick', this.onOpenEditWindow.createDelegate( this, ['edit']), this );
		this.add( this.grid );
	},

	onBeforeLoad: function( store, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		var domainFilter = {};
		domainFilter[this.parentDomainPorperty] = 'product';

		var refIdFilter = {};
		
		refIdFilter[this.parentRefIdProperty] = null;
		if( this.ParentItemUi.record.data['product.id'] ) {
			refIdFilter[this.parentRefIdProperty] = this.ParentItemUi.record.data['product.id'];
		}
		
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				 	'==' : domainFilter
				}, {
					'==' : refIdFilter
			} ],
			'parents': true
		};
	},

	onBeforeWrite: function( store, action, records, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainProperty( store, action, records, options );
		}
	},
		
	onLoaded: function( store, records, options)
	{
		this.parentStore = this.getParentStore();
		
		colModel = new Ext.grid.ColumnModel(
			this.getColumns()
		);
		
		this.grid.reconfigure(this.store, colModel);
		this.doLayout();
	},
	
	getParentStore: function()
	{
		var recordName = this.parentItemRecordName,
		idProperty = this.parentItemIdProperty,
		data = { items : [], total : 0 };

		if( this.store.reader.jsonData &&
			this.store.reader.jsonData.graph &&
			this.store.reader.jsonData.graph[recordName] &&
			this.store.reader.jsonData.graph[recordName]['parentitems'])
		{
			data = this.store.reader.jsonData.graph[recordName]['parentitems'];
		}

		this.parentStore = new Ext.data.DirectStore( {
			autoLoad : false,
			remoteSort : false,
			hasMultiSort : true,
			fields : MShop.Schema.getRecord(recordName),
			api: {
                read    : MShop.API[recordName].searchItems,
                create  : MShop.API[recordName].saveItems,
                update  : MShop.API[recordName].saveItems,
                destroy : MShop.API[recordName].deleteItems
            },
            writer: new Ext.data.JsonWriter({
                writeAllFields: true,
                encode: false
            }),
            paramsAsHash: true,
			totalProperty : 'total',
			idProperty : idProperty,
			data : data,
			baseParams: {
                site: MShop.config.site["locale.site.code"]
            },
            listeners: {
            	'reload' : {
            		fn: function(listUi, parentStore) {
            			listUi.parentStore = parentStore;
		
						colModel = new Ext.grid.ColumnModel(
							listUi.getColumns()
						);
						
						listUi.grid.reconfigure(listUi.store, colModel);
						listUi.doLayout();
            		}
            	}
            }
		});
		
		return this.parentStore;
	},

	onDestroy: function()
	{
		this.store.un( 'beforeload', this.onBeforeLoad, this );
		this.store.un( 'beforewrite', this.onBeforeWrite, this );
		this.store.un( 'exception', this.onStoreException, this );
		this.store.un( 'load', this.onLoaded, this );

		MShop.panel.AbstractUsedByListUi.superclass.onDestroy.apply( this, arguments );
	},

	onStoreException: function( proxy, type, action, options, response )
	{
		var title = _( 'Error' );
		var msg = response && response.error ? response.error.message : _( 'No error information available' );
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function( store )
	{
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options)
	{
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if( !this.domainProperty ) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push( {'==': condition} );
	},

	setDomainProperty: function( store, action, records, options )
	{
		var rs = [].concat( records );

		Ext.each(rs, function( record ) {
			if( !this.domainProperty ) {
				this.domainProperty = this.idProperty.replace( /\..*$/, '.domain' );
			}
			record.data[this.domainProperty] = this.domain;
		}, this );
	},
	
	onOpenEditWindow: function( action ) {
		var record = this.grid.getSelectionModel().getSelected();
		var parentRecord = this.parentStore.getById( record.data[this.parentIdProperty] );

		this.parentStore.reader.meta.root = 'items';
		delete this.parentStore.reader.ef;
		
		var itemUi = Ext.ComponentMgr.create( {
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : parentRecord,
			store: this.parentStore,
			listUI: this
		} );

		itemUi.show();
	},

	listTypeColumnRenderer : function( listTypeId, metaData, record, rowIndex, colIndex, store, listTypeStore, displayField ) {
		var list = [];
		if(listTypeStore != null ) {
			list = listTypeStore.getById( listTypeId );
			return list ? list.get( displayField ) : listTypeId;
		}
		return listTypeId;
	},

	statusColumnRenderer : function( listTypeId, metaData, record, rowIndex, colIndex, store, listTypeStore, displayField ) {
		var list = [];
		if(listTypeStore != null ) {
			list = listTypeStore.getById( listTypeId );
			metaData.css = 'statusicon-' + ( list ? Number( list.get( displayField ) ) : 0 );
		}
	},

	productTypeColumnRenderer : function( typeId, metaData, record, rowIndex, colIndex, store, typeStore, productTypeStore, prodctId, displayField ) {
		var type = [];
		if(typeStore != null ) {
			type = typeStore.getById( typeId );
			var productType = productTypeStore.getById( type.data[prodctId] );
		
			return productType ? productType.get( displayField ) : typeId;
		}
		return typeId;
	}
});
