if( typeof FV_Regex == 'undefined' ) LoadScript( CKEDITOR_BASEPATH + 'plugins/foliopress-clean/foliovision-regex.js' );

STR_REGEXP_LT = "(?:\\x3C)";
STR_REGEXP_GT = "(?:\\x3E)";
STR_REGEXP_FS = "(?:\\x2F)";
STR_DEFAULT_TAGS = "p|div"

var FPClean = new Object();
FPClean.bLoaded = false;
FPClean.aRegexes = new Array();
FPClean.strTags = '';

function FPClean_LoadConfigs(){
	if( typeof FCKConfig.FPClean_SpecialText == 'undefined' ) return;
	if( typeof FV_Regex == 'undefined' ) return;
	if( typeof FCKConfig.FPClean_Tags == 'undefined' ) FPClean.strTags = STR_DEFAULT_TAGS;
	else FPClean.strTags = FCKConfig.FPClean_Tags;
	
	var strREText = '';
	for( var i=0; i<FCKConfig.FPClean_SpecialText.length; i++ ){
		strREText = STR_REGEXP_LT + "(?:" + FPClean.strTags + ")" + STR_REGEXP_GT + "(";
		strREText += FV_Regex.ConvertString( FCKConfig.FPClean_SpecialText[i] );
		strREText += ")" + STR_REGEXP_LT + STR_REGEXP_FS + "(?:" + FPClean.strTags + ")" + STR_REGEXP_GT;
		
		FPClean.aRegexes.push( new RegExp( strREText, "g" ) );
	}
	
	FPClean.bLoaded = true;
}

function FPClean_ClearTags( strText ){
	if( false == FPClean.bLoaded ) FPClean_LoadConfigs();
	
	var strChange = strText;
	for( var i=0; i<FPClean.aRegexes.length; i++ ){
		strChange = strChange.replace( FPClean.aRegexes[i], "$1" );
	}

	
	///  Modification 2010/11/09
	//strChange = strChange.replace ("<p>&nbsp;</p>","");
	strChange = strChange.replace (/<p>&nbsp;<\/p>/gi,"");
	strChange = strChange.replace (/<p>&#160;<\/p>/gi,"");
	///  End of modification

	return strChange;
}

function FPClean_OnAfterSetHTML( oEditor ){
	var strText = '';
	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) strText = oEditor.EditingArea.Textarea.value;
	else strText = oEditor.EditorDocument.body.innerHTML;
	
	strText = FPClean_ClearTags( strText );
	
	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) oEditor.EditingArea.Textarea.value = strText;
	else oEditor.EditorDocument.body.innerHTML = strText;
}

function FPClean_OnAfterLinkedFieldUpdate( oEditor ){
	oEditor.LinkedField.value = FPClean_ClearTags( oEditor.LinkedField.value );
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FPClean_OnAfterSetHTML );
FCK.Events.AttachEvent( 'OnAfterLinkedFieldUpdate', FPClean_OnAfterLinkedFieldUpdate );