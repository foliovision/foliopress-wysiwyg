// see license.txt for licensing
function kfm_addContextMenu(el,fn){
	var evtype='contextmenu';
	if(window.webkit&&!window.webkit420)evtype='mousedown'; // Safari, Konqueror
	if($j.browser.opera)evtype='mousedown'; // Opera

	$j.event.add(el,evtype,function(e){
	  /// Modification 2010/11/08
		if(e.type=='contextmenu' || e.button==2 || ((e.keyCode==17||e.ctrlKey==true) &&e.button>0)) {
			fn(e);
		}
		if( ( g_CtrlKeyDown && e.button == 0 ) ) {
			e.button = 2;
			fn(e);
		}
		/// End of modification
	});
	return el;
}
function kfm_contextmenuinit(){
	$j.event.add(document,'click',function(e){
//		if(e.ctrlKey)return;
		if(!contextmenu)return;
	   var c=contextmenu,m={x:e.pageX,y:pageY};
		var l=c.offsetLeft,t=c.offsetTop;
		if(m.x<l||m.x>l+c.offsetWidth||m.y<t||m.y>t+c.offsetHeight)kfm_closeContextMenu();
	});
	kfm_addContextMenu(document,function(e){
		if(window.webkit||!e.ctrlKey)e.stopPropagation();
	});
}
kfm.cm={
	submenus:[]
}
llStubs.push('kfm_closeContextMenu');
llStubs.push('kfm_createContextMenu');
// fix to hide normal context menu in Safari/Chrome
document.oncontextmenu = function(e){return false;}

/// #### Add	zUhrikova 	03/02/2010		Foliovision
/**
 * returns file name striped of extension and certain characters( '-', '.', '_' ) are changed to white space
 *
 *	sName:			String - Name of the image, or any other string that should be changed
 *
 **/ 
function kfmAdd_correctImageName( sName ){
	var iPos = sName.lastIndexOf( '.' );
	var sReturn = sName;
	if( iPos > 0 ) sReturn = sName.substring( 0, iPos );

	sReturn = sReturn.replace( /-/g, " " );
	sReturn = sReturn.replace( /\./g, " " );
	sReturn = sReturn.replace( /_/g, " " );
	
	return sReturn;
}

/**
 *	returns XHTML code of image back from input info
 *
 *	aImageSize:		Array - Array returned from "getimagesize" PHP function ([0] int-ImageWidth; [1] int-ImageHeight; [2] ?; [3] String-Properly Formated IMG width and height properties of HTML)
 *	sUrl:				String - Url of the image
 *	id:				Integer - Id of the image file in KFM hierarchy
 *	bFormat:			Bool - Specifies if output should be also wraped in H5 tag (Foliovision style to insert nicely formated images)
 *	sOriginalUrl:	String - Url of the original image used
 *	 
 **/ 
function kfmAdd_ImageTagText( aImageSize, sUrl, id, bFormat, sOriginalUrl ){
	var sHtmlCode = "";
	var myFile = File_getInstance( id );
   if( myFile ){
   	var sAlt = kfmAdd_correctImageName( myFile.name );
   	
   	var iWidth = 0;
   	try{
   		iWidth = aImageSize[0];
   	}catch( ex ){}
//      sOriginalUrl = str_replace('//', '/',sOriginalUrl);  //removed zUhrikova 15/2/2010
		if( iImageLink == 1 ) sHtmlCode += '<a href="' + sOriginalUrl + '" title="' + sAlt + '"';
		if(( iImageLink == 1 ) && iLinkLightbox == 1 ) sHtmlCode += ' rel="lightbox[slideshow]"';
		if( iImageLink == 1  ) sHtmlCode += '>';
      sHtmlCode += '<img src="' + sUrl + '"';
      if( iWidth != 0 ) sHtmlCode += aImageSize[3] + " ";
      sHtmlCode += 'alt="' + sAlt + '" />';
      if( iImageLink == 1 ) sHtmlCode += '</a>';
      //console.log( g_sHtmlCode_template );
      if( bFormat ) sHtmlCode = eval( g_sHtmlCode_template );
   }

   return sHtmlCode;
}
function kfmAdd_ImageTagTextOriginal( aImageSize, sUrl, id, bFormat, sOriginalUrl ){
	var sHtmlCode = "";
	var myFile = File_getInstance( id );
   if( myFile ){
   	var sAlt = kfmAdd_correctImageName( myFile.name );
   	
   	var iWidth = 0;
   	try{
   		iWidth = aImageSize[0];
   	}catch( ex ){}
//      sOriginalUrl = str_replace('//', '/',sOriginalUrl);  //removed zUhrikova 15/2/2010
      sHtmlCode += '<img src="' + sUrl + '"';
      if( iWidth != 0 ) sHtmlCode += aImageSize[3] + " ";
      sHtmlCode += 'alt="' + sAlt + '" />';
      
      //console.log( g_sHtmlCode_template );
      if( bFormat ) sHtmlCode = eval( g_sHtmlCode_template );
   }

   return sHtmlCode;
}
/**
 * returns corretly formated html code of <img> enclosed in <h5> tag of the requested to opener
 * Image taken is not resized
 *
 *	id:				Integer - Id of the image file in KFM hierarchy
 *	size:				Integer - Size of image to return to sender (0 is for original)
 *
 **/ 
