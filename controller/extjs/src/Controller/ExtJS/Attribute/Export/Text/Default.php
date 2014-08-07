<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */



/**
 * ExtJS attribute text export controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Attribute_Export_Text_Default
	extends Controller_ExtJS_Common_Load_Text_Abstract
	implements Controller_ExtJS_Common_Load_Text_Interface
{


	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Attribute_Export_Text' );
	}


	/**
	 * Creates a new job to export a file.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function createJob( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dir = $config->get( 'controller/extjs/attribute/export/text/default/exportdir', 'uploads' );

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$languages = ( count( $lang ) > 0 ) ? implode( $lang, '-' ) : 'all';

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Attribute text export: '. $languages,
					'job.method' => 'Attribute_Export_Text.exportFile',
					'job.parameter' => array(
						'site' => $params->site,
						'items' => $items,
						'lang' => $params->lang,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $context );
		$jobController->saveItems( $result );

		return array(
			'items' => $items,
			'success' => true,
		);
	}


	/**
	 * Exports content files in container.
	 *
	 * @param stdClass $params Object containing the properties, e.g. the list of attribute IDs
	 */
	public function exportFile( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );
		$context = $this->_getContext();

		$items = (array) $params->items;
		$lang = ( property_exists( $params, 'lang' ) ) ? (array) $params->lang : array();

		$config = $context->getConfig();

		/** controller/extjs/attribute/export/text/default/exportdir
		 * Directory where exported files of attribute texts are stored
		 *
		 * All exported files are stored in this file system directory directory.
		 *
		 * The export directory must be relative to the "basedir" configuration
		 * option. If
		 *
		 *  /var/www/test
		 *
		 * is the configured base directory and the export directory should be
		 * located in
		 *
		 *  /var/www/test/htdocs/files/exports
		 *
		 * then the configuration for the export directory must be
		 *
		 *  htdocs/files/exports
		 *
		 * Avoid leading and trailing slashes for the export directory string!
		 *
		 * @param string Relative path in the file system
		 * @since 2014.03
		 * @category Developer
		 * @see controller/extjs/media/default/basedir
		 */
		$dir = $config->get( 'controller/extjs/attribute/export/text/default/exportdir', 'uploads' );

		/** controller/extjs/attribute/export/text/default/dirperms
		 * Directory permissions used when creating the directory if it doesn't exist
		 *
		 * The representation of the permissions is in octal notation (using 0-7)
		 * with a leading zero. The first number after the leading zero are the
		 * permissions for the web server creating the directory, the second is
		 * for the primary group of the web server and the last number represents
		 * the permissions for everyone else.
		 *
		 * You should use 0700 for the permissions as the web server needs
		 * to write into the new directory but the files shouldn't be publicall
		 * available. The group permissions are only important if you plan to
		 * retrieve the files directly via FTP or by other means because then
		 * you need to be able to read and manage those files. In this case use
		 * 0770 as permissions.
		 *
		 * A more detailed description of the meaning of the Unix file permission
		 * bits can be found in the Wikipedia article about
		 * {@link https://en.wikipedia.org/wiki/File_system_permissions#Numeric_notation file system permissions}
		 *
		 * @param integer Octal Unix permission representation
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$perms = $config->get( 'controller/extjs/attribute/export/text/default/dirperms', 0700 );

		/** controller/extjs/attribute/export/text/default/downloaddir
		 * Directory where the exported files can be found through the web
		 *
		 * The exported files are stored in this directory.
		 *
		 * The download directory must be relative to the document root of your
		 * virtual host. If the document root is
		 *
		 *  /var/www/test/htdocs
		 *
		 * and the exported files will be in
		 *
		 *  /var/www/test/htdocs/files/exports
		 *
		 * then the configuration for the download directory must be
		 *
		 *  files/exports
		 *
		 * Avoid leading and trailing slashes for the export directory string!
		 *
		 * @param string Relative path in the URL
		 * @since 2014.03
		 * @category Developer
		 */
		$downloaddir = $config->get( 'controller/extjs/attribute/export/text/default/downloaddir', 'uploads' );

		$foldername = 'attribute-text-export_' . date('Y-m-d_H:i:s') . '_' . md5( time() . getmypid() );
		$tmpfolder = $dir . DIRECTORY_SEPARATOR . $foldername;

		if( is_dir( $dir ) === false && mkdir( $dir, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $dir, $perms ) );
		}

		$context->getLogger()->log( sprintf( 'Create export directory for attribute IDs: %1$s', implode( ',', $items ) ), MW_Logger_Abstract::DEBUG );

		$filename = $this->_exportData( $items, $lang, $tmpfolder );
		$downloadFile = $downloaddir . DIRECTORY_SEPARATOR . basename( $filename );

		return array(
			'file' => '<a href="'.$downloadFile.'">' . $context->getI18n()->dt( 'controller/extjs', 'Download' ) . '</a>',
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		return array(
			'Attribute_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "array","name" => "lang","optional" => true ),
				),
				"returns" => "",
			),
		);
	}


	/**
	 * Gets all data and exports it to the content files.
	 *
	 * @param array $ids List of item IDs that should be part of the document
	 * @param array $lang List of languages to export (empty array for all)
	 * @param string $filename Temporary folder name where to write export files
	 * @return string Path to the exported file
	 */
	protected function _exportData( array $ids, array $lang, $filename )
	{
		$context  = $this->_getContext();
		$manager = MShop_Locale_Manager_Factory::createManager( $context );
		$globalLanguageManager = $manager->getSubManager( 'language' );

		$search = $globalLanguageManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'locale.language.id') ) );

		if( !empty( $lang ) ) {
			$search->setConditions( $search->compare( '==', 'locale.language.id', $lang ) );
		}

		/** controller/extjs/attribute/export/text/default/container/type
		 * Container file type storing all language files for the exported texts
		 *
		 * When exporting texts, one file or content object is created per
		 * language. All those files or content objects are put into one container
		 * file so editors don't have to download one file for each language.
		 *
		 * The container file types that are supported by default are:
		 * * Zip
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Container file type
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/attribute/export/text/default/container/format
		 * Format of the language files for the exported texts
		 *
		 * The exported texts are stored in one file or content object per
		 * language. The format of that file or content object can be configured
		 * with this option but most formats are bound to a specific container
		 * type.
		 *
		 * The formats that are supported by default are:
		 * * CSV (requires container type "Zip")
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Content file type
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/attribute/export/text/default/container/options
		 * Options changing the output format of the exported texts
		 *
		 * Each content format may support some configuration options to change
		 * the output for that content type.
		 *
		 * The options for the CSV content format are:
		 * * csv-separator, default ','
		 * * csv-enclosure, default '"'
		 * * csv-escape, default '"'
		 * * csv-lineend, default '\n'
		 *
		 * For format options provided by other container types implemented by
		 * extensions, please have a look into the extension documentation.
		 *
		 * @param array Associative list of options with the name as key and its value
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$containerItem = $this->_createContainer( $filename, 'controller/extjs/attribute/export/text/default/container' );
		$actualLangid = $context->getLocale()->getLanguageId();
		$start = 0;

		do
		{
			$result = $globalLanguageManager->searchItems( $search );

			foreach ( $result as $item )
			{
				$langid = $item->getId();

				$contentItem = $containerItem->create( $langid );
				$contentItem->add( array( 'Language ID', 'Attribute type', 'Attribute code', 'List type', 'Text type', 'Text ID', 'Text' ) );
				$context->getLocale()->setLanguageId( $langid );
				$this->_addLanguage( $contentItem, $langid, $ids );

				$containerItem->add( $contentItem );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$context->getLocale()->setLanguageId( $actualLangid );
		$containerItem->close();

		return $containerItem->getName();
	}


	/**
	 * Adds data for the given language.
	 *
	 * @param MW_Container_Content_Interface $contentItem Content item
	 * @param string $langid Language id
	 * @param array $ids List of of item ids whose texts should be added
	 */
	protected function _addLanguage( MW_Container_Content_Interface $contentItem, $langid, array $ids )
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->_getContext() );
		$search = $manager->createSearch();

		if( !empty( $ids ) ) {
			$search->setConditions( $search->compare( '==', 'attribute.id', $ids ) );
		}

		$sort = array(
			$search->sort( '+', 'attribute.siteid' ),
			$search->sort( '+', 'attribute.domain' ),
			$search->sort( '-', 'attribute.code' ),
			$search->sort( '-', 'attribute.typeid' ),
		);
		$search->setSortations( $sort );

		$start = 0;

		do
		{
			$result = $manager->searchItems( $search, array('text') );

			foreach( $result as $item ) {
				$this->_addItem( $contentItem, $item, $langid );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );
	}


	/**
	 * Adds all texts belonging to an attribute item.
	 *
	 * @param MW_Container_Content_Interface $contentItem Content item
	 * @param MShop_Attribute_Item_Interface $item product item object
	 * @param string $langid Language id
	 */
	protected function _addItem( MW_Container_Content_Interface $contentItem, MShop_Attribute_Item_Interface $item, $langid )
	{
		$listTypes = array();
		foreach( $item->getListItems( 'text' ) as $listItem ) {
			$listTypes[ $listItem->getRefId() ] = $listItem->getType();
		}

		foreach( $this->_getTextTypes( 'attribute' ) as $textTypeItem )
		{
			$textItems = $item->getRefItems( 'text', $textTypeItem->getCode() );

			if( !empty( $textItems ) )
			{
				foreach( $textItems as $textItem )
				{
					$listType = ( isset( $listTypes[ $textItem->getId() ] ) ? $listTypes[ $textItem->getId() ] : '' );
					$items = array( $langid, $item->getType(), $item->getCode(), $listType, $textTypeItem->getCode(), '', '' );

					// use language of the text item because it may be null
					if( ( $textItem->getLanguageId() == $langid || is_null( $textItem->getLanguageId() ) )
						&& $textItem->getTypeId() == $textTypeItem->getId() )
					{
						$items[0] = $textItem->getLanguageId();
						$items[5] = $textItem->getId();
						$items[6] = $textItem->getContent();
					}
				}
			}
			else
			{
				$items = array( $langid, $item->getType(), $item->getCode(), 'default', $textTypeItem->getCode(), '', '' );
			}

			$contentItem->add( $items );
		}
	}
}