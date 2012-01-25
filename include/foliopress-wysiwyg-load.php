<?php
/**
 *	Simple functions to automaticaly load Skins and ToolbarSets from FCKEditor
 *
 *	@package foliopress-wysiwyg
 */
 
require_once( 'foliovision-code-parser.php' );

function fp_wysiwyg_load_fck_toolbars( $strConfigPath ){
	$objParser = new FVCodeParser( 'JavaScript', array( 'line' => array( '//' ), 'block' => array( array( 'start' => '/*', 'end' => '*/' ) ) ), ';', $strConfigPath );
	
	$aToolbars = $objParser->FindUncommentedCode( 'FCKConfig.ToolbarSets' );
	$aReturn = array();
	foreach( $aToolbars as $aItem ) $aReturn[] = $objParser->GetIndexFromArray( $aItem['text'] );
	
	return $aReturn;
}

/**
 *	Function should check if folder is regular FCKEditor skin
 *
 *	@param string $strDir Path to directory that should be plugin.
 *	@return bool true if directory contains regular FCKEditor skin false otherwise
 */ 
function fp_wysiwyg_is_regular_fck_skin( $strDir ){
	///TODO: Code this;
	return true;
}

/**
 *	Returns directories names that should be regular plugins for FCKEditor
 *
 *	@param string $strFCKSkinsPath Path to folder where skins for FCKEditor are present
 *	@return array Array filled with skins names, or empty array
 */ 
function fp_wysiwyg_load_fck_items( $strFCKSkinsPath ){
	$aReturn = array();

	if( !is_dir( $strFCKSkinsPath ) ) throw new Exception( 'Directory '.$strFCKSkinsPath.' is not present !' );
		
	$aFiles = scandir( $strFCKSkinsPath );
	if( $aFiles == false ) throw new Exception( 'No '.$strFCKSkinsPath.' items present in FCKEditor !' );
		
	foreach( $aFiles as $strFile ){
		if( strcasecmp( $strFile, '.' ) == 0 || strcasecmp( $strFile, '..' ) == 0 ) continue;
		$strPath = realpath( $strFCKSkinsPath . '/' . $strFile );
		/*if( is_dir( $strPath ) && fp_wysiwyg_is_regular_fck_skin( $strPath ) )*/ $aReturn[] = basename( $strFile );
	}
	if( count( $aReturn ) <= 0 ) throw new Exception( 'No '.$strFCKSkinsPath.' items loaded !' );
		
	return $aReturn;
}


/**
 *	Outputs items of array $aItems as correct XHTML <option> tags to HTML
 *
 *	@param array $aItems Array filled with option strings 
 *	@param string $strSelected Default selected option
 */      
function fp_wysiwyg_output_options( $aItems, $strSelected ){
	
	try{
		foreach( $aItems as $strItem ){
			print( "<option value=\"$strItem\" " );
			if( $strSelected == $strItem ) print( 'selected="selected" ' );
			print( ">$strItem</option>\n" );
		}

	}catch( Exception $ex ){
		throw $ex;
	}
}

?>
