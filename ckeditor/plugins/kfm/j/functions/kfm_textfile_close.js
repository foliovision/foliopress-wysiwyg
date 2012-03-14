window.kfm_textfile_close=function(){
	if(document.getElementById("edit-start").value!=codepress.getCode() && !kfm.confirm( kfm.lang.CloseWithoutSavingQuestion))return;
	kfm_leftColumn_enable();
	kfm_changeDirectory("kfm_directory_icon_"+kfm_cwd_id);
	$j.event.remove(document.getElementById('kfm_right_column'),'keyup',kfm_textfile_keybinding);
}
