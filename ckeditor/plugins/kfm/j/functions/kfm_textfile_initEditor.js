window.kfm_textfile_initEditor=function(res,readonly){
	if(!document.getElementById('kfm_left_column_hider'))kfm_leftColumn_disable();
	var t=document.createElement('table');
	t.id='kfm_editFileTable';
	t.style.width='100%';
	var right_column=document.getElementById('kfm_right_column');
	right_column.innerHTML='';
	$j.event.add(right_column,'keyup',kfm_textfile_keybinding);
	right_column.contentMode='codepress';
	right_column.appendChild(t);
	var r2=kfm.addRow(t),c=0;
	kfm.addCell(r2,c++,1,res.name);
	if(!readonly){ /* show option to save edits */
		kfm.addCell(r2,c++,1,$j('<a href="javascript:new Notice(\\"saving file...\\");document.getElementById(\\"edit-start\\").value=codepress.getCode();x_kfm_saveTextFile('+res.id+',document.getElementById(\\"edit-start\\").value,kfm_showMessage);" class="button">Save</a>'));
	}
	kfm.addCell(r2,c++,1,$j('<a href="javascript:kfm_textfile_close()" class="button">'+kfm.lang.Close+'</a>'));
	var row=kfm.addRow(t);
	r3=kfm.addCell(row,0,c);
	r3.id='kfm_codepressTableCell';
	var className='codepress '+res.language+(readonly?' readonly-on':'');
	var h=$(window).height()-t.offsetHeight-2;
	if(window.ie)h-=13;
	var codeEl=document.createElement('textarea');
	codeEl.id          ='codepress';
	codeEl.classname   =className,
	codeEl.value       =res.content,
	codeEl.title       =res.name;
	codeEl.style.width =(t.offsetWidth-25)+'px';
	codeEl.style.height=h+'px';
	changeCheckEl=newInput('edit-start','textarea',res.content);
	changeCheckEl.style.display='none';
	r3.appendChild(codeEl);
	r3.appendChild(changeCheckEl);
	if(window.CodePress)kfm_textfile_createEditor();
	else loadJS('j/codepress-0.9.6/codepress.js','cp-script','en-us','kfm_textfile_createEditor();');
}
