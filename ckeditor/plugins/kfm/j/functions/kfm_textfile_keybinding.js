window.kfm_textfile_keybinding=function(e){
	if(e.which!=27)return;
	e.stopPropagation();
	kfm_textfile_close();
}