function kfmAdd_returnImageWithFormating( id, size ){
  x_kfm_getFilesUrlAndSizes( id, size, size, function( aUrlSize ){
		if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download'){
			var sHtmlCode = kfmAdd_ImageTagText( aUrlSize[1], aUrlSize[0], id, true, aUrlSize[2] );
			window.opener.FCKSetHTML( sHtmlCode );
			/// Addition    29/10/09    Foliovision
			if(bMultipleImagePosting!=1)
			/// End of addition
           window.close();
//         else
//	    	  window.opener.focus();
		}
	});
}
/**
 * returns html code of <img> requested to opener
 * Image taken is not resized
 * bFormat:			Bool - Specifies if output should be also wraped in H5 tag (Foliovision style to insert nicely formated images) 
 **/
function kfmAdd_returnImage( id, bFormat){
	x_kfm_getFileUrl(id,0,0,function(url){
      if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download' ){
      	  x_kfm_getImageSize( id, function( aImageSize ){
            var sHtmlCode = kfmAdd_ImageTagTextOriginal( aImageSize, url, id, bFormat, url);
         	  window.opener.FCKSetHTML( sHtmlCode );
    			 if(bMultipleImagePosting!=1) /// Addition    29/10/09    Foliovision
				    window.close();
			   });
		}
   });

}
// Added zUhrikova 22/02/2010 Foliovision
// opens image in new tab
function kfmAdd_returnImageWindow( id ){
	x_kfm_getFileUrl(id,0,0,function(url){
      if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download' ){
      	if( g_objPreviewWindow == null || g_objPreviewWindow.closed ){
			  g_objPreviewWindow = window.open( url, "preview" );
		   }else if( g_strPreviousUrl != url ){
			  g_objPreviewWindow = window.open( url, "preview" );
			  g_objPreviewWindow.focus();
		    }else{
			    g_objPreviewWindow.focus();
		     }
          g_strPreviousUrl = url;
		 }
   });
}
///  Addition    23/07/09    Foliovision
/**
 * returns html code of <img> requested to opener
 * Image taken is not resized
 **/
function kfmAdd_returnImageThumbnail( id, size ){
	x_kfm_getFilesUrlAndSizes( id, size, size, function( aUrlSize ){
		/*if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor' ){
			var sHtmlCode = kfmAdd_ImageTagText( aUrlSize[1], aUrlSize[0], id, true, aUrlSize[2] );
			window.opener.FCKSetHTML( sHtmlCode );
			window.close();
		}*/
   if( g_objPreviewWindow == null || g_objPreviewWindow.closed ){
			g_objPreviewWindow = window.open( aUrlSize[0], "preview" );
		}else if( g_strPreviousUrl != aUrlSize[0] ){
			g_objPreviewWindow = window.open( aUrlSize[0], "preview" );
			g_objPreviewWindow.focus();
		}else{
			g_objPreviewWindow.focus();
		}
      g_strPreviousUrl = aUrlSize[0];
		/// Addition    29/10/09    Foliovision
		if(bMultipleImagePosting!=1)
		/// End of addition
            window.close();
	});
}
/**
 *	Function that copies file url into clipboard
 *
 *	id:				Integer - Id of file in KFM hierarchy
 *	size:				Integer - Size of image thumbnail, if it is an image
 *
 **/
