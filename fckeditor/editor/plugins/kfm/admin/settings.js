function change_setting(name,value,uid){
  var usersetting = $('#'+sprefix+name+'_'+uid+'_usersetting').val();
  usersetting = usersetting ? usersetting : 0; // Non admin users will not have the choice
  $('.'+sprefix+name+'_usersetting').val(usersetting); // Change other open list if thre are any
	$.post('setting_change.php',{uid:uid,name:name,value:value,usersetting:usersetting},function(res){eval(res);});
}
function style_usersetting(name, uid){
	$('#desc_'+sprefix+name+'_'+uid).removeClass().addClass('user_setting');
}
function style_defaultsetting(name, uid){
	$('#desc_'+sprefix+name+'_'+uid).removeClass().addClass('default_setting');
}
function change_is_user_setting(name, is, uid){
  if(!parseInt(is)){
    //TODO offer option to delete this setting for all non admin users
  }
  var value = $('#'+sprefix+name+'_'+uid).val();
	$.post('setting_change.php',{uid:uid,name:name,value:value,usersetting:is},function(res){eval(res);});
	//$.post('settings_isuser_change.php',{sname:name,isuser:is},function(res){eval(res);});
}
function setting_default_value(name, uid){
	$.post('setting_make_default.php', {sname:name, uid:uid},function(res){eval(res);});
}
function setting_select_list(name, option, checked, uid){
	checked=checked?1:0;
	$.post('setting_change.php',{uid:uid,name:name,value:option,checked:checked},function(res){eval(res);});
}
function setting_help(setting_name, caller, uid){
	var cont=$("<div class=\"help_container\" id=\""+setting_name+"_help\"></div>");
	var title=$("<div class=\"help_title\"><h1>"+setting_name+"</h1></div>");
	var help="<img src=\"/themes/<?php echo $kfm->setting('theme');?>/large_loader.gif\" alt=\"loading...\" />";
	var hbody=$("<div class=\"help_body\">"+help+"</div>");
	var close=$("<div class=\"help_close\" onclick=\"setting_help_close('"+setting_name+"')\">x</span>");
	cont.append(close);
	cont.append(title);
	cont.append(hbody);
	cont.Draggable({handle:'h1'});
	$('#settings_container_'+uid).append(cont);
	var calpos=$(caller).offset();
	calpos.left+=20;
	cont.css(calpos);
	hbody.html(help);
	cont.fadeIn();
	$.post('setting_get_help.php',{name:setting_name},function(res){hbody.html(res);});
}
function setting_help_close(setting_name){
	$("#"+setting_name+"_help").remove();
}
