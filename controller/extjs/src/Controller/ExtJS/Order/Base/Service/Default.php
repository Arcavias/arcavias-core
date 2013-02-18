<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Default.php 14818 2012-01-12 09:53:56Z spopp $
 */


/**
 * ExtJS order base delivery controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Order_Base_Service_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the Order base service controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Order_Base_Service' );

		$manager = MShop_Order_Manager_Factory::createManager( $context );
		$baseManager =  $manager->getSubManager( 'base' );
		$this->_manager = $baseManager->getSubManager( 'service' );
	}


	/**
	 * Creates a new order base service item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the order base service properties
	 * @return array Associative list with nodes and success value
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if( isset( $entry->{'order.base.service.id'} ) ) { $item->setId( $entry->{'order.base.service.id'} ); }
			if( isset( $entry->{'order.base.service.baseid'} ) ) { $item->setBaseId( $entry->{'order.base.service.baseid'} ); }
			if( isset( $entry->{'order.base.service.code'} ) ) { $item->setCode( $entry->{'order.base.service.code'} ); }
			if( isset( $entry->{'order.base.service.name'} ) ) { $item->setName( $entry->{'order.base.service.name'} ); }
			if( isset( $entry->{'order.base.service.type'} ) ) { $item->setType( $entry->{'order.base.service.type'} ); }

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}
}
