/*
Plugin Name: FolioPress Preformated Tags for FCKeditor
Plugin URI: http://www.foliovision.com
Description: This plugin is used to fix problems with line break in pre tags which occures in some sites.
Version: 0.1
Author: Foliovision s.r.o.
Author URI: http://www.foliovision.com

@author Foliovision s.r.o
@version 0.1
*/

////  V sucastnosti je problem, ze FF odstrani vsetky obrazky, nejde match

var FPIEWordCleanup = new Object();

/**
*	tralalal
*	
*	@param	string	oM	string to process
*	@return	string	processed string
*/
function FPIEWordCleanup_ClearSpanTags( oM, oOpener, iIndex ){
	if(oM.match("fck") != null)
		return oM;
	else {
		//alert("Span count: " + g_spans);
		return "";
	}
}

function FPIEWordCleanup_ClearPTags( oM, oOpener, iIndex ){
	if(oM.match("fck") != null)
		return oM;
	else
		return "<p>"
}

function FPIEWordCleanup_ClearTags( oM, oOpener, iIndex ){
	if(oM.match("_fck") != null)
		return oM;
	else {
    oM = oM.replace(new RegExp("style=\".*?\"","gmi"),"");
    return oM;
  }
}

function FPIEWordCleanup_ClearIMG( oM, oOpener, iIndex ){
  reg = RegExp("res://|file://","ig")
	if(!reg.test(oM))
		return oM;
	else
		return "";
}

function CleanupClear ( strText ) {
  var strTextBefore = '';
  strTextBefore = strText;
	//	W: Let the game begin!
	strText = strText.replace(new RegExp("<meta.*>","gmi"),"");
		
	strText = strText.replace(new RegExp("<w:.*>","gmi"),"[W:]");
	strText = strText.replace(new RegExp("<style.*?>(.|\r|\n)*?</style>","gmi"),"");
	strText = strText.replace(new RegExp("<xml>(.|\r|\n)*?</xml>","gmi"),"");
	
	strText = strText.replace(new RegExp("<span.*?>","gmi"),FPIEWordCleanup_ClearSpanTags);
	strText = strText.replace(new RegExp("</span>","gmi"),"");
	
	strText = strText.replace(new RegExp("<div .*?>","gmi"),FPIEWordCleanup_ClearTags);
	
	strText = strText.replace(new RegExp("<p .*?>","gmi"),FPIEWordCleanup_ClearTags);
	
	strText = strText.replace(new RegExp("<ol .*?>","gmi"),"<ol>");
	strText = strText.replace(new RegExp("</ol>","gmi"),"</ol>");
	strText = strText.replace(new RegExp("<li .*?>","gmi"),"<li>");
	strText = strText.replace(new RegExp("</li>","gmi"),"</li>");
	strText = strText.replace(new RegExp("<b .*?>","gmi"),"<b>");
	strText = strText.replace(new RegExp("(&nbsp;){2,}","gmi"),"&nbsp;");
	
	//strText = strText.replace(new RegExp("<a name=\"_Toc.*?>(.*?)</a>","g"),"$1");
	//strText = strText.replace(new RegExp("<font.*?>(.*?)</font>","gmi"),"$1");  // IE prob
	strText = strText.replace(/<font.*?>/gi,'');
	strText = strText.replace(/<\/font>/gi,'');
	strText = strText.replace(/<br.*?>/gi,'<br />');
	strText = strText.replace(new RegExp("<h1.*?>","gmi"),"<h2>");
	strText = strText.replace(new RegExp("<h1 .*?>","gmi"),"<h2>");
	strText = strText.replace(new RegExp("</h1>","gmi"),"</h2>");
	
	//strText = strText.replace(new RegExp("<!--\[if.*?-->","g"),"");
	//strText = strText.replace(new RegExp("<!--\[endif\]-->","g"),"");
	strText = strText.replace(/<!--\[if.*?>/gmi, '');
	strText = strText.replace(/<!--\[endif\]-->/gmi, '');
	
	strText = strText.replace(new RegExp("<o:.*?>","gmi"),'');
	strText = strText.replace(new RegExp("</o:.*?>","gmi"),'');
	strText = strText.replace(/<o:.*?>/gmi, '');
	strText = strText.replace(/<\/o:.*?>/gmi, '');
	
	strText = strText.replace(/<v:(.|\r|\n)*?>/gmi, '');
	strText = strText.replace(/<\/v:(.|\r|\n)*?>/gmi, '');
	
	strText = strText.replace(/<a name=\"_Toc.*?>(.*?)<\/a>/gi,"$1");
			
	strText = strText.replace(/<img.*?>/gmi, FPIEWordCleanup_ClearIMG);
	//strText = strText.replace(new RegExp("<img.*?>","gmi"), '');
	//	W: End!
	//alert(strTextBefore + "\n\n--------\n\n" + strText);
	
	return strText;
}

/**
*	custom event handler
*/
function FPIEWordCleanup_OnAfterSetHTML( oEditor ){

	var strText = '';

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) {
		strText = oEditor.EditingArea.Textarea.value;
		}
	else {
		strText = oEditor.EditorDocument.body.innerHTML;
		}
	
	strText = CleanupClear( strText );
	
	//strText = strText.replace(new RegExp("<pre.*</pre>","g"), FPIEWordCleanup_ClearBRtags );

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) oEditor.EditingArea.Textarea.value = strText;
	else oEditor.EditorDocument.body.innerHTML = strText;
	//alert("preformated here!");
}

/**
*	custom event handler
*/
function FPIEWordCleanup_OnAfterLinkedFieldUpdate( oEditor ){
	oEditor.LinkedField.value = CleanupClear( oEditor.LinkedField.value );
}

function FPIEWordCleanup_OnPaste( oEditor ){
	if( FCKBrowserInfo.IsIE ) {
	  return true;
	}
	else
		return true;
}

/**
*	enhancing the functionality of ProcessDocument
*/
var FPProcessor = FCKDocumentProcessor.AppendNew();
FPProcessor.ProcessDocument = function( oDocument ){
	
	var strText = FCK.EditorDocument.body.innerHTML;
	var	iPreCount = 0;

	strText = CleanupClear( strText );

}


FCK.CustomCleanWord = function ( oNode, bIgnoreFont, bRemoveStyles ) {

  //alert("custom");
  strText = oNode.innerHTML ;
  strText = CleanupClear( strText );

  return strText;

}


FCK.Events.AttachEvent( 'OnAfterSetHTML', FPIEWordCleanup_OnAfterSetHTML );
FCK.Events.AttachEvent( 'OnAfterLinkedFieldUpdate', FPIEWordCleanup_OnAfterLinkedFieldUpdate );
//FCK.Events.AttachEvent( 'OnPaste', FPIEWordCleanup_OnPaste );