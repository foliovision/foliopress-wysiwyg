window.kfm_textfile_attachKeyBinding=function(){
	if(!codepress.editor||!codepress.editor.body)return setTimeout('kfm_textfile_attachKeyBinding();',1);
	var doc=codepress.contentWindow.document;
	if(doc.attachEvent)doc.attachEvent('onkeypress',kfm_textfile_keybinding);
	else doc.addEventListener('keypress',kfm_textfile_keybinding,false);
}
