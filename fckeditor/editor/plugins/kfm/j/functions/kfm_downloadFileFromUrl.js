window.kfm_downloadFileFromUrl=function(filename,msg){
	if(filename.toString()!==filename)filename='';
	var url=document.getElementById('kfm_url').value;
	if(url.substring(0,4)!='http')return;
	if(!filename)filename=url.replace(kfm_regexps.all_up_to_last_slash,'');
	var not_ok=0,o;
	kfm_prompt(kfm.lang.FileSavedAsMessage+msg,filename,function(filename){
		if(!filename)return;
		if(kfm_isFileInCWD(filename)){
			o=kfm.confirm(kfm.lang.AskIfOverwrite(filename));
			if(!o)not_ok=1;
		}
		if(filename.indexOf('/')>-1){
			msg=kfm.lang.NoForwardslash;
			not_ok=1;
		}
		if(not_ok)return kfm_downloadFileFromUrl(filename,msg);
		x_kfm_downloadFileFromUrl(url,filename,kfm_refreshFiles);
		document.getElementById('kfm_url').value='';
	});
}
