/*
Plugin Name: FolioPress Preformated Tags for FCKeditor
Plugin URI: http://www.foliovision.com
Description: This plugin is used to cleanup tables z outlooku
Version: 0.1
Author: Foliovision s.r.o.
Author URI: http://www.foliovision.com

@author Foliovision s.r.o
@version 0.1
*/

var FPTableCleanup = new Object();

/**
*	tralalal
*	
*	@param	string	oM	string to process
*	@return	string	processed string
*/
function FPTableCleanup_ClearSpanTags( oM, oOpener, iIndex ){
	if(oM.match("fck") != null)
		return oM;
	else {
		//alert("Span count: " + g_spans);
		return "";
	}
}

function FPTableCleanup_ClearPTags( oM, oOpener, iIndex ){
	if(oM.match("fck") != null)
		return oM;
	else
		return "<p>"
}

function FPTableCleanup_TableCleanup( strText ) {
	var i, iTableBegini = 0, iTableEndi = 0;
	var text = '';
	var strTableBegin = '<table '
	var strTableEnd = '</table>'
	var iTableBeginCount = 0, iTableEndCount = 0;
	var iTableBeginPossition = 0, iTableEndPossition = 0;
		
	for(i = 0; i < strText.length; i++) {
    //  searching for table begin
    if(strTableBegin.charAt(iTableBegini) == strText.charAt(i)) {
      iTableBegini++;
      if(iTableBegini == strTableBegin.length) {
        //alert(text);
        iTableBegini = 0;
        iTableBeginCount++;
        //  Set nested-table start possition;
        if(iTableBeginCount == 1)
          iTableBeginPossition = i - strTableBegin.length + 1;
      }
    }
    else
      iTableBegini = 0;
    /// searching for table end
    if(strTableEnd.charAt(iTableEndi) == strText.charAt(i)) {
      iTableEndi++;
      if(iTableEndi == strTableEnd.length) {
        //alert(text);
        iTableEndi = 0;
        iTableEndCount++;
      }
    }
    else
      iTableEndi = 0;
    //  This detects the end of nested-table part of string 
    if(iTableBeginCount == iTableEndCount && iTableBeginCount > 0) {
      iTableEndPossition = i;
      //alert('Nested table ' + iTableBeginCount + '/' + iTableEndCount + ' ' + iTableBeginPossition + ' - ' + iTableEndPossition);
      
      //  ak je vnorena, tak pojdu von vsetky!\
      if(iTableBeginCount > 1) {
        text = strText.substring(iTableBeginPossition, iTableEndPossition);
        
        text = text.replace(/( |\t)*?<table.*?>\n?\r?( |\t)*?/gi,'');
        text = text.replace(/( |\t)*?<\/table>\n?\r?( |\t)*?/gi,'');
        text = text.replace(/( |\t)*?<td.*?>\n?\r?( |\t)*?/gi,'');
        text = text.replace(/( |\t)*?<\/td>\n?\r?( |\t)*?/gi,'\t');
        text = text.replace(/( |\t)*?<tr.*?>\n?\r?( |\t)*?/gi,'');
        text = text.replace(/( |\t)*?<\/tr>\n?\r?( |\t)*?/gi,'<br />');
        text = text.replace(/( |\t)*?<tbody.*?>\n?\r?( |\t)*?/gi,'');
        text = text.replace(/( |\t)*?<\/tbody>\n?\r?( |\t)*?/gi,'');
        
        strText =  strText.substring(0, iTableBeginPossition) + text + strText.substring(iTableEndPossition, strText.length);
      }
      iTableBeginCount = 0;
      iTableEndCount = 0; 
    }     
  }
  return strText;
}

/**
*	custom event handler
*/
function FPTableCleanup_OnAfterSetHTML( oEditor ){
  var strText = '';

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) {
		strText = oEditor.EditingArea.Textarea.value;
		}
	else {
		strText = oEditor.EditorDocument.body.innerHTML;
		}
	
	
	strText = FPTableCleanup_TableCleanup( strText );
	
	//strText = strText.replace(new RegExp("<pre.*</pre>","g"), FPTableCleanup_ClearBRtags );

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) oEditor.EditingArea.Textarea.value = strText;
	else oEditor.EditorDocument.body.innerHTML = strText;
	//alert("preformated here!");
}

/**
*	custom event handler
*/
function FPTableCleanup_OnAfterLinkedFieldUpdate( oEditor ){
	oEditor.LinkedField.value = FPTableCleanup_TableCleanup( oEditor.LinkedField.value );
}

function FPTableCleanup_OnPaste( oEditor ){
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
	
	strText = FPTableCleanup_TableCleanup (strText);
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FPTableCleanup_OnAfterSetHTML );
FCK.Events.AttachEvent( 'OnAfterLinkedFieldUpdate', FPTableCleanup_OnAfterLinkedFieldUpdate );
//FCK.Events.AttachEvent( 'OnPaste', FPTableCleanup_OnPaste );