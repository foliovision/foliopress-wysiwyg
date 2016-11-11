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







var FPImageWP = function( strName ){
	this.Name = strName;
	this.State = false;
}

FPImageWP.prototype.Execute=function(){
  var img = FCKSelection.GetSelectedElement();
  var h5 = FCKSelection.MoveToAncestorNode('h5');
  var a = FCKSelection.MoveToAncestorNode('a');
  
  var id = img.className.match(/wp-image-(\d+)/);
  if( !id ) return;
  
  var metadata = {
      align: h5 ? h5.className.replace(/.*(left|right|center).*/,'$1') : 'none',
      alt: img.alt,    
      attachment_id: id[1],
      caption: h5 ? h5.innerHTML.replace(/^<a.*?<\/a><br ?\/?>\s*/,'') : false,
      customHeight: img.height,
      customWidth: img.width,
      extraClasses: h5 ? h5.className.replace(/[a-z]*(left|right|center)[a-z]*/,'') : false,
      height: img.height,
      linkUrl: a ? a.href : false,
      size: img.className.replace(/.*size-(\S+).*/,'$1'),
      title: img.title,
      url: img.src,
      width: img.width
      };
  
  var frame = window.parent.wp.media({
    frame: 'image',    
    metadata: metadata,
    state: 'image-details'
    });
  
  frame.on( 'update',function(data) {
    img.alt = data.alt;
    img.height = data.height;
    img.width = data.width;
    img.className = img.className.replace(/size-(\S+)/,'data-'+data.size);
    img.title = data.title;
    img.src = data.url;

    //  todo: img src is not getting updated
    //  todo: customHeight and customWidth
    
    if( a ){
      a.href = data.linkUrl;
      //  todo: is not getting updated as well
    }
    
    if( h5 ) {
      if( data.align != 'none' ) {
        h5.className = h5.className.replace(/(left|right|center)/,data.align);
      } else {
        h5.className = h5.className.replace(/\S*(left|right|center)\S*/,'');
      }
      if( data.caption ) h5.innerHTML = h5.innerHTML.replace(/^(<a.*?<\/a><br ?\/?>\s*).+/,'$1'+data.caption);
      h5.className = h5.className.replace(metadata.extraClasses, data.extraClasses.replace(/\s*$/,'')+' ');
      h5.className = h5.className.replace(/\s*$/,'').replace(/^\s*/,'');
    }    
    
  });  
    
  frame.open();
  
}

FPImageWP.prototype.GetState = function(){
  //return FCK_TRISTATE_ON;
}


FCK.ContextMenu.RegisterListener( {
  AddItems : function( menu, tag, tagName )
  {
    // under what circumstances do we display this option
    if ( tagName == 'IMG' && tag.className.match(/wp-image-(\d+)/) )
    {
      
      /*for( var i in menu._MenuBlock._Items ) {
        if(menu._MenuBlock._Items[i]['Name'] == 'Image' ) {
          delete(menu._MenuBlock._Items[i]);
          console.log('unset!',menu._MenuBlock._Items[i]);
        }
      }*/
      
      menu._MenuBlock._Items.pop();
      menu._MenuBlock._Items.pop();
      
      // when the option is displayed, show a separator  the command
      menu.AddSeparator() ;
      // the command needs the registered command name, the title for the context menu, and the icon path
      menu.AddItem( 'FPImageWP', 'Media Library'/*, oPlaceholderItem.IconPath*/ ) ;
      /*for( var i in menu._MenuBlock._Items ) {
        if(menu._MenuBlock._Items[i]['Name'] == 'Image' ) {
          menu._MenuBlock._Items[i].IsDisabled = true;
          console.log('unset!',menu._MenuBlock._Items[i]);
        }
      }*/
    }
  }}
);

FCKCommands.RegisterCommand( 'FPImageWP', new FPImageWP( 'foliopress-paste' ) );

