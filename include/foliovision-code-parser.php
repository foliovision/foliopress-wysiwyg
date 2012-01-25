<?php
/**
 * Class that has some basic functions to load source code, and change it.
 *
 * @author pBaran (Foliovision) 
 * @package Foliovision.Basic
 */

/**
 * Class that has some basic functions to load source code, and change it.
 * 
 * @package Foliovision.Basic
 * @subpackage FVCodeParser
 */
class FVCodeParser{
	var $strFile = '';
	var $strText = '';
	var $strCodeSpec = 'PHP';
	var $aCommentSigns = array( 'line' => array( '//', '#' ), 'block' => array( array( 'start' => '/*', 'end' => '*/' ) ) );
	var $aArraySpecifier = array( array( 'start' => '[', 'end' => ']' ) );
	var $strSeparator = ';';
	var $strAssign = '=';
	
	/**
	 * Basic constructor with things to specify
	 * 
	 * @param string $strType			Specifies code that will be parsed (only informative)
	 * @param array $aComments			Specifies commenting symbols that this language uses.
	 * @param string $strSep			Specifies command separator
	 * @param string $strFileToLoad		Full path to file to load
	 */
	function __construct( $strType = null, $aComments = null, $strSep = null, $strFileToLoad = null ){
		if( $strType != null ) $this->strCodeSpec = $strType;
		if( $aComments != null ) $this->aCommentSigns = $aComments;
		if( $strSep != null ) $this->strSeparator = $strSep;
		
		if( $strFileToLoad != null ){	
			$this->strFile = realpath( $strFileToLoad );
			if( !file_exists( $this->strFile ) ) throw new Exception( "Incorrect file specified: '$strFileToLoad' !" );
		
			$this->LoadFile();
		}
	}
	
	
	/**
	 * Loads content of a file into internal string
	 * 
	 * @param string $strFile			Full path to file to load
	 */
	function LoadFile( $strFile = null ){
		if( $strFile == null ) $strFile = $this->strFile;
		if( !file_exists( $strFile ) ) throw new Exception( "Incorrect file specified: '$strFile' !" );

		$this->strText = file_get_contents( $strFile );
		if( $this->strText === false ) throw new Exception( "Can not load contents of: '$strFile' !" );
		
		$this->strFile = $strFile;
	}
	
	/**
	 * Saves loaded (and maybe changed) content of some file to it
	 */
	function SaveFile(){
		if( strlen( $this->strFile ) <= 0 ) throw new Exception( 'Can not save file:   No file loaded !' );
		if( false === file_put_contents( $this->strFile, $this->strText ) ) throw new Exception( "file_put_contents wasn't able to save file '".$this->strFile."' !" );
	}
	
	/**
	 * Clears loaded file from memory
	 */
	function Clear(){
		$this->strFile = "";
		$this->strText = "";
	}
	
	/**
	 * Checks if code (specified by index of character) is commented
	 * 
	 * @param int $iPosEnd			Specifies position of first character of code
	 * 
	 * @return bool 				False if code is uncommented, true otherwise 
	 */
	function IsPositionCommented( $iPosEnd ){

		$return = true;

		try{
			$strCache = substr( $this->strText, 0, $iPosEnd + 1 );

			if( isset( $this->aCommentSigns['block'] ) ){
				foreach( $this->aCommentSigns['block'] as $aBlock ){
					$iPos = strripos( $strCache, $aBlock['start'] );
					if( !($iPos === false) ){
						$iPos = stripos( $strCache, $aBlock['end'], $iPos );
						if( $iPos === false ) return true;
					}
				}
			}

			if( isset( $this->aCommentSigns['line'] ) ){
				foreach( $this->aCommentSigns['line'] as $strLine ){
					$iPos = strripos( $strCache, $strLine );
					if( !($iPos === false) ){
						$iPos = stripos( $strCache, "\n", $iPos );
						if( $iPos === false ) return true;
					}
				}
			}

			$return = false;

		}catch( Exception $ex ){
			throw $ex;
		}

		return $return;
	}
	
	/**
	 * Extracts code from text
	 * 
	 * @param string $strText		Text to extract code from
	 * @param int $iPos				Position of first character of code to extract
	 * 
	 * @return array				Extracted code with its start and end position
	 */
	function ExtractCode( $strText, $iPos, $iCutOf ){
		///TODO: Check if separator character is in comment or not
		$iPosEnd = stripos( $strText, $this->strSeparator, $iPos );
		$aReturn = array( 'text' => substr( $strText, $iPos, $iPosEnd - $iPos ), 'start' => $iCutOf + $iPos, 'end' => $iCutOf + $iPosEnd );
		
		return $aReturn;
	}
	
	/**
	 * Finds all occurences of specified code that is not inside comments
	 * 
	 * @param string $strCode		Code to find occurences of
	 * 
	 * @return array				Array of arrays of all occurences of this code with its position [ 'text', 'start', 'end' ]
	 */
	function FindUncommentedCode( $strCode ){
		
		$return = array();
	
		$iCutOf = 0;
		$iPos = false;
		$strCache = $this->strText;
		while( true ){
			$iPos = stripos( $strCache, $strCode );
			if( $iPos === false ) return $return;
		
			$bOK = true;
			$iNextCh = ord( substr( $strCache, $iPos + strlen( $strCode ), 1 ) );
			
			if( $iNextCh >= 65 && $iNextCh <= 90 ) $bOK = false;
			if( $iNextCh >= 97 && $iNextCh <= 122 ) $bOK = false;
			if( $iNextCh >= 48 && $iNextCh <= 57 ) $bOK = false;
			if( $iNextCh == 95 ) $bOK = false;
			
			if( !$this->IsPositionCommented( $iPos + $iCutOf ) && $bOK ){
				$return[] = $this->ExtractCode( $strCache, $iPos, $iCutOf );
				$strCache = substr( $strCache, $iPos + 1 );
				$iCutOf += $iPos + 1; 
			}else{
				$strCache = substr( $strCache, $iPos + 1 );
				$iCutOf += $iPos + 1;
			}
		}

		return $return;
	}
	
