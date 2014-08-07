<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Template
 */


/**
 * Generic text template processing object.
 *
 * @package MW
 * @subpackage Template
 */
class MW_Template_Base implements MW_Template_Interface
{
	private $_begin = '[$]';
	private $_end = '[/$]';
	private $_text = '';


	/**
	 * Builds a template object with string and markers.
	 *
	 * @param string $text Template as text
	 * @param string $begin Marker for start sequence with '$' as wildcard
	 * @param string $end Marker for stop sequence with '$' as wildcard
	 */
	public function __construct( $text, $begin = '[$]', $end = '[/$]' )
	{
		$this->_begin = $begin;
		$this->_end = $end;
		$this->_text = $text;
	}


	/**
	 * Removes the maker and enables content in template.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function enable( $name )
	{
		$marray = array();

		foreach( (array) $name as $item )
		{
			$marray[] = str_replace( '$', $item, $this->_begin );
			$marray[] = str_replace( '$', $item, $this->_end );
		}

		$this->_text = str_replace( $marray, '', $this->_text );

		return $this;
	}


	/**
	 * Removes the content between the marker.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function disable( $name )
	{
		$list = array();

		foreach( (array) $name as $item ) {
			$list[$item] = '';
		}

		$this->substitute( $list );

		return $this;
	}


	/**
	 * Returns a new template object containing the requested part from the template.
	 *
	 * @param string $name Marker whose content should be returned
	 * @return MW_Template_Interface Subtemplate object containing the template between the given marker name
	 */
	public function get( $name )
	{
		$mbegin = str_replace( '$', $name, $this->_begin );
		$mend = str_replace( '$', $name, $this->_end );

		if( ( $begin = strpos( $this->_text, $mbegin ) ) === false )
		{
			throw new MW_Template_Exception( sprintf( 'Error finding begin of marker "%1$s" in template', $name ) );
		}

		$begin += strlen( $mbegin );

		if( ( $end = strpos( $this->_text, $mend, $begin ) ) === false )
		{
			throw new MW_Template_Exception( sprintf( 'Error finding end of marker "%1$s" in template', $name ) );
		}

		return new self( substr( $this->_text, $begin, $end - $begin ), $this->_begin, $this->_end );
	}


	public function getMarkerNames()
	{
		$matches = array();
		$regex = '/' . str_replace( '\$', '(.*)', preg_quote( $this->_begin, '/' ) ) . '/U';

		if( preg_match_all( $regex, $this->_text, $matches ) === false ) {
			throw new MW_Template_Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
		}

		return array_unique( $matches[1] );
	}


	/**
	 * Replaces a string or a list of strings.
	 *
	 * @param string[] $old String or list of strings to remove
	 * @param string|array $new String or list of strings to insert instead
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function replace( $old, $new )
	{
		$this->_text = str_replace( $old, $new, $this->_text );

		return $this;
	}


	/**
	 * Substitutes the marker by given text.
	 *
	 * @param array $substitute Array of marker names (keys) and text to substitute (values)
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function substitute( array $substitute )
	{
		foreach( $substitute as $marker => $value )
		{
			$begin = 0;
			$mbegin = str_replace( '$', $marker, $this->_begin );
			$mend = str_replace( '$', $marker, $this->_end );

			while( ( $begin = strpos( $this->_text, $mbegin, $begin ) ) !== false )
			{
				if( ( $end = strpos( $this->_text, $mend, $begin + strlen( $mbegin ) ) ) === false )
				{
					throw new MW_Template_Exception( sprintf( 'Error finding end of marker "%1$s" in template', $marker ) );
				}

				$this->_text = substr_replace( $this->_text, $value, $begin, $end + strlen( $mend ) - $begin );
			}
		}

		return $this;
	}


	/**
	 * Generates the template by replacing substrings and remove markers.
	 *
	 * @param bool $remove Remove still disabled markers from statement
	 * @return string
	 */
	public function str( $remove = true )
	{
		if( $remove === false ) {
			return $this->_text;
		}

		$matches = array();
		$text = $this->_text;

		$regex = '/' . str_replace( '\$', '(.*)', preg_quote( $this->_begin, '/' ) ) . '/U';
		if( preg_match_all( $regex, $text, $matches ) === false ) {
			throw new MW_Template_Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
		}

		$matches = array_unique( $matches[1] );
		foreach( $matches as $match )
		{
			$begin = str_replace( '\$', $match, preg_quote( $this->_begin, '/' ) );
			$end = str_replace( '\$', $match, preg_quote( $this->_end, '/' ) );

			$regex = '/' . $begin . '.*' . $end . '/smU';
			if( ( $text = preg_replace( $regex, '', $text ) ) === null ) {
				throw new MW_Template_Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
			}
		}

		return $text;
	}
}
