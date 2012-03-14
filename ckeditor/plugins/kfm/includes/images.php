<?php
function _changeCaption($fid,$newCaption){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	$im=kfmImage::getInstance($fid);
	$im->setCaption($newCaption);
	return kfm_loadFiles($cwd_id);
}
function _getThumbnail($fileid,$width,$height){
	$im=kfmImage::getInstance($fileid);
	$im->setThumbnail($width,$height); // Already done in the Image constructor, maybe needed for Thumbnails with different sizes.
	return array($fileid,array('icon'=>$im->thumb_url,'width'=>$im->width,'height'=>$im->height,'caption'=>$im->caption));
}
function _resizeImage($fid,$width,$height){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	$im=kfmImage::getInstance($fid);
	$im->resize($width, $height);
	if($im->hasErrors())return $im->getErrors();
	return kfm_loadFiles($cwd_id);
}
function _resizeImages($fs,$width,$height){
	foreach($fs as $f)_resizeImage($f,$width,$height);
}
function _rotateImage($fid,$direction){
	$im=kfmImage::getInstance($fid);
	$im->rotate($direction);
	if($im->hasErrors())return $im->getErrors();
	return $fid;
}
function _setCaption($fid,$caption){
	
}
function _cropToOriginal($fid, $x1, $y1, $width, $height){
	$im=kfmImage::getInstance($fid);
	$im->crop($x1, $y1, $width, $height);
	if($im->hasErrors())return $im->getErrors();
	return $fid;
}
function _cropToNew($fid, $x1, $y1, $width, $height, $newname){
	global $kfm_session;
	$cwd_id=$kfm_session->get('cwd_id');
	$im=kfmImage::getInstance($fid);
	$im->crop($x1, $y1, $width, $height, $newname);
	if($im->hasErrors())return $im->getErrors();
	return kfm_loadFiles($cwd_id);
}
// Changed zUhrikova 05/02/2010 Foliovision
## This function gets image size from id
function _getImageSize( $fid ){ //was $strUrl
//	$strFile = $_SERVER['DOCUMENT_ROOT'] . $strUrl;
	$file  = kfmFile::getInstance($fid);
  	$aSize = getimagesize( $file->path );
	//	$aSize = getimagesize( $strFile );
	return $aSize;
}
// End of add		zUhrikova 05/02/2010
// Changed zUhrikova 15/02/2010 Foliovision
## This function gets image size from url
function _getImageSizeUrl( $furl ){ //was $strUrl
//	$strFile = $_SERVER['DOCUMENT_ROOT'] . $strUrl;
	$file  = kfmFile::getInstance($furl);
  	$aSize = getimagesize( $file->path );
	//	$aSize = getimagesize( $strFile );
	return $aSize;
}
### End of add		zUhrikova 15/02/2010
?>
