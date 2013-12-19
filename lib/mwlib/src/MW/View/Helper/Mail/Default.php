<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for creating e-mails.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Mail_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_message;


	/**
	 * Initializes the Mail view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param MW_Mail_Message_Interface $message E-mail message object
	 */
	public function __construct( $view, MW_Mail_Message_Interface $message )
	{
		parent::__construct( $view );

		$this->_message = $message;
	}


	/**
	 * Returns the e-mail message object.
	 *
	 * @return MW_Mail_Message_Interface E-mail message object
	 */
	public function transform()
	{
		return $this->_message;
	}
}
