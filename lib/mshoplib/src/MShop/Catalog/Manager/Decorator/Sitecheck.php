<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Catalog
 * @version $Id: Sitecheck.php 14689 2012-01-04 15:04:29Z nsendetzky $
 */


/**
 * Provides a site check decorator for tree managers.
 *
 * @package MShop
 * @subpackage Catalog
 */
class MShop_Catalog_Manager_Decorator_Sitecheck
	extends MShop_Catalog_Manager_Decorator_Abstract
{
	/**
	 * Adds a new item object.
	 *
	 * @param MShop_Common_Item_Interface $item Item which should be inserted
	 */
	public function insertItem( MShop_Catalog_Item_Interface $item, $parentId = null, $refId = null )
	{
		if ( $parentId !== null ) {

			$parent = $this->_getManager()->getItem( $parentId );
			$siteId = $this->_getContext()->getLocale()->getSiteId();

			if ( $parent->getSiteId() != $siteId ) {
				throw new MShop_Exception( sprintf( 'Site can not be inserted. Site ID of site differs from site ID of parent site.' ) );
			}
		}

		parent::insertItem( $item, $parentId, $refId );
	}


	/**
	 * Moves an existing item to the new parent in the storage.
	 *
	 * @param mixed $id ID of the item that should be moved
	 * @param mixed $oldParentId ID of the old parent item which currently contains the item that should be removed
	 * @param mixed $newParentId ID of the new parent item where the item should be moved to
	 * @param mixed $newRefId ID of the item where the item should be inserted before (null to append)
	 */
	public function moveItem( $id, $oldParentId, $newParentId, $refId = null )
	{
		$ids = array( $id, $oldParentId, $newParentId );
		$siteId = $this->_getContext()->getLocale()->getSiteId();

		$manager = $this->_getManager();
		$search = $manager->createSearch();
		$search->setConditions( $search->compare('==', 'catalog.id', $ids ) );

		foreach ( $manager->searchItems( $search ) as $item )
		{
			if ( $item->getSiteId() != $siteId  ) {
				throw new MShop_Exception( sprintf( 'Site can not be moved. Site ID of site differs from present site id, site ID of existing parent site or site id of new parent site.' ) );
			}
		}

		parent::moveItem( $id, $oldParentId, $newParentId, $refId );
	}
}
