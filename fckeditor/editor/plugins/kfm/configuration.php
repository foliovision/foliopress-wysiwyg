<?php
/**
 * KFM - Kae's File Manager
 *
 * configuration example file
 *
 * do not delete this file. copy it to configuration.php, remove the lines
 *   you don't want to change, and edit the rest to your own needs.
 *
 * @category None
 * @package  None
 * @author   Kae Verens <kae@verens.com>
 * @author   Benjamin ter Kuile <bterkuile@gmail.com>
 * @license  docs/license.txt for licensing
 * @link     http://kfm.verens.com/
 */

if( file_exists( dirname(__FILE__).'/../../../../../../../wp-load.php' ) )
  require_once( dirname(__FILE__).'/../../../../../../../wp-load.php' );
else
  require_once( dirname(__FILE__).'/../../../../../../../wp-config.php' );

error_reporting(NULL);

//if (!DB_HOST) require_once( dirname(__FILE__).'/../../../../../../../../../wp-config.php' );*/

global $current_user;

if( ( (!$current_user->id)&& !($_POST["kfm_session"]) ) ) {
  if( !check_admin_referer( 'foliopress-wysiwyg-recreate-thumbnails', 'foliopress-wysiwyg-recreate-thumbnails' ) ) {
    die('Access denied.');
  }
}

//echo(DB_NAME);
// what type of database to use
// values allowed: mysql, pgsql, sqlite, sqlitepdo
$kfm_db_type = 'mysql';

// the following options should only be filled if you are not using sqlite/sqlitepdo as the database
$kfm_db_prefix   = 'kfm_new_';
$kfm_db_host     = DB_HOST;
$kfm_db_name     = DB_NAME;
$kfm_db_username = DB_USER;
$kfm_db_password = DB_PASSWORD;
$kfm_db_port     = '';

// get settings from wp-config
$aFckOptions = get_option( FV_FCK_OPTIONS );

$image_path_changed = false;
if ($aFckOptions["image_path_changed"] || ( isset($aFckOptions["seo_images_reset"]) && $aFckOptions["seo_images_reset"] ) )
{
  $image_path_changed = true;
  $aFckOptions["image_path_changed"] = false;
  update_option( FV_FCK_OPTIONS,$aFckOptions);
}
/**
 * This setting specifies if you want to use the KFM security. If set to false, no login form will be displayd
 * Note that the user_root_folder setting will not work when the user is the main user
 *
 * Please change this to 'true' if you want to use usernames and passwords.
 */
$use_kfm_security=false;

/**
 * where on the server should the uploaded files be kept?
 * if the first two characters of this setting are './', then the files are relative to the directory that KFM is in.
 * Here are some examples:
 *    $kfm_userfiles_address = '/home/kae/userfiles'; # absolute address in Linux
 *    $kfm_userfiles_address = 'D:/Files';            # absolute address in Windows
 *    $kfm_userfiles_address = './uploads';           # relative address
 */
 
if(isset($aFckOptions["images"]))
  $defDirImages = str_replace('/','/',$aFckOptions["images"]);
else 
  $defDirImages = "images";

/// Addition  2011/01/17
//  Let's detect the root folder properly
$root_folder = $_SERVER['DOCUMENT_ROOT'];
if( strpos( ABSPATH, $root_folder ) !== 0 ) { //  if the DOCUMENT_ROOT is not matching the WP ABSPATH
  $wp_prefix = str_replace( get_bloginfo( 'url' ), '', get_bloginfo( 'wpurl' ) );
  $root_folder = str_replace( $wp_prefix, '', ABSPATH );
  $_SERVER['DOCUMENT_ROOT'] = $root_folder;
}
/// End of addition
$kfm_userfiles_address = $_SERVER['DOCUMENT_ROOT'] .'/'. $defDirImages;//'/images';

// where should a browser look to find the files?
// Note that this is usually the same as $kfm_userfiles_address (if it is relative), but could be different
//   in the case that the server uses mod_rewrite or personal web-sites, etc
// Use the value 'get.php' if you want to use the KFM file handler script to manage file downloads.
// If you are not using get.php, this value must end in '/'.
// Examples:
//   $kfm_userfiles_output = 'http://thisdomain.com/files/';
//   $kfm_userfiles_output = '/files/';
//   $kfm_userfiles_output = 'http://thisdomain.com/kfm/get.php';
//   $kfm_userfiles_output = '/kfm/get.php';
//$kfm_userfiles_output = '/images/';
$kfm_userfiles_output = $aFckOptions["images"];
$kfm_userfiles_output_real = $aFckOptions["images"];
if( isset($aFckOptions["images_real"]) && $aFckOptions["images_real"] ) {
	$kfm_userfiles_output_real = $aFckOptions["images_real"];

}
// directory in which KFM keeps its database and generated files
// if this starts with '/', then the address is absolute. otherwise, it is relative to $kfm_userfiles_address.
// $kfm_workdirectory = '.files';
// $kfm_workdirectory = '/home/kae/files_cache';
// warning: if you use the '/' method, then you must use the get.php method for $kfm_userfiles_output.
$kfm_workdirectory = '.files';

