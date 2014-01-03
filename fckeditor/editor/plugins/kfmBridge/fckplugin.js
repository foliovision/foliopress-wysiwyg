/**
 *	Custom Comand class for FCK 
 **/

/// ==================   Declaration   ==================

var FCKCustomKFMCommand = function( name ){
   this.Name = name;
}

/// ==================   Implementation   ==================

FCKCustomKFMCommand.prototype.Execute = function(){
   
   var iWidth = FCKConfig.ImageBrowserWindowWidth;
   var iHeight = FCKConfig.ImageBrowserWindowHeight;
   var sUrl = FCKConfig.ImageBrowserURL;
   var oEditor = FCKeditorAPI.GetInstance( FCK.Name );
   
   var iLeft = ( FCKConfig.ScreenWidth  - iWidth ) / 2;
	var iTop  = ( FCKConfig.ScreenHeight - iHeight ) / 2;

	var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes";
	sOptions += ",width=" + iWidth;
	sOptions += ",height=" + iHeight;
	sOptions += ",left=" + iLeft;
	sOptions += ",top=" + iTop;

	if( FCKConfig.PreserveSessionOnFileBrowser && FCKBrowserInfo.IsIE ){
		
		var oWindow = oEditor.window.open( sUrl, 'FCKBrowseWindow', sOptions );

		if( oWindow ){
			try{
				var sTest = oWindow.name;
				oWindow.opener = window;
			}catch( e ){
				alert( FCKLang.BrowseServerBlocked );
				alert( "02" );
			}
		}else{
			alert( FCKLang.BrowseServerBlocked );
		}
   }else{
		if( !window.open( sUrl, 'FCKBrowseWindow', sOptions ) ) alert( FCKLang.BrowseServerBlocked );
	}
}

FCKCustomKFMCommand.prototype.GetState = function(){
   return FCK_TRISTATE_OFF;
}

/// ==================   End of class   ==================

/**
 *	Function that inserts html code directly to cursor location in FCK window
 */
function FCKSetHTML( html ){
//   FCK.InsertHtml( html);
// zUhrikova Foliovision 2010/03/23
// fixing the buggy InsertHtml in Safari and Chrome, adding empty span solves it (however it creates  <p>&nbsp;</p>)
if (FCKBrowserInfo.IsSafari) {
   FCK.InsertHtml( html+"<span></span>" );
   //FCK.InsertHtml( html+"<br />" );
   //FCK.InsertHtml( html+"\r\n" );
   }
else   
   FCK.InsertHtml( html);
}

function kfmBridge_ItIsMe(){
	return true;
}


/***
 *	Special FCK commands for this plugin to work
 ***/
FCKCommands.RegisterCommand( 'kfmBridge', new FCKCustomKFMCommand( 'kfm bridge command' ) );

var oKfmBridgeItem = new FCKToolbarButton( 'kfmBridge', 'Quick Insert Image' ) ;
oKfmBridgeItem.IconPath = FCKPlugins.Items['kfmBridge'].Path + 'kfmBridge.png' ;

FCKToolbarItems.RegisterItem( 'kfmBridge', oKfmBridgeItem ) ;
