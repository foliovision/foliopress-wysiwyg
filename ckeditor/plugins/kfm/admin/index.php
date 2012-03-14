<?php
require_once('initialise.php');
foreach($kfm->plugins as $plugin){
	foreach($plugin->admin_tabs as $tab){
		if(!empty($tab['requirements']['user_ids']) && !in_array($kfm->user_id, $tab['requirements']['user_ids'])) continue;
		$kfm->addAdminTab(isset($tab['title'])? $tab['title'] : $plugin->title, $plugin->url().$tab['file'], isset($tab['stylesheet'])? $plugin->url.$tab['stylesheet'] : false);
	}
}
$getparams='?';
foreach($_GET as $key => $value)$getparams.=urlencode($key).'='.urlencode($value).'&';
$getparams=rtrim($getparams,'& ');
$sprefix='kfm_setting_'; // Until now a dummy prefix for settings. Maybe needed for future things. Also in the settings.php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>KFM admin</title>

<link rel="stylesheet" href="../j/jquery/tabs/ui.tabs.css" type="text/css" />
<link rel="stylesheet" href="../themes/<?php echo $kfm->setting('theme');?>/css.php" type="text/css" />
<link type="text/css" rel="stylesheet" href="http://jqueryui.com/themes/base/ui.all.css" />
<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('jquery', '1');
google.load('jqueryui', '1');
// ugly fix for variable.js call to kfm_vars
kfm_vars={};
</script>
<script type="text/javascript" src="../j/all.php"></script>
<script type="text/javascript" src="../j/jquery/tabs/ui.tabs.js"></script>
<script type="text/javascript">
$ = $j;
$(function(){
	$('#tabscontainer > ul').tabs();
});
function message(msg){
	jobj=$('#messages');
	jobj.html(msg);
	jobj.fadeIn();
	jobj.animate({fontSize:"16px"},2500);
	jobj.fadeOut();
}
function error(msg){
	message('Error: '+msg);
}

</script>
<?php
foreach($kfm->admin_tabs as $tab){
	if($tab['stylesheet'])echo '<link rel="stylesheet" href="'.$tab['stylesheet'].'" type="text/css" />'."\n";
}
?>
<style type="text/css">
#general_info{
	float:right;
	width:300px;
	background-color:#ddd;
	text-align:right;
	padding-right:4px;
}
.admin_button{
	color:black;
	font-weight:bold;
}
#messages{
	display:none;
	position:absolute;
	border:2px dashed #aaa;
	background-color:#006;
	color:white;
	width:200px;
	top:30px;
	right:30px;
	padding:5px;
	font-size:16px;
}
#password_div{
	margin:30px auto;
	padding:10px;
	width:450px;
	border:2px dashed #aaa;
}
#password_div label { position: absolute; text-align:left; width:222px; }
#password_div input, textarea { margin-left: 140px; }

.settings_container{
	margin-left:60px;
	margin-right:60px;
	background-color:#eee;
}
.button{
	cursor:hand;
	width:automatic;
}
#kfm_admin_users_table{
	margin-left:60px;
	margin-right:60px;
	background-color:#eee;
}
.group_header{
	font-size:24px;
	font-weight:bold;
}
.user_setting{
}
.default_setting{
	color:#777;
}
</style>
<style type="text/css">
#associations_container{
	margin-left:60px;
	margin-right:60px;
	padding:10px;
	background-color:#eee;
}
</style>
<style type="text/css">
.help_container{
	position:absolute;
	display:none;
	border:2px solid #bbb;
	background-color:#444;
	width:300px;
	padding:5px;
}
.help_title{
	margin:0 25px 0 0;
	background-color:#777;
	color:white;
}
.help_title h1{
	font-sze:10px;
	cursor:pointer;
	background-color:inherit;
}
.help_body{
	padding:10px;
	background-color:#777;
	color:white;
	margin-top:5px;
}
.help_body pre{
	display:block;
	margin: 3px 0px 3px 15px;
	padding: 2px 4px;
	font-family: verdana;
	background-color:#888;
}
.help_close{
	position:absolute;
  display:block;
  right: 5px;
	top:5px;
	border:1px solid #bbb;
	background-color:#777;
	color:white;
	padding: 0 2px;
	cursor:pointer;
}
</style>
<script type="text/javascript">
var sprefix='<?php echo $sprefix;?>';
</script>
<script type="text/javascript" src="settings.js"></script>
</head>
<body>
<div id="general_info">
<?php echo $kfm->username;?> <a class="admin_button" href="<?php echo $kfm->setting('kfm_url');?>">To the File Manager</a>
</div>
<div id="messages"></div>
<div id="tabscontainer">
	<ul>
		<?php if($kfm->user_status==1) echo '<li><a href="users.php" title="Users tab"><span>Users</span></a></li>'; ?>
		<li><a href="settings.php" title="Settings tab"><span>Settings</span></a></li>
		<li><a href="password.php" title="Password tab"><span>Change password</span></a></li>
		<?php 
		if($kfm->user_status==1) echo '<li><a href="associations.php" title="File associations"><span>File associations</span></a></li>';
		foreach($kfm->admin_tabs as $tab){
			$active=isset($_GET['tab'])&&$_GET['tab']==$tab['title']?' class="ui-tabs-selected"':'';
			echo '<li'.$active.'><a href="'.$tab['page'].$getparams.'" title="'.$tab['title'].'"><span>'.$tab['title'].'</span></a></li>'."\n";
		}
		?>
	</ul>
</div>
</body>
</html>
