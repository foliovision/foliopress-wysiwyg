<?php
require_once('initialise.php');
/** This function sets a usersetting 
  * Only the values of the admin user are taken into account for this
 */
function change_usersetting($sn, $value, $is, $uid){
  global $kfm;
  $s=db_fetch_row('SELECT id FROM '.KFM_DB_PREFIX.'settings WHERE name="'.mysql_escape_string($sn).'" and user_id='.$uid);
  if($s && count($s)){
    $kfm->db->query('UPDATE '.KFM_DB_PREFIX.'settings SET value="'.mysql_escape_string($value).'", usersetting='.$is.' WHERE name="'.mysql_escape_string($sn).'" AND user_id='.$uid);
  }else{
    $sql = 'INSERT INTO '.KFM_DB_PREFIX.'settings (name, value, user_id, usersetting) VALUES ("'.mysql_escape_string($sn).'","'.mysql_escape_string($value).'", '.$uid.','.mysql_escape_string($is).')';
    $kfm->db->query($sql);
  }
}
if(!isset($_POST['name']) || !isset($_POST['value'])) die ('error("post value missing");');
if(!isset($_POST['usersetting'])) die ('error("Cannot determine if setting is usersetting")');
$sn=$_POST['name'];
$value=$_POST['value'];
$usersetting = (Int)$_POST['usersetting'];
/* Next section to create a proper value for the database */
if($kfm->sdef[$sn]['type']=='select_list'){
	if(!isset($_POST['checked']))die ('error("property checked must be given for a select list");');
	$ch=$_POST['checked'];
	$sval=implode(',',$kfm->setting($sn));
	if(isset($_POST['clean']) && $_POST['clean'])$sval='';
	if($ch){
		$sval.=','.$value;
	}else{
		$sval=preg_replace('/'.$value.',*/','',$sval);
	}
	$sval=trim($sval, ' ,');
	$value=$sval;
}
// Only allow administrator users to give a custom uid
$uid = ($kfm->isAdmin() && isset($_POST['uid']) && is_numeric($_POST['uid'])) ? $_POST['uid'] : $kfm->user_id;
//die('alert("'.$sn.'\n'.$value.'\n'.$usersetting.'\n'.$uid.'")');
if($uid == 1){
  change_usersetting($sn, $value, $usersetting, 1); 
}elseif($kfm->isAdmin()){
  if($usersetting xor $kfm->isUserSetting($sn)){
    // When a user with admin rights which is not the admin user changes the usersetting value
    // Change user setting value for admin user, but not per se its value
    // First determine if the admin user already has this setting, otherwise apply it
    $s=db_fetch_row('SELECT id FROM '.KFM_DB_PREFIX.'settings WHERE name="'.mysql_escape_string($sn).'" and user_id=1');
    if($s && count($s)){
      // only update the usersetting value, since the user with administrator rights might have applied a different setting for himself
      $kfm->db->query('UPDATE '.KFM_DB_PREFIX.'settings SET usersetting="'.mysql_escape_string($usersetting).'" WHERE name="'.mysql_escape_string($sn).'" AND user_id=1');
    }else{
      // Add the setting for the admin user. The setting of the user with administrator rights will become the default setting
      // I (Benjamin) think this is a feature and not a bug :)
      change_usersetting($sn, $value, $usersetting, 1);
    }
  }
  change_usersetting($sn, $value, $usersetting, $uid); 
	echo '$("#todefault_'.$sn.'_'.$uid.'").fadeIn();';
}elseif($kfm->isUserSetting($sn)){
  // User settings can be changed by all users
  // The usersetting value will be 0 but should not be used anyway
  change_usersetting($sn, $value, 0, $uid); 
	if($kfm->user_id!=1) echo '$("#todefault_'.$sn.'_'.$uid.'").fadeIn();';
}else{
  // Something illegal is going on!!!!
  die('error("You have no rights to change this setting!");');
}
/*
if($kfm->isAdmin() || $kfm->isUserSetting($sn)){
  // Only allow admin users to make settings for others 
  $uid= ($kfm->isAdmin() && isset($_POST['uid'] && is_numeric($_POST['uid'])) ? $_POST['uid']: $kfm->user_id;
  $s=db_fetch_row('SELECT id FROM '.KFM_DB_PREFIX.'settings WHERE name="'.mysql_escape_string($sn).'" and user_id='.$uid);
  if($s && count($s)){
    $kfm->db->query('UPDATE '.KFM_DB_PREFIX.'settings SET value="'.mysql_escape_string($value).'" WHERE name="'.mysql_escape_string($sn).'" AND user_id='.$uid);
  }else{
    $usersetting = mysql_escape_string($_POST['usersetting']);
    if(!$kfm->user_id == 1) $usersetting = 0; // Only admin user can 
    $kfm->db->query('INSERT INTO '.KFM_DB_PREFIX.'settings (name, value, user_id, usersetting) VALUES ("'.mysql_escape_string($sn).'","'.mysql_escape_string($value).'", '.$uid.','.$usersetting.')');
	  if($kfm->user_id!=1) echo '$("#todefault_'.$sn.'").fadeIn();';
  }
}
*/

// Change theme in current session if is changed
if($sn=='theme' && $uid == $kfm->user_id )$kfm_session->set('theme',$value);
echo 'style_usersetting("'.$sn.'",'.$uid.');';
?>
message('setting changed');
