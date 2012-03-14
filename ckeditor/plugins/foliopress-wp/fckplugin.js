/**
 * Some licence information
 *
 **/  

/**
 * Creates fake <p> element for FCK
 *
 * @param string strFakeText Text that will be inside this fake <p> tag
 * @param string strRealElement Text of the real element also with tag definition
 *
 * @return HTMLElement Created fake element object
 */
var FPDocumentProcessor_CreateFakeText = function( strFakeText, strRealElement ){
	var oText = FCK.EditorDocument.createElement( 'p' );
	oText.setAttribute( '_fckfakelement', 'true', 0 );
	oText.setAttribute( '_fckrealelement', FCKTempBin.AddElement( strRealElement ), 0 );
	oText.innerHTML = strFakeText;
	return oText;
}


/**
 * Class that represents more button in FCK Toolbar
 */
var FPMore = function( strName ){
	this.Name = strName;
}

/**
 * Function that is called when you click FPMore button on FCK Toolbar
 */
FPMore.prototype.Execute=function(){
	var oMore = FCK.EditorDocument.createComment( 'more' );
	var oFakeText = FPDocumentProcessor_CreateFakeText( '&lt!--more--&gt', oMore );
	oFakeText = FCK.InsertElement( oFakeText );
}

/**
 * Unknown function, but probably important for FCK
 */
FPMore.prototype.GetState = function(){
	return FCK_TRISTATE_OFF;
}


/**
 * Class that represents next page button in FCK Toolbar
 */
var FPNext = function( strName ){
	this.Name = strName;
}

/**
 * Function that is called when you click FPNext button on FCK Toolbar
 */
FPNext.prototype.Execute=function(){
	var oNext = FCK.EditorDocument.createComment( 'nextpage' );
	var oFakeText = FPDocumentProcessor_CreateFakeText( '&lt!--nextpage--&gt', oNext );
	oFakeText = FCK.InsertElement( oFakeText );
}

/**
 * Unknown function, but probably important for FCK
 */
FPNext.prototype.GetState = function(){
	return FCK_TRISTATE_OFF;
}


/**
 * Class that represents next page button in FCK Toolbar
 */
var FPBreak = function( strName ){
	this.Name = strName;
}

/**
 * Function that is called when you click FPNext button on FCK Toolbar
 */
FPBreak.prototype.Execute=function(){
	var oBreak = FCK.EditorDocument.createComment( 'break' );
	var oFakeText = FPDocumentProcessor_CreateFakeText( '&lt!--break--&gt', oBreak );
	oFakeText = FCK.InsertElement( oFakeText );
}

/**
 * Unknown function, but probably important for FCK
 */
FPBreak.prototype.GetState = function(){
	return FCK_TRISTATE_OFF;
}


/**
 * This is class that will process the text
 */
var FPProcessor = FCKDocumentProcessor.AppendNew();
FPProcessor.ProcessDocument = function( oDocument ){

	function FP_Replace( strMatch, strFirst, strSecond ){
		var iIndex = strSecond.substr( FCKConfig.ProtectedSource._CodeTag.toString().length );
		var strValue = FCKTempBin.Elements[ iIndex ];
		if ( strValue == '<!--more-->' ){
			var oMore = FCKTempBin.AddElement(FCK.EditorDocument.createComment( 'more' ));
			var strFakeText = "<p _fckfakelement='true' _fckrealelement='"+ oMore + "'>&lt!--more--&gt</p>";
			return strFakeText;
		}else if( strValue == '<!--nextpage-->' ){
			var oNext = FCKTempBin.AddElement(FCK.EditorDocument.createComment( 'nextpage' ));
			var strFakeText = "<p _fckfakelement='true' _fckrealelement='"+ oNext + "'>&lt!--nextpage--&gt</p>";
			return strFakeText;
		}else if( strValue == '<!--break-->' ){
			var oBreak = FCKTempBin.AddElement(FCK.EditorDocument.createComment( 'break' ));
			var strFakeText = "<p _fckfakelement='true' _fckrealelement='"+ oBreak + "'>&lt!--break--&gt</p>";
			return strFakeText;
		}else{
			return strMatch;
		}
	}
	
	var content = FCK.EditorDocument.body.innerHTML;
	FCK.EditorDocument.body.innerHTML = content.replace( /(<|&lt;)!--\{(\d+)\}--(>|&gt;)/gm, FP_Replace );
}

///   Addition 26/06/2009
/**
 * Class that represents next page button in FCK Toolbar
 */
var FPPaste = function( strName ){
	this.Name = strName;
	this.State = false;
}

FPPaste.prototype.Execute=function(){
   if(this.State == false) {
      this.State = true;
      FCKConfig.ForcePasteAsPlainText = false;
      //alert('I\'m a rich paster now!');
      	/*if ( FCK.Paste() )
					FCK.ExecuteNamedCommand( 'Paste', null, true ) ;*/

   }
   else {
      this.State = false;
      FCKConfig.ForcePasteAsPlainText = true;
      //alert('I\'m a poor paster now! :(');
   }
   FCKToolbarItems.GetItem('foliopress-paste').RefreshState() ;
   FCK.Focus();
}

FPPaste.prototype.GetState = function(){
	if ( FCKConfig.ForcePasteAsPlainText == false )
      return FCK_TRISTATE_ON;
   else
      return FCK_TRISTATE_OFF;
}
///   End of addition 26/06/2009


/// Registration for FCKEditor of Toolbar buttons and assigning them images

FCKCommands.RegisterCommand( 'foliopress-more', new FPMore( 'foliopress-more' ) );
var oMore = new FCKToolbarButton( 'foliopress-more', 'WordPress Read More', null, null, false, true );
oMore.IconPath = FCKConfig.PluginsPath + 'foliopress-wp/images/more.gif';
FCKToolbarItems.RegisterItem( 'foliopress-more', oMore );

FCKCommands.RegisterCommand( 'foliopress-next', new FPNext( 'foliopress-next' ) );
var oNext = new FCKToolbarButton( 'foliopress-next', 'WordPress Next Page', null, null, false, true );
oNext.IconPath = FCKConfig.PluginsPath + 'foliopress-wp/images/next.gif';
FCKToolbarItems.RegisterItem( 'foliopress-next', oNext );

FCKCommands.RegisterCommand( 'foliopress-break', new FPBreak( 'foliopress-break' ) );
var oNext = new FCKToolbarButton( 'foliopress-break', 'Foliopress Break Page', null, null, false, true );
oNext.IconPath = FCKConfig.PluginsPath + 'foliopress-wp/images/next.gif';
FCKToolbarItems.RegisterItem( 'foliopress-break', oNext );

///   Addition 26/06/2009
FCKCommands.RegisterCommand( 'foliopress-paste', new FPPaste( 'foliopress-paste' ) );
var oPaste = new FCKToolbarButton( 'foliopress-paste', 'Paste Rich Text Mode', null, null, false, true );
oPaste.IconPath = FCKConfig.PluginsPath + 'foliopress-wp/images/rich.png';
FCKToolbarItems.RegisterItem( 'foliopress-paste', oPaste );
///   End of addition 26/06/2009