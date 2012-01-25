/*
Plugin Name: FolioPress RBG Colors Replacer - convert rbg inline CSS colors to hex values
*/

var FPRGBColors = new Object();

function FPRGBColors_ClearRBGStyles( strText ) {
  //strText = strText.replace( '/rgb\((\d*), (\d*), (\d*).*?\)/', '$1$2$3' );

  afterStrText = strText.replace( /(?:rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\))/gi, function( match, red, green, blue ) {
    red = parseInt( red, 10 ).toString( 16 );
    green = parseInt( green, 10 ).toString( 16 );
    blue = parseInt( blue, 10 ).toString( 16 );
    var color = [red, green, blue] ;
    
    // Add padding zeros if the hex value is less than 0x10.
    for ( var i = 0 ; i < color.length ; i++ )
      color[i] = String( '0' + color[i] ).slice( -2 ) ;
    return '#' + color.join( '' ) ;
  }); 
  
  

  return afterStrText; 
}

/**
*	custom event handler
*/
function FPRGBColors_OnAfterSetHTML( oEditor ){
  var strText = '';

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) {
		strText = oEditor.EditingArea.Textarea.value;
		}
	else {
		strText = oEditor.EditorDocument.body.innerHTML;
		}
	
	
	strText = FPRGBColors_ClearRBGStyles( strText );
	
	//strText = strText.replace(new RegExp("<pre.*</pre>","g"), FPTableCleanup_ClearBRtags );

	if( FCK_EDITMODE_WYSIWYG != oEditor.EditingArea.Mode ) oEditor.EditingArea.Textarea.value = strText;
	else oEditor.EditorDocument.body.innerHTML = strText;
	//alert("preformated here!");
}

/**
*	custom event handler
*/
function FPRGBColors_OnAfterLinkedFieldUpdate( oEditor ){
	oEditor.LinkedField.value = FPRGBColors_ClearRBGStyles( oEditor.LinkedField.value );
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FPRGBColors_OnAfterSetHTML );
FCK.Events.AttachEvent( 'OnAfterLinkedFieldUpdate', FPRGBColors_OnAfterLinkedFieldUpdate );