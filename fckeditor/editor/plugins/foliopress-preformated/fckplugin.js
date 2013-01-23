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

var	g_pastetest = 0;
var	g_bodytext = '';

var FPPreformated = new Object();

/**
*	erase <br> or <br />
*	
*	@param	string	oM	string to process
*	@return	string	processed string
*/
function FPPreformated_ClearBRtags( oM, oOpener, iIndex ){
	oM = oM.replace(new RegExp("<br>","g"),'\n');
	oM = oM.replace(new RegExp("<br />","g"),'\n');
	return oM;
}

/**
*	custom event handler
*/
function FPPreformated_OnAfterSetHTML( oEditor ){
	var strText = '';

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) {
		strText = oEditor.EditingArea.Textarea.value;
		}
	else {
		strText = oEditor.EditorDocument.body.innerHTML;
		}
		
	strText = strText.replace(new RegExp("<pre.*</pre>","g"), FPPreformated_ClearBRtags );

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) oEditor.EditingArea.Textarea.value = strText;
	else oEditor.EditorDocument.body.innerHTML = strText;
	//alert("preformated here!");
}

/**
*	custom event handler
*/
function FPPreformated_OnAfterLinkedFieldUpdate( oEditor ){
	oEditor.LinkedField.value = oEditor.LinkedField.value.replace(new RegExp("<pre.*</pre>","g"), FPPreformated_ClearBRtags );
}

/**
*	enhancing the functionality of ProcessDocument
*/
var FPProcessor = FCKDocumentProcessor.AppendNew();
FPProcessor.ProcessDocument = function( oDocument ){
	
	var content = FCK.EditorDocument.body.innerHTML;
	var	iPreCount = 0;

	content = content.replace(new RegExp("<pre.*</pre>","g"), FPPreformated_ClearBRtags );
	//if(iPreCount!=0)
		//alert("Undisclosed <pre> tags!");
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FPPreformated_OnAfterSetHTML );
FCK.Events.AttachEvent( 'OnAfterLinkedFieldUpdate', FPPreformated_OnAfterLinkedFieldUpdate );