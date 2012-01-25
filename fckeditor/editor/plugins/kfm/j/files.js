// see license.txt for licensing
var kfm_file_bits={
	cacheableIcons:[],
	contextMenu:function(e){
		var el=e.target;
		while(el.parentNode&&!el.file_id)el=el.parentNode;
		if(!el.parentNode)return;
		if(selectedFiles.length>1)kfm_getLinks(selectedFiles);
		else kfm_getLinks([el.file_id]);
	},
	dragDisplay:function(){
		var i;
		window.dragAddedFileToSelection=false;
		if(!kfm_isFileSelected(this.file_id)){
		  kfm_selectNone();
			kfm_addToSelection(this.file_id);
			window.dragAddedFileToSelection=true;
		}
		/*var drag_wrapper=document.createElement('div');
		drag_wrapper.id='kfm_drag_wrapper';
		drag_wrapper.style.minWidth='100px';
		drag_wrapper.style.opacity='.7';
		for(i=0;i<10&&i<selectedFiles.length;++i)kfm.addEl(drag_wrapper,[File_getInstance(selectedFiles[i]).name,document.createElement('br')]);
		if(selectedFiles.length>10){
			i=document.createElement('i');
			i.innerHTML=kfm.lang.AndNMore(selectedFiles.length-10);
			drag_wrapper.appendChild(i);
		}
		return drag_wrapper;*/
	},
	dblclick:function(e){
		var el=e.target;
		while(!el.file_id && el)el=el.parentNode;
		if(!el)return;
		var id=el.file_id;
		kfm_selectNone();
		kfm_addToSelection(id);
		var openingHook=kfm_getDefaultOpener([id]);
		if(openingHook)openingHook.doFunction([id]);
	},
	infoTooltipStart:function(e){ // initialise info tooltip

	///	2010/11/19, this fixes the hover over thumbnails issue - it grabs the parent node to get the file info
	if( !e.target.className.match(/kfm_file/gi) ) {
		e.target = e.target.parentNode;
	}
	///

	   //changed zUhrikova 17/02/2010 Foliovision
      //	kfm_showFileDetails(e.target.file_id);
		if(window.kfm_tooltipInit)clearTimeout(window.kfm_tooltipInit);
		if(window.kdnd_dragging)return; // don't open if currently dragging files
		if (!bFileSelected)window.kfm_tooltipInit=setTimeout('kfm_showFileDetails('+e.target.file_id+')',50);
	},
	infoTooltipStop:function(){ // remove info tooltip
		///	tooltip stay
		/*if(window.kfm_tooltipInit)clearTimeout(window.kfm_tooltipInit);
		var o=document.getElementById('kfm_tooltip');
		if(o)o.parentNode.removeChild(o);*/
	},
	padding:0
}
function kfm_fileLoader(id){
	if($type(id)!='array')return $j('#kfm_file_icon_'+id).css('background-image','url(themes/'+kfm_theme+'/icons/64x64/loader.gif)');
	id.each(kfm_fileLoader);
}
function kfm_filesLoader(){
	$j('<img src="themes/'+kfm_theme+'/small_loader.gif" alt=""/>').appendTo('#documents_loader');
}
function kfm_files_reflowIcons(){
	var el,panel,els,k;
	panel=document.getElementById('documents_body');
	if(panel.contentMode!='file_icons')return;
	k=0;
	iBoxSize = iThumbnail_size+10;
	els=$j('#documents_body .kfm_file_icon');
	els.each(function(){
		if(!this)return;
		ej=$j(this);
		ej.css({'clear':'none'});
		ej.css({'width':iBoxSize+'px'});
		if(k&&els[k-1].offsetLeft>=this.offsetLeft)ej.css({'clear':'left'});
		++k;
	});
	kfm_show_number_of_files(k);
	kfm_setThumbnails();	//	initial thumbnails display call
}
function kfm_setThumbnails(){
	var els,F,d,fold,top;
	top = $j(window).scrollTop();	///
	fold = $j(window).height() + top;
	els=$j('#documents_body .kfm_file_icon');

	els.each(function(){
		  F=File_getInstance(this.file_id);
		  if( F.width && !this.icon_loaded && fold>=getOffset(this,'Top') && top<getOffset(this,'Top') ){
			if( this.imageHolder.style.backgroundImage ) {
				///console.log( 'Skiping '+ this.imageHolder.style.backgroundImage );
			} else {
				F.setThumbnailBackground(this.imageHolder);
			}
		  }
	});
}
function kfm_getCachedIcon(type){
	if(window.kfm_file_bits.cacheableIcons[type])return window.kfm_file_bits.cacheableIcons[type];
	var icon=document.createElement('div');
	icon.className='kfm_file '+(type?'kfm_file_listview':'kfm_file_icon');
	icon.style.cursor=window.ie?'hand':'pointer';
	window.kfm_file_bits.cacheableIcons[type]=icon;
	return icon;
}
function kfm_isFileInCWD(id){
	var i,files=document.getElementById('documents_body').fileids;
	for(i=0;i<files.length;++i)if(files[i]==id)return true;
	return false;
}


