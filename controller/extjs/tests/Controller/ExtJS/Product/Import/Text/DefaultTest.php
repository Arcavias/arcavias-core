<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Product_Import_Text_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_testdir;
	protected $_testfile;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_Import_Text_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$this->_testdir = $context->getConfig()->get( 'controller/extjs/product/import/text/default/uploaddir', './tmp' );
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Product_Import_Text_Default( $context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetServiceDescription()
	{
		$desc = $this->_object->getServiceDescription();
		$this->assertInternalType( 'array', $desc );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.importFile'] ) );
	}


	public function testImportFile()
	{
		$context = TestHelper::getContext();
		$filename = 'product-import-test.xlsx';

		$phpExcel = new PHPExcel();
		$phpExcel->setActiveSheetIndex(0);
		$sheet = $phpExcel->getActiveSheet();

		$sheet->setCellValueByColumnAndRow( 0, 2, 'en' );
		$sheet->setCellValueByColumnAndRow( 0, 3, 'en' );
		$sheet->setCellValueByColumnAndRow( 0, 4, 'en' );
		$sheet->setCellValueByColumnAndRow( 0, 5, 'en' );
		$sheet->setCellValueByColumnAndRow( 0, 6, 'en' );
		$sheet->setCellValueByColumnAndRow( 0, 7, 'en' );

		$sheet->setCellValueByColumnAndRow( 1, 2, 'product' );
		$sheet->setCellValueByColumnAndRow( 1, 3, 'product' );
		$sheet->setCellValueByColumnAndRow( 1, 4, 'product' );
		$sheet->setCellValueByColumnAndRow( 1, 5, 'product' );
		$sheet->setCellValueByColumnAndRow( 1, 6, 'product' );
		$sheet->setCellValueByColumnAndRow( 1, 7, 'product' );

		$sheet->setCellValueByColumnAndRow( 2, 2, 'ABCD' );
		$sheet->setCellValueByColumnAndRow( 2, 3, 'ABCD' );
		$sheet->setCellValueByColumnAndRow( 2, 4, 'ABCD' );
		$sheet->setCellValueByColumnAndRow( 2, 5, 'ABCD' );
		$sheet->setCellValueByColumnAndRow( 2, 6, 'ABCD' );
		$sheet->setCellValueByColumnAndRow( 2, 7, 'ABCD' );

		$sheet->setCellValueByColumnAndRow( 3, 2, 'default' );
		$sheet->setCellValueByColumnAndRow( 3, 3, 'default' );
		$sheet->setCellValueByColumnAndRow( 3, 4, 'default' );
		$sheet->setCellValueByColumnAndRow( 3, 5, 'default' );
		$sheet->setCellValueByColumnAndRow( 3, 6, 'default' );
		$sheet->setCellValueByColumnAndRow( 3, 7, 'default' );

		$sheet->setCellValueByColumnAndRow( 4, 2, 'long' );
		$sheet->setCellValueByColumnAndRow( 4, 3, 'metadescription' );
		$sheet->setCellValueByColumnAndRow( 4, 4, 'metakeywords' );
		$sheet->setCellValueByColumnAndRow( 4, 5, 'metatitle' );
		$sheet->setCellValueByColumnAndRow( 4, 6, 'name' );
		$sheet->setCellValueByColumnAndRow( 4, 7, 'short' );

		$sheet->setCellValueByColumnAndRow( 6, 2, 'ABCD: long' );
		$sheet->setCellValueByColumnAndRow( 6, 3, 'ABCD: meta desc' );
		$sheet->setCellValueByColumnAndRow( 6, 4, 'ABCD: meta keywords' );
		$sheet->setCellValueByColumnAndRow( 6, 5, 'ABCD: meta title' );
		$sheet->setCellValueByColumnAndRow( 6, 6, 'ABCD: name' );
		$sheet->setCellValueByColumnAndRow( 6, 7, 'ABCD: short' );

		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save($filename);


		$params = new stdClass();
		$params->site = $context->getLocale()->getSite()->getCode();
		$params->items = $filename;

		$this->_object->importFile( $params );


		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'product' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'ABCD:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = array();
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$listManager = $productManager->getSubManager( 'list' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.list.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'product.list.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}


		foreach( $textItems as $item ) {
			$this->assertEquals( 'ABCD:', substr( $item->getContent(), 0, 5 ) );
		}

		$this->assertEquals( 6, count( $textItems ) );
		$this->assertEquals( 6, count( $listItems ) );

		if( file_exists( $filename ) !== false ) {
			throw new Exception( 'Import file was not removed' );
		}
	}


	public function testUploadFile()
	{
		$context = TestHelper::getContext();
		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );


		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->_testfile,
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testfile;
		$params->site = $context->getLocale()->getSite()->getCode();

		$result = $this->_object->uploadFile( $params );

		$this->assertTrue( file_exists( $result['items'] ) );
		unlink( $result['items'] );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'job.label' => 'file.txt' ) ) ) ),
		);

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );

		$deleteParams = (object) array(
			'site' => 'unittest',
			'items' => $result['items'][0]->{'job.id'},
		);

		$jobController->deleteItems( $deleteParams );

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testUploadFileExceptionNoUploadFile()
	{
		$context = TestHelper::getContext();

		$_FILES = array();

		$params = new stdClass();
		$params->items = 'file.txt';
		$params->site = $context->getLocale()->getSite()->getCode();

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->uploadFile( $params );
	}

}