	/**
	 * Sets border for array indexes
	 * 
	 * @param array $aArraySpec		Array of special structure where array borders are specified
	 */
	function SetArraySpecifier( $aArraySpec ){
		$this->aArraySpecifier = $aArraySpec;
	}
	
	/**
	 * Returns array index striped from \n \t \r ' " (only first apearance of any array is taken into consideration)
	 * 
	 * @param string $strCde		Code which has an array in itself
	 * 
	 * @return string				Index of array found in code
	 */
	function GetIndexFromArray( $strCode ){
		///TODO: Check if array index is uncommented
		$strReturn = '';

		foreach( $this->aArraySpecifier as $aSpec ){
			$iPosStart = stripos( $strCode, $aSpec['start'] );
			if( $iPosStart === false ) continue;
			
			$iPosEnd = stripos( $strCode, $aSpec['end'] );
			if( $iPosEnd === false ) continue;
			
			$strReturn = substr( $strCode, $iPosStart + 1, $iPosEnd - $iPosStart - 1 );
			$strReturn = trim( $strReturn, " \n\t\r'\"" );
			break;
		}

		return $strReturn;
	}

	/**
	 * Sets symbol for assignment in current language
	 * 
	 * @param string $strAssignment		Assignment symbol
	 */
	function SetAssignmentSymbol( $strAssignment ){
		$this->strAssign = $strAssignment;
	}
	
	/**
	 * Reassigns some variable found with @see FindUncommentedCode
	 * 
	 * @param array $aVariable		Array returned by @see FindUncommentedCode
	 * @param string $strValue		New value to assign this value
	 */
	function ReassignVariableValue( $aVariable, $strValue, $strVariable ){
		if( !is_array( $aVariable ) ) throw new Exception( 'Fatal error !' );
		
		$iCEnd = 0;
		$strCache = '';
		foreach( $aVariable as $aVar ){
			$iPos = stripos( $aVar['text'], $this->strAssign );
			$strVar = substr( $aVar['text'], $iPos + 1 );
			
			$strTest = substr( $aVar['text'], 0, $iPos );
			$strTest = trim( $strTest, " \t\n\r" );
			
			if( 0 == strcmp( $strTest, $strVariable ) ){
				$strCache .= substr( $this->strText, $iCEnd, $aVar['start'] - $iCEnd );
				$strCache .= str_replace( $strVar, ' ' . $strValue, $aVar['text'] );
				$iCEnd = $aVar['end'];
			}
		}
		$strCache .= substr( $this->strText, $iCEnd );
			
		$this->strText = $strCache;
	}
	
	function ReassignVariablesValue( $objVariable, $objValue ){
		if( strlen( $this->strText ) <= 0 ) throw new Exception( 'No file loaded to assign value to !' );
		
		if( is_array( $objVariable ) && is_array( $objValue ) ){
			if( count( $objVariable ) <> count( $objValue ) ) throw new Exception( 'Invalid input variables in ReassignVariablesValue' );
			
			for( $i=0; $i<count( $objVariable ); $i++ ){
				$aVar = $this->FindUncommentedCode( $objVariable[$i] );
				if( 0 == count( $aVar ) ) $this->AddVariableDef( $objVariable[$i], $objValue[$i] );
				else $this->ReassignVariableValue( $aVar, $objValue[$i], $objVariable[$i] );
			}

		}elseif( !is_array( $objVariable ) && !is_array( $objValue ) ){
			$aVar = $this->FindUncommentedCode( $objVariable );
			if( 0 == count( $aVar ) ) $this->AddVariableDef( $objVariable, $objValue );
			else $this->ReassignVariableValue( $aVar, $objValue, $objVariable );

		}else throw new Exception( 'Invalid input variables in ReassignVariablesValue' );

	}
	
	function AddVariableDef( $strVariable, $strValue ){
		$this->strText .= "\n" . $strVariable . ' ' . $this->strAssign . ' ' . $strValue . $this->strSeparator;
	}
	
	function ExtractArrayFromSimpleDefine( $strArray ){
		$iPos = stripos( 'array(', $strArray );
		$strArray = substr( $strArray, $iPos + 6 );
		
		$aReturn = array();
		while( strlen( $strArray ) > 0 ){
			$iPos = 0;
			$iLen = strlen( $strArray );
			while( (strcmp( $strArray[$iPos], ',' ) != 0 && strcmp( $strArray[$iPos], ')' ) != 0 ) && $iPos < $iLen ) $iPos++;
			if( $iPos == $iLen ) $strArray = '';
			else{
				$strValue = substr( $strArray, 0, $iPos );
				$strArray = substr( $strArray, $iPos + 1 );
				$aReturn[] = trim( $strValue, " \n\t\r'\"" );
			}
		}
		
		return $aReturn;
	}
}

?>
