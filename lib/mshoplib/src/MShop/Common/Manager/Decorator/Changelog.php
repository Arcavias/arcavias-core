<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Provides a changelog decorator for managers.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Decorator_Changelog
	extends MShop_Common_Manager_Decorator_Abstract
{
	/**
	 * Adds or updates an item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item object whose data should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$manager = $this->_getManager();

		$manager->saveItem( $item, $fetch );
		$new = $manager->getItem( $item->getId() );

		$this->_getContext()->getLogger()->log( $new->toArray(), MW_Logger_Abstract::NOTICE, 'changelog' );
	}
}