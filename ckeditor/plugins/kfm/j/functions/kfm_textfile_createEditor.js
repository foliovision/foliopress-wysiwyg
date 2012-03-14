window.kfm_textfile_createEditor=function(){
	CodePress.run();
	var tip=document.getElementById('kfm_tooltip');
	if(tip)tip.parentNode.removeChild(tip);
	kfm_textfile_attachKeyBinding();
}
