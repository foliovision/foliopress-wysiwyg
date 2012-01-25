<?php

class FVIniParser{
	var $aOptions = array();

	function __construct( $strPath = '', $bSections = false ){
		if( strlen( $strPath ) > 3 ) $this->LoadIniFile( $strPath, $bSections ); 
	}

	function LoadIniFile( $strPath, $bSections = false ){
		if( !file_exists( $strPath ) ) throw new Exception( "Ini file '$strPath' doesn't exist !" );
		$this->aOptions = parse_ini_file( $strPath, $bSections );
	}

	function SaveChangesToIni( $strPath ){
		require_once( 'foliovision-code-parser.php' );
		
		$objParser = new FVCodeParser( 'PHPINI', array( 'line' => array( ';' ), 'block' => array() ), "\n", $strPath );
		$objParser->SetAssignmentSymbol( '=' );
		
		$aVars = array();
		$aValues = array();
		foreach( array_keys( $this->aOptions ) as $strKey ){
			$aVars[] = $strKey;
			if( is_string( $this->aOptions[$strKey] ) ) $aValues[] = '"'.$this->aOptions[$strKey].'"';
			else $aValues[] = $this->aOptions[$strKey];
		}
		
		$objParser->ReassignVariablesValue( $aVars, $aValues );
		$objParser->SaveFile();
	}
	
	function CreateNewIniFile( $strPath ){
		if( file_exists( $strPath ) ){
			if( !unlink( $strPath ) ) throw new Exception( "Could not create new ini file '$strPath', old one is undeletable !" );
		}
		
		$fwrite = fopen( $strPath, "w" );
		if( false === $fwrite ) throw new Exception( "Could not create new ini file '$strPath' !" );
		try{
			foreach( array_keys( $this->aOptions ) as $strKey ){
				$strValue = $this->aOptions[$strKey];
				if( is_string( $strValue ) ) $strValue = '"' . $strValue . '"';
				if( false === fwrite( $fwrite, "\n" . $strKey . ' = ' . $strValue . "\n" ) ) throw new Exception( "Error while writing to ini file '$strPath' !" );
			}
		}catch( Exception $ex ){
			if( false != $fwrite ) fclose( $fwrite );
			throw $ex;
		}
		fclose( $fwrite );
	}


}

function ini_parser_extract_separated_values( $strValues, $strSeparator ){
	$aReturn = array();
	
	while( strlen( $strValues ) > 0 ){
		$iPos = strpos( $strValues, $strSeparator );
		if( !$iPos ){
			$aReturn[] = trim( $strValues, " \n\r\t'\"" );
			$strValues = '';
		}else{
			$aReturn[] = trim( substr( $strValues, 0, $iPos ), " \n\r\t'\"" );
			$strValues = substr( $strValues, $iPos + 1 );
		}
	}
	
	return $aReturn;
}

// this was and maybe will be used for debuging
/*print( '<html><head /><body>' );
try{
	$objIniParser = new FVIniParser(  );
	$objIniParser->LoadIniFile( "c:\\_baran\\web\\test.ini" );
	//str_replace( "\n", '<br />', print_r( $objIniParser->aOptions ) );
	print( '<br />' );
	//print_r( $objIniParser->ExtractSeparatedValues( $objIniParser->aOptions['special_thumbnail_sizes'], ',' ) );

	$objIniParser->aOptions['special_thumbnail_sizes'] = '150, 200';
	$objIniParser->aOptions['path_to_images'] = '/imagesall/';
	$objIniParser->aOptions['new'] = 200;
	$objIniParser->SaveChangesToIni( "c:\\_baran\\web\\test.ini" );

}catch( Exception $ex ){
	print( "Error caught: " . $ex->getMessage() );
}
print( '<body /><html />' );*/
?>
