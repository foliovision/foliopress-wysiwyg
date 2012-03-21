<?php
/**
 * KFM - Kae's File Manager
 *
 * upload.php - uploads a file and adds it to the db
 *
 * @category None
 * @package  None
 * @author   Kae Verens <kae@verens.com>
 * @author   Benjamin ter Kuile <bterkuile@gmail.com>
 * @license  docs/license.txt for licensing
 * @link     http://kfm.verens.com/
 */

require_once 'initialise.php';

/// Added		zUhrikova		5/02/2010		Foliovision
include('includes/image-non-class.php');
$bRename = false;
$bRenaming = false;
$errors = array();
require_once( 'includes/myFileChecker.php' );

if ($kfm->setting('allow_file_upload')) {
	$fids     = array();
	$files    = array();
	$fdata    = isset($_FILES['kfm_file'])?$_FILES['kfm_file']:$_FILES['Filedata'];
	if(is_array($fdata['name'])){
		for($i=0;$i<count($fdata['name']);++$i){
			$files[]=array(
				'name'    =>$fdata['name'][$i],
				'tmp_name'=>$fdata['tmp_name'][$i],
			);
		}
	}
	else $files[]=$fdata;
	$replace  = isset($_REQUEST['fid'])?(int)$_REQUEST['fid']:0;
	//$replace  = isset($_REQUEST['rename_to'])?1:0;
	foreach($files as $file){
		$tmpname  = $file['tmp_name'];
		// { filename
		$filename = $file['name'];
		if(isset($_REQUEST['rename_to'])){
        $filename=$_REQUEST['rename_to'];
        $bRenaming = true;
        if(isset($_REQUEST['tmp_name']))$tmpname=$_REQUEST['tmp_name'];
      }
      /// Addition		pBaran		13/03/2008-19/03/2008		Foliovision
	   // files uploaded will be striped of every stupid characters
	   $filename = preg_replace( '/[^\w\d\.\-\s_]/' , '' , $filename );
	   // files uploaded will have their names striped of underscores and spaces and replaced with hyphen
	   $filename = str_replace( array( '_', ' ', '--' ), '-', $filename );
	   /// End of addition		pBaran		13/03/2008-19/03/2008

		// }
		// { directory
		if(isset($_REQUEST['directory_name'])){
			$dirs				   = explode(DIRECTORY_SEPARATOR, trim($_REQUEST['directory_name'], ' '.DIRECTORY_SEPARATOR));
			$subdir				 = $user_root_dir;
			$startup_sequence_array = array();
			foreach ($dirs as $dirname) {
				$parent=$subdir;
				$subdir = $parent->getSubdir($dirname);
				if(!$subdir){
					kfm_createDirectory($parent->id,$dirname);
					$subdir=$parent->getSubdir($dirname);
				}
				$kfm_startupfolder_id	 = $subdir->id;
			}
			$kfm_session->set('cwd_id', $kfm_startupfolder_id);
			$cwd=$kfm_startupfolder_id;
		}
		else $cwd = $kfm_session->get('cwd_id');
		if(isset($_REQUEST['cwd']) && $_REQUEST['cwd']>0)$cwd=$_REQUEST['cwd'];
		// }
		if(!$cwd) $errors[] = kfm_lang('CWD not set');
		else {
			$toDir = kfmDirectory::getInstance($cwd);
			if($replace){
				$replace_file = kfmFile::getInstance($replace);
				$to           = $replace_file->path;
				if($replace_file->isImage()) $replace_file->deleteThumbs();
			}
			else $to = $toDir->path().''.$filename;
         // add zUhrikova
			if ($bRenaming) $sTestFile = $toDir->path().'/'.$tmpname;
			else $sTestFile = $tmpname;
         // end add zUhrikova
         if (!is_file($sTestFile)) $errors[] = 'No file uploaded.';//Dir->path().'/'.$tmpname
			else if (!kfmFile::checkName($filename)) {
				$errors[] = 'The filename: '.$filename.' is not allowed';
			}
			else if(in_array(kfmFile::getExtension($filename),$kfm->setting('banned_upload_extensions'))){
				$errors[] = 'The extension: '.kfmFile::getExtension($filename).' is not allowed';
			}
			// { check to see if it's an image, and if so, is it bloody massive
			if(in_array(kfmFile::getExtension($filename),array('jpg', 'jpeg', 'gif', 'png', 'bmp'))){
				//list($width, $height, $type, $attr)=getimagesize($tmpname);
			//	if($width>$toDir->maxWidth() || $height>$toDir->maxHeight()){
			  if(file_exists($tmpname)){
            /// change zUhrikova 9/2/2010 Foliovision
				   $aImageInfo = getimagesize($tmpname);
				   $freemem = (str_replace('M','',ini_get('memory_limit') )*1048576 - memory_get_usage());
      			//	fix for PNG
      			if( $aImageInfo['channels']=='' )
      				$aImageInfo['channels'] = 3;
      			$imagemem = Round(($aImageInfo[0] * $aImageInfo[1] * $aImageInfo['bits'] * $aImageInfo['channels'] / 8 + Pow(2, 16)) * 2);
      			if($freemem <= $imagemem) {
      				$bTooHot = true;
      				$errors[] = "The file size exceeds available memory. File cannot be uploaded, please resize it locally.";
      				unlink( $tmpname );
      			}
      			else {		
       				if($aImageInfo[0] > $toDir->maxWidth() || $aImageInfo[1] > $toDir->maxHeight()) {
      					$iRatio = (int) $aImageInfo[1]/$aImageInfo[0];
      					if($aImageInfo[0] > $aImageInfo[1]) {
      						$iNewHeight = $toDir->maxWidth() * $iRatio;	
      						$iNewWidth = $toDir->maxWidth();
      					} else {
      						$iNewWidth = $toDir->maxHeight() / $iRatio;
      						$iNewHeight = $toDir->maxHeight();
      					}
      					FV_CreateResizedCopy($tmpname, $tmpname, $iNewWidth, $iNewHeight, $aImageInfo);
      				}
      			}
					//$errors[] = 'Please do not upload images which are larger than '.$toDir->maxWidth().'x'.$toDir->maxHeight();
					// end of change zUhrikova 9/2/2010
				}
			}
			// }
		}
		if ($cwd==$kfm->setting('root_folder_id') && !$kfm->setting('allow_files_in_root')) $errors[] = 'Cannot upload files to the root directory';
		if (!$replace && file_exists($to)){
		// changed zUhrikova 9/2/2010 Folivision
		  if( is_uploaded_file( $tmpname ) ){
				$strRenameTemp = mykfm_CreateTempNameForFile( $filename, $toDir->path() );
				move_uploaded_file( $tmpname, $toDir->path() . '/' . $strRenameTemp );
				chmod( $toDir->path() . '/' . $strRenameTemp, octdec( '0' . $kfm->setting('default_upload_permission') ) );
			}else $strRenameTemp = basename( $tmpname );

			$bRename = true;
			// end of change zUhrikova 9/2/2010
         $errors[] = 'File with that name already exists. Your file has been renamed to ' . $strRenameTemp;
      }
		if (!count($errors)) {
		   if ($bRenaming) kfm_move_uploaded_file( $filename, $to, $toDir->path().$tmpname );
         else kfm_move_uploaded_file( $filename, $to, $tmpname );
         //move_uploaded_file($tmpname, $to);//$toDir->path().
   		if (!file_exists($to)) $errors[] = kfm_lang('failedToSaveTmpFile' , $toDir->path().$tmpname, $to);//file_exists($to)
   		else 
            if ($kfm->setting('only_allow_image_upload') && !getimagesize($to)) {
   			   $errors[] = 'only images may be uploaded';
   			   unlink($to);
   		   }
			   else {
   				chmod($to, octdec('0'.$kfm->setting('default_upload_permission')));
   				$fid  = kfmFile::addToDb($filename, $kfm_session->get('cwd_id'));
   				$file = kfmFile::getInstance($fid);
   				$bRename = false;
   				if (function_exists('exif_imagetype')) {
   					$imgtype = @exif_imagetype($to);
   					if ($imgtype) {
   						$file    = kfmImage::getInstance($file);
   						$comment = '';
   						if ($imgtype==1) { // gif
   							$fc    = file_get_contents($to);
   							$arr   = explode('!', $fc);
   							$found = 0;
   							for ($i = 0;$i<count($arr)&&!$found;++$i) {
   								$block = $arr[$i];
   								if (substr($block, 0, 2)==chr(254).chr(21)) {
   									$found   = 1;
   									$comment = substr($block, 2, strpos($block, 0)-1);
   								}
   							}
   						}
   						else {
   							$data = @exif_read_data($to, 0, true);
   							if (is_array($data)&&isset($data['COMMENT'])&&is_array($data['COMMENT'])) $comment = join("\n", $data['COMMENT']);
   						}
   						$file->setCaption($comment);
   					}
   					else if (isset($_POST['kfm_unzipWhenUploaded'])&&$_POST['kfm_unzipWhenUploaded']) {
   						kfm_extractZippedFile($fid);
   						$file->delete();
   					}
   				}
   				$fids[]=$fid;
			   }
		}
	}
}
else $errors[] = kfm_lang('permissionDeniedUpload');

if (isset($_REQUEST['swf']) && $_REQUEST['swf']==1) {
	if(count($errors))echo join("\n", $errors);
	else echo 'OK';
	exit;
}
?>
<html>
<head>
<script type="text/javascript">
<?
         $js = isset($_REQUEST['js'])?$js:'';
         if (isset($_REQUEST['onload'])) echo $_REQUEST['onload'];
         else if (isset($_REQUEST['onupload'])) echo $_REQUEST['onupload'];
         else if (count($errors)) echo 'alert("'.addslashes(join("\n", $errors)).'");';
//         else
         {
         	echo 'parent.kfm_vars.startup_selectedFiles=['.join(',',$fids).'];';
         	echo 'parent.x_kfm_loadFiles('.$kfm_session->get('cwd_id').',parent.kfm_refreshFiles);';
         	echo 'parent.kfm_dir_openNode('.$kfm_session->get('cwd_id').');'.$js;
       }
       ?>
</script>
</head>
<body>
</body>
</html>
