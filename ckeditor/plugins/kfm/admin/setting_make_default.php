<?php
require_once('initialise.php');
if(!isset($_REQUEST['sname'])) die ('error("Postvalues are not correct")');
$uid = ($kfm->isAdmin() && isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])) ? $_REQUEST['uid'] : $kfm->user_id;
$sn=$_REQUEST['sname'];
$kfm->db->query('DELETE FROM '.KFM_DB_PREFIX.'settings WHERE name="'.$sn.'" AND user_id='.$uid);
$a=db_fetch_all('SELECT value FROM '.KFM_DB_PREFIX.'settings WHERE name="'.$sn.'" AND user_id=1');
$value='#DEFAULT#';
foreach($a as $setting) $value=$setting['value'];
echo '$("#kfm_setting_'.$sn.'_'.$uid.'").val("'.$value.'");';
if($value=='#DEFAULT#')echo '$("#kfm_setting_'.$sn.'_'.$uid.'").attr("disabled","disabled");';
else if($kfm->sdef[$sn]['type']=='select_list'){
	require_once('functions.php');
	$newhtml=form_select_list($sn, explode(',',$value),$kfm->sdef[$sn]['options']);
	$newhtml=str_replace("'","\'",$newhtml);
	echo '$("#select_list_'.$sn.'_container_'.$uid.'").html(\''.$newhtml.'\');';
}else if($value=='theme'){
	$kfm_session->set('theme',$value);
}
echo 'style_defaultsetting("'.$_REQUEST['sname'].'", '.$uid.');';
if($uid != 1) echo '$("#todefault_'.$sn.'_'.$uid.'").fadeOut();';
?>
