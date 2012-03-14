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

// what type of database to use
// values allowed: mysql, pgsql, sqlitepdo
$kfm_db_type = 'sqlitepdo';

// the following options should only be filled if you are not using sqlitepdo as the database
$kfm_db_prefix   = 'kfm_';
$kfm_db_host     = 'localhost';
$kfm_db_name     = 'kfm';
$kfm_db_username = 'username';
$kfm_db_password = 'password';
$kfm_db_port     = '';

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
$kfm_userfiles_address = '/home/kae/Desktop/userfiles';

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
$kfm_userfiles_output = '/userfiles/';

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

// what plugin should handle double-clicks by default
$kfm_default_file_selection_handler='return_url';

/**
 * This function is called in the admin area. To specify your own admin requirements or security, un-comment and edit this function
 */
//	function kfm_admin_check(){
//		return true;
//	}