function kfmAdd_copyUrl( id, size ){
	x_kfm_getFileUrl( id, size, size, function(url){
      CopyToClipboard( url );
   });
}
/**
 *	Function that copies text into clipboard
 *
 *	sText:			String - Text to be copied into clipboard
 *
 **/
function CopyToClipboard( sText ){
	if( is_ie5up ){
		window.clipboardData.setData( "Text", sText );
   }else if( is_gecko ){
   	try {
			netscape.security.PrivilegeManager.enablePrivilege( "UniversalXPConnect" );
		}catch (err) {   
			alert( "Sorry, but mozilla blocks clipboard copy:\n\n" +err+ "." );
			return false;
		}

   	var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance( Components.interfaces.nsIClipboard );
		if( !clip ) return false;

		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance( Components.interfaces.nsITransferable );
		if( !trans ) return false;

		trans.addDataFlavor('text/unicode');

		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance( Components.interfaces.nsISupportsString );
		var copytext = sText;

		str.data=copytext;
		trans.setTransferData( "text/unicode", str, copytext.length*2 );

		var clipid = Components.interfaces.nsIClipboard;
		if( !clipid ) return false;

		clip.setData( trans, null, clipid.kGlobalClipboard );
	}
	
   return true;
}

/// Addition    2010/01/05  Support for editing of meta fields etc.
/*function kfmAdd_PostThumbnail( id, sThumbField, iThumbWidth, iThumbHeight ) {
    x_kfm_getFilesUrlAndSizes( id, iThumbWidth, iThumbHeight, true, function( aUrlSize ){
		if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download'){
			//console.log( aUrlSize );
      kfmSetWPMeta( sThumbField, aUrlSize[0] );
      //if( sThumbField == 'thesis_thumb' ) {
        kfmSetWPEditorField( sThumbField, aUrlSize[0] );
      //}
		}
	});
  
}*/

function kfmAdd_PostMeta( id, sPostMeta ) {
    x_kfm_getFileUrl( id, 0, 0, function( url ){
		if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download'){
            kfmSetWPMeta( sPostMeta, url );
            //if( sThumbField == 'thesis_thumb' ) {
                kfmSetWPEditorField( sPostMeta, url );
            //}
		}
	});
  
}

function kfmSetWPMeta( metaKey, metaValue ) {
  if( window.opener ) {
    if( typeof window.opener.FCKSetWPMeta == 'function' ) {  //  todo, just one check
	  window.opener.FCKSetWPMeta( metaKey, metaValue );
    } else if( typeof window.opener.parent.FCKSetWPMeta == 'function' ) {
	  window.opener.parent.FCKSetWPMeta( metaKey, metaValue );
    } 
  }
  else {
    alert( 'Please reopen this window from editor toolbar.' );
  }
}

function kfmSetWPEditorField( metaKey, metaValue ) {
  if( window.opener ) {
    if( typeof window.opener.FCKSetWPEditorField == 'function' ) {  //  todo, just one check
	  window.opener.FCKSetWPEditorField( metaKey, metaValue );
    } else if( typeof window.opener.parent.FCKSetWPEditorField == 'function' ) {
	  window.opener.parent.FCKSetWPEditorField( metaKey, metaValue );
    } 
  }    
  else {
    alert( 'Please reopen this window from editor toolbar.' );
  }
}
/// End of addition

/// Addition    2010/05/02  Support for Featured Image
function kfmAdd_FeaturedImage( id ) {
    x_kfm_getFileUrl( id, 0, 0, function( url ){
		if( kfm_file_handler=='return' || kfm_file_handler=='fckeditor'|| kfm_file_handler=='download'){
      kfmSetFeaturedImage( url );
		}
	});
}

function kfmSetFeaturedImage( ImageURL ) {
  if( window.opener ) {

    if( typeof window.opener.FCKSetFeaturedImage == 'function' ) {  //  todo, just one check
	  window.opener.FCKSetFeaturedImage( ImageURL );
    } else if( typeof window.opener.parent.FCKSetFeaturedImage == 'function' ) {
	  window.opener.parent.FCKSetFeaturedImage( ImageURL );
    } 
  }
  else {
    alert( 'Please reopen this window from editor toolbar.' );
  }
}
/// End of addition