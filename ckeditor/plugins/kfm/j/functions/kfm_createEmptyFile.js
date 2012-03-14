window.kfm_createEmptyFile=function(filename,msg){
	if(!filename || filename.toString()!==filename){
		filename='';
		msg='';
	}
	var not_ok=0;
	kfm_prompt(kfm.lang.WhatFilenameToCreateAs+msg,filename,function(filename){
		if(!filename)return;
		if(kfm_isFileInCWD(filename)){
			var o=kfm.confirm(kfm.lang.AskIfOverwrite(filename));
			if(!o)not_ok=1;
		}
		if(filename.indexOf('/')>-1){
			msg=kfm.lang.NoForwardslash;
			not_ok=1;
		}
		if(not_ok)return kfm_createEmptyFile(filename,msg);
		x_kfm_createEmptyFile(kfm_cwd_id,filename,kfm_refreshFiles);
	});
}