/// #### Add	zUhrikova	moved from older version by pBaran		1/2/2010		Foliovision
var iCaller = -1;
function kfmAdd_TryToFindCaller(){
	try{
		var bCaller = window.opener.kfmBridge_ItIsMe();
		if( bCaller ) iCaller = 0;
	}catch( ex ){ iCaller = 1; }
}
/// #### End of add		zUhrikova		1/2/2010

function kfm_incrementalFileDisplay(refresh_count){
	if(refresh_count!=kfm_vars.files.refresh_count){ // a new refresh is fired
		return;
	}
	var a,b,fsdata,wrapper,fdata,name,F,el,id,prevEl;
	b=window.kfm_incrementalFileDisplay_vars;
	fsdata=b.data.files;

	wrapper=document.getElementById('documents_body');
	
	if(wrapper.contentMode!='file_icons')return (window.kfm_incrementalFileDisplay_vars=null);
	icon=kfm_getCachedIcon(kfm_listview);

	a=b.at;
	if(a)prevEl=document.getElementById('kfm_file_icon_'+fsdata[a-1].id);
	do{
		fdata=fsdata[a];
		name=fdata.name;
		id=fdata.id;
		F=File_getInstance(id,fdata);
		ext=fdata.ext;
		el=icon.cloneNode(true);
		if(!kfm_listview){ // add icon holder
			var img=document.createElement('div');
			img.className='img_holder';
			img.style.width = iThumbnail_size+'px';
			img.style.height = iThumbnail_size+'px';
			el.appendChild(img);
			el.imageHolder=img;
			///el.imageHolder.id = 'aaa';///	2010/11/08
			$j.event.add(el.imageHolder,'mouseover',window.kfm_dir_bits.addHover);
		}
		kfm_fileIcon_addEvents(el);
		el.id='kfm_file_icon_'+id;
		el.file_id=id;
		wrapper.files[a]=el;
		el.appendChild(F.getText('name'));
		if(kfm_listview){
			var cs=0,cell;
			var listview_table=$j('#kfm_files_listview_table tbody').get(0);
			var rows=listview_table.rows.length;
			var row=listview_table.insertRow(rows);
			row.fileid=F.id;
			row.id='kfm_files_listview_table_row'+F.id;
			cell=row.insertCell(cs++);
			cell.className='listview_icon listview_icon_'+ext;
			cell.innerHTML='&nbsp;';
			row.insertCell(cs++).appendChild(el);
			{ // file size
				cell=row.insertCell(cs++);
				var hidden=document.createElement('span');
				hidden.style.display='none';
				hidden.appendChild(document.createTextNode(F.filesize_raw));
				cell.appendChild(hidden);
				cell.appendChild(F.getText('filesize'));
			}
			row.insertCell(cs++).appendChild(F.getText('ext'));
			{ // modified time
				cell=row.insertCell(cs++);
				var hidden=document.createElement('span');
				hidden.style.display='none';
				hidden.appendChild(document.createTextNode(F.ctime));
				cell.appendChild(hidden);
				cell.appendChild(F.getText('modified'));
			}
		}
		else{
			el.className+=' kfm_icontype_'+ext;
			wrapper.appendChild(el);
			if(a&&prevEl.offsetLeft>=el.offsetLeft)el.style.clear='left';
		}
		prevEl=el;
		++a;
	}while(a<fsdata.length && a%kfm_show_files_in_groups_of);
	window.kfm_incrementalFileDisplay_vars.at=a;
	if(a<fsdata.length)kfm_incrementalFileDisplay(refresh_count);
	else{ // finished displaying icons
		kdnd_makeDraggable('kfm_file');
		if(kfm_listview){
			$j('#kfm_tooltip').remove();
			$j('#kfm_files_listview_table').columnSizing();
			$j('#kfm_files_listview_table').tablesorter({
				sortList:[[1,0]],
				headers:{
					1:{
						sorter:'kfmobject'
					}
				},
				widgets:['zebra']
			});
		}
		else kfm_files_reflowIcons();
		$j('#documents_loader').html('&nbsp;');
		if(kfm_vars.startup_selectedFiles){
			for(var i=0;i<kfm_vars.startup_selectedFiles.length;++i){
        kfm_selectNone(); //added zUhrikova 21/02/2010 Foliovision
        kfm_addToSelection(kfm_vars.startup_selectedFiles[i]);
      }
			kfm_vars.startup_selectedFiles=false;
		}
	}
}
function kfm_fileIcon_addEvents(icon){
	$j.event.add(icon,'mouseover',function(e){
      if(!kfm_listview)window.kfm_file_bits.infoTooltipStart(e);
		//kfm_showFileDetails(selectedFiles[selectedFiles.length-1]);
      if(this.hasActionEvents)return;
//		if (!e.ctrlKey){
      $j.event.add(this,'click', kfm_toggleSelectedFile);
     	$j.event.add(this,'dblclick',window.kfm_file_bits.dblclick);
   	if(!kfm_listview)$j.event.add(this,'mouseout',window.kfm_file_bits.infoTooltipStop);
		kfm_addContextMenu(icon,window.kfm_file_bits.contextMenu);
		this.hasActionEvents=true;
		this.dragDisplay=kfm_file_bits.dragDisplay;
	});
/*	$j.event.add(icon,'mouseover',function(e){
	   // Changed zUhrikova 05/02/2010 Foliovision
		if(!kfm_listview) 
      window.kfm_file_bits.infoTooltipStart(e); 
		/*  if( !bFileSelected )
		  {   //e=new Event(e);
		      window.kfm_showFileDetails(e.target.file_id);
//    		window.kfm_run_delayed( 'file_details', 'kfm_showFileDetails(' + e.target.file_id + ');', 50 );
    		  }*/
  		   // 
      // End of change zUhrikova
	/*	if(this.hasActionEvents)return;
		$j.event.add(this,'click',kfm_toggleSelectedFile);
		$j.event.add(this,'dblclick',window.kfm_file_bits.dblclick);
		if((!kfm_listview))
        $j.event.add(this,'mouseout',window.infoTooltipStop);
      kfm_addContextMenu(icon,window.kfm_file_bits.contextMenu);
		this.hasActionEvents=true;
		this.dragDisplay=kfm_file_bits.dragDisplay;
	});*/
}
function kfm_refreshFiles(res){
	/// Addition		pBaran		11/07/2008		Foliovision
	if( 0 < kfm_cwd_id_startup ){
//	   console.log(kfm_startup_dirs);
		for( var i=0; i<kfm_startup_dirs.length-1; i++ )
        kfm_refreshDirectories( kfm_startup_dirs[i] );
		kfm_changeDirectory( 'kfm_directory_icon_' + kfm_startup_dirs[kfm_startup_dirs.length-1].parent );//
   	kfm_refreshPanels('kfm_left_column');
		kfm_cwd_id_startup = -1;
		return;
	} 
	/// End of addition		pBaran		11/07/2008

	if(!res.files)return;
	kfm_show_number_of_files(res.files.length);
	kdnd_addDropHandler('kfm_file','.kfm_directory_link',kfm_files_dragToDirectory);
	if(window.kfm_incrementalFileDisplay_loader){
		clearTimeout(window.kfm_incrementalFileDisplay_loader);
		window.kfm_incrementalFileDisplay_vars=null;
	}
	kfm_selectNone();
	if(!res)return;
	if(res.parent)kfm_cwd_id=res.parent;
	if(res.toString()===res)return;
	window.kfm_incrementalFileDisplay_vars={at:0,data:res};
	var a,b,lowest_name,lowest_index,s,wrapper;
	wrapper=document.getElementById('documents_body');
	wrapper.innerHTML='';
	$extend(wrapper,{contentMode:'file_icons',fileids:[],files:[]});
	document.getElementById('cwd_display').innerHTML=kfm.lang.CurrentWorkingDir(res.reqdir);
	{ // order files by name
		if(!res.files)res.files=[];
		for(a=0;a<res.files.length-1;++a){
			lowest_name=res.files[a].name;
			lowest_index=a;
			for(b=a+1;b<res.files.length;++b){
				if(res.files[b].name<lowest_name){
					lowest_index=b;
					lowest_name=res.files[b].name;
				}
			}
			if(lowest_index!=a){
				b=res.files[a];
				res.files[a]=res.files[lowest_index];
				res.files[lowest_index]=b;
			}
		}
	}
	for(a=0;a<res.files.length;++a)wrapper.fileids[a]=res.files[a].id;
	kfm_directories[kfm_cwd_id].hasChildren=res.files.length;
	document.title='SEO Images: '+res.reqdir;
	kfm_lastClicked=null;
	if(res.uploads_allowed)kfm_addPanel(document.getElementById('kfm_left_column'),'kfm_file_upload_panel');
	else kfm_removePanel('kfm_left_column','kfm_file_upload_panel');
	kfm_refreshPanels('kfm_left_column');
	if(!res.files.length){
		$j('#documents_loader').empty().html('&nbsp;');
		s=document.createElement('span');
		s.className='kfm_empty';
		s.innerHTML=kfm.lang.DirEmpty(res.reqdir);
		wrapper.appendChild(s);
	}else{
		if(kfm_listview){
			var listview_table=document.createElement('table');
			listview_table.id='kfm_files_listview_table';
			wrapper.appendChild(listview_table);
			$j(listview_table).html('<thead><tr class="listview_headers"><th>&nbsp;</th><th id="listview_headers_name">'+_('Name',0,0,1)+'</th><th id="listview_headers_size">'+_('Size',0,0,1)+'</th><th id="listview_headers_type">'+_('Type',0,0,1)+'</th><th id="listview_headers_lastmodified">'+_('Last Modified',0,0,1)+'</th></tr></thead><tbody></tbody>');
			$j(listview_table).css('width','99%');
		}
		kfm_vars.files.refresh_count++;
		kfm_incrementalFileDisplay(kfm_vars.files.refresh_count);
	}
}
function kfm_show_number_of_files(num){
	$j('#folder_info').text(num+(num==1?' file':' files')); //TODO new string
}
// { these are defined in their own files in /j/functions/
llStubs.push('kfm_deleteFile');
llStubs.push('kfm_deleteFiles');
llStubs.push('kfm_deleteSelectedFiles');
llStubs.push('kfm_downloadFileFromUrl');
llStubs.push('kfm_downloadSelectedFiles');
llStubs.push('kfm_downloadSelectedFiles_addIframe');
llStubs.push('kfm_files_dragToDirectory');
llStubs.push('kfm_extractZippedFile');
llStubs.push('kfm_removeFilesFromView');
llStubs.push('kfm_renameFile');
llStubs.push('kfm_renameFiles');
llStubs.push('kfm_showToolTip');
llStubs.push('kfm_zip');
// }
/// #### Add zUhrikova code from older version by pBaran     03/02/2010 Foliovision

var g_objPreviewWindow = null;
var g_strPreviousUrl = "";

/**
 * This function opens url in new window and ensures that this window is only one
 * 
 * Actualy window reference will be stored in global variable as well as previous url
 *    
 **/

function kfmAdd_openInWindow( id ){
	x_kfm_getFileUrl( id, 0, 0, function(url){
		if( g_objPreviewWindow == null || g_objPreviewWindow.closed ){
			g_objPreviewWindow = window.open( url, "preview" );
		}else if( g_strPreviousUrl != url ){
			g_objPreviewWindow = window.open( url, "preview" );
			g_objPreviewWindow.focus();
		}else{
			g_objPreviewWindow.focus();
		}

		g_strPreviousUrl = url;
	});
}

/// #### End of add     zUhrikova     03/02/2010