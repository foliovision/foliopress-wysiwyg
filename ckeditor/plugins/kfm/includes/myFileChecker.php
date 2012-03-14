<?php
// original code from pBaran Foliovision
function mykfm_FileExistsUnderDirTree( $strName, $strRoot, $aBannedFolders ){
	$aFiles = scandir( $strRoot );
	if( $aFiles == false ) return false;
	
	foreach( $aFiles as $strFile ){
		if( is_dir( $strRoot . '/' . $strFile ) && !in_array( $strFile, $aBannedFolders, false ) ){
			$bRet = false;
			if( strcasecmp( $strFile, "." ) != 0 && strcasecmp( $strFile, ".." ) != 0 ) $bRet = mykfm_FileExistsUnderDirTree( $strName, $strRoot . '/' . $strFile );
			if( $bRet == true ) return true;
		}else{
			if( strcasecmp( $strName, $strFile ) == 0 ) return true;
		}
	}
	
	return false;
}

function mykfm_CreateTempNameForFile( $strName, $strPath ){
	$i = 1;
	// changed zUhrikova 9/2/2010 
	
   $info = pathinfo($strName);
   $file_name =  basename($strName,'.'.$info['extension']);

	while( file_exists( $strPath . '/' .  $file_name . '-' . $i . '.'.$info['extension']) ){
		$i++;
	// end of change zUhrikova
	}
	$strFile = $file_name . '-' . $i . '.'.$info['extension'];
	return $strFile;
}

?>