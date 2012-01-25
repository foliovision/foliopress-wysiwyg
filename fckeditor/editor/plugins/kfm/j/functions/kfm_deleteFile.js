window.kfm_deleteFile=function(id){
	if(!kfm_vars.permissions.file.rm)return kfm.alert(_("permission denied: cannot delete file"));
	var filename=File_getInstance(id).name;
	if(kfm.confirm(kfm.lang.DelFileMessage(filename))){
		x_kfm_rm([id],kfm_removeFilesFromView);
	}
}