// where is the 'convert' program kept, if you have it installed?
$kfm_imagemagick_path = '/usr/bin/convert';

// use server's version of Pear?
$kfm_use_servers_pear = false;

// we would like to keep track of installations, to see how many there are, and what versions are in use.
// if you do not want us to have this information, then set the following variable to '1'.
$kfm_dont_send_metrics = 0;

// hours to offset server time by.
// for example, if the server is in GMT, and you are in Northern Territory, Australia, then the value to use is 9.5
$kfm_server_hours_offset = 1;

// thumb format. use .png if you need transparencies. .jpg for lower file size
$kfm_thumb_format='.jpg';
$kfm_thumb_size = ($aFckOptions["KFMThumbnailSize"])?$aFckOptions["KFMThumbnailSize"]:128;

// what plugin should handle double-clicks by default
$kfm_default_file_selection_handler='return_url';

#Permissions for uploaded files. Old, removed
$kfm_default_upload_permission = ($aFckOptions["fileperm"])?$aFckOptions["fileperm"]:777;
$kfm_default_directory_permission = ($aFckOptions["dirperm"])?$aFckOptions["dirperm"]:666;

# what happens if someone double-clicks a file or presses enter on one? use 'return' for FCKeditor
$kfm_file_handler='return'; # values allowed: download, return
$kfm_allow_multiple_file_returns = false;


/// Addition		zUhrikova		12/2/2010		Foliovision
$bMultipleImagePosting = true;
$bTransformTrueColorToPalette = true;
$iTrueColorToPaletteLimit = 5000;
$iJPGQuality = 90;

// bRemember_Directory:
//   if true, the directory year/month will be loaded
//   if false, last opened directory will be loaded
$bDefDirectory = true; 
/// End of addition		pBaran		18/07/2008

### Added		zUhrikova		12/2/2010		Foliovision
//$kfm_special_thumbs_sizes = array( 400, 200, 150 );
$kfm_return_image_link = 1;
$kfm_link_lightbox = 1;
$custom_config_php = '';
### Added		zUhrikova		1/2/2010		Foliovision

//Added		zUhrikova		12/2/2010		Foliovision
// Copy from wp-config
//$kfm_special_thumbs_sizes = ;

foreach($aFckOptions["KFMThumbs"] as $iThumbSize){
  $kfm_special_thumbs_sizes[] = $iThumbSize;//$aFckOptions["KFMThumbs"];//array( 400, 200, 150 );
}

$kfm_return_image_link = $aFckOptions["KFMLink"];
$kfm_link_lightbox = $aFckOptions["KFMLightbox"];
$iJPGQuality = $aFckOptions["JPEGQuality"];
$bTransformTrueColorToPalette = $aFckOptions["PNGTransform"];
$iTrueColorToPaletteLimit = $aFckOptions["PNGLimit"];

$kfm_use_imagemagick = true;

$bMultipleImagePosting = $aFckOptions["multipleimageposting"];
$iMaxWidth = $aFckOptions["MaxWidth"];
$iMaxHeight = $aFckOptions["MaxHeight"];

if (isset($aFckOptions["UseFlashUploader"]))
  $bUseFlashUloader = $aFckOptions["UseFlashUploader"];
else
  $bUseFlashUloader = true;  

if (isset($aFckOptions["DIRset"]))
  $bDefDirectory = $aFckOptions["DIRset"];
else
  $bDefDirectory = true;
### Folders that will not be shown inside KFM. 400, 200 and 150 are special folders used to store thumbnails of sizes 400, 200 and 150 respectively

$kfmAdd_banned_folders = array();
foreach( $kfm_special_thumbs_sizes as $iSize )
  $kfmAdd_banned_folders[] = sprintf( '%d', $iSize ); 
$kfmAdd_banned_folders[] = $kfm_workdirectory;
$kfmAdd_banned_folders[] = 'tmp_thumbs';
//$kfm->defaultSetting('banned_folders',array('/^\./'));
### ### End of add		zUhrikova		1/2/2010

///		Addition  2011/01/05
$kfm_sPostMeta = $aFckOptions[fp_wysiwyg_class::FV_SEO_IMAGES_POSTMETA];
$kfm_image_template = $aFckOptions[fp_wysiwyg_class::FV_SEO_IMAGES_IMAGE_TEMPLATE];
/// End of addition

/**
 * This function is called in the admin area. To specify your own admin requirements or security, un-comment and edit this function
 */
//	function kfm_admin_check(){
//		return true;
//	}
