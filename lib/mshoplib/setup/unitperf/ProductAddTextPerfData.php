<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds performance records to product table.
 */
class MW_Setup_Task_ProductAddTextPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddColorPerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert text data and product/text relations.
	 */
	protected function _process()
	{
		$this->_msg( 'Adding product text performance data', 0 );


		$attribute = array(
			'plain', 'checked', 'striped', 'curled', 'colored', 'bubbled', 'geometric', 'quilted', 'pimpled', 'dotted',
			'light', 'heavy', 'simple', 'clear', 'cool', 'thin', 'thick', 'airy', 'breezy', 'blowy',
			'dark', 'gloomy', 'shiny', 'big', 'tight', 'alpaca', 'horsehair', 'viscose', 'polyester', 'mohair',
			'brocade', 'chiffon', 'cotton', 'damask', 'denim', 'flannel', 'jersey', 'satin', 'seersucker', 'tweed',
			'linen', 'satin', 'silk', 'sheen', 'velvet', 'wool', 'sisal', 'jute', 'angora', 'cashmere',
		);
		$articles = array(
			'shirt', 'skirt', 'jacket', 'pants', 'socks', 'blouse', 'slip', 'sweater', 'dress', 'top',
			'anorak', 'babydoll', 'swimsuit', 'trunks', 'bathrobe', 'beret', 'bra', 'bikini', 'blazer', 'bodysuit',
			'bolero', 'bowler', 'trousers', 'bustier', 'cape', 'catsuit', 'chucks', 'corduroys', 'corsage', 'cutaway',
			'lingerie', 'tricorn', 'bow tie', 'tails', 'leggings', 'galoshes', 'string', 'belt', 'hotpants', 'hat',
			'jumpsuit', 'jumper', 'caftan', 'hood', 'kimono', 'headscarf', 'scarf', 'corset', 'costume', 'tie',
			'cummerbund', 'robe', 'underpants', 'dungarees', 'undershirt', 'camisole', 'mantle', 'bodice', 'topless', 'moonboots',
			'cap', 'nightie', 'negligee', 'overalls', 'parka', 'poncho', 'bloomers', 'pumps', 'pajamas', 'farthingale',
			'sari', 'veil', 'apron', 'swimsuit', 'shorts', 'tuxedo', 'stocking', 'suspender', 'tanga', 'tankini',
			'toga', 'tunic', 'turban', 'jerkin', 'coat', 'suit', 'vest', 'gloves', 'bag', 'briefcase',
			'shoes', 'sandals', 'flip-flops', 'ballerinas', 'slingbacks', 'clogs', 'moccasins', 'sneakers', 'boots', 'slippers',
		);
		$size = array(
			'',
			'(9XS)', '(8XS)', '(7XS)', '(6XS)', '(5XS)', '(4XS)', '(3XS)', '(2XS)', '(XS)', '(S)',
			'(SS-M)', '(S-M)', '(M)', '(M-L)', '(M-LL)',
			'(L)', '(XL)', '(2XL)', '(3XL)', '(4XL)', '(5XL)', '(6XL)', '(7XL)', '(8XL)', '(9XL)',
		);


		$context = $this->_getContext();
		$textManager = MShop_Factory::createManager( $context, 'text' );
		$attrManager = MShop_Factory::createManager( $context, 'attribute' );
		$productListManager = MShop_Factory::createManager( $context, 'product/list' );


		$attrSearch = $attrManager->createSearch();
		$attrSearch->setConditions( $attrSearch->compare( '==', 'attribute.type.code', 'color' ) );
		$attrSearch->setSlice( 0, 1000 );

		$attrIds = $attrManager->searchItems( $attrSearch );


		$textListItem = $this->_getProductListItem( 'text', 'default' );
		$textTypes = $this->_getTextTypeIds();

		$textItem = $textManager->createItem();
		$textItem->setLanguageId( 'en' );
		$textItem->setDomain( 'product' );
		$textItem->setStatus( 1 );


		$search = $productListManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.domain', 'attribute' ),
			$search->compare( '==', 'product.list.type.code', 'variant' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'product.list.id' ) ) );


		$this->_txBegin();

		$start = 0;

		do
		{
			$result = $productListManager->searchItems( $search );

			foreach ( $result as $listItem )
			{
				$refId = $listItem->getRefId();

				if( !isset( $attrIds[$refId] ) ) {
					continue;
				}

				$id = $listItem->getParentId();
				$text = $attrIds[$refId]->getName() . ' ' . current( $attribute ) . ' ' . current( $articles ) . ' ' . current( $size );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['name'] );
				$textItem->setLabel( $text );
				$textItem->setContent( $text );
				$textManager->saveItem( $textItem );

				$textListItem->setId( null );
				$textListItem->setParentId( $id );
				$textListItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $textListItem, false );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['short'] );
				$textItem->setLabel( 'Short ' . $text );
				$textItem->setContent( 'Short description for ' . $text );
				$textManager->saveItem( $textItem );

				$textListItem->setId( null );
				$textListItem->setParentId( $id );
				$textListItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $textListItem, false );


				$textItem->setId( null );
				$textItem->setTypeId( $textTypes['long'] );
				$textItem->setLabel( 'Long ' . $text );
				$textItem->setContent( 'Long description for ' . $text . '. This may include some "lorem ipsum" text' );
				$textManager->saveItem( $textItem );

				$textListItem->setId( null );
				$textListItem->setParentId( $id );
				$textListItem->setRefId( $textItem->getId() );
				$productListManager->saveItem( $textListItem, false );


				if( current( $attribute ) === false )
				{
					reset( $attribute );
					next( $articles );

					if( current( $articles ) === false ) {
						reset( $articles );
						next( $size );

						if( current( $size ) === false ) {
							reset( $size );
						}
					}
				}
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getTextTypeIds()
	{
		$textTypeManager = MShop_Factory::createManager( $this->_getContext(), 'text/type' );

		$search = $textTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'text.type.domain', 'product' ),
			$search->compare( '==', 'text.type.code', array( 'name', 'short', 'long' ) ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$types = $textTypeManager->searchItems( $search );

		$textTypes = array();
		foreach( $types as $type ) {
			$textTypes[ $type->getCode() ] = $type->getId();
		}

		return $textTypes;
	}
}
