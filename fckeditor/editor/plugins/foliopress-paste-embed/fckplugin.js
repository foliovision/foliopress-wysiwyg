/*
 * Foliopress WYSIWYG FCKeditor extension
 * Copyright (C) 2011 Foliovision
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 *    http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 *    http://foliovision.com/
 * 
 * File Name: fckplugin.js
 * 
 */
  
FCKCommands.RegisterCommand('PasteEmbed', new FCKDialogCommand( 'PasteEmbed', 'Paste Embed Code', FCKPlugins.Items['foliopress-paste-embed'].Path + 'foliopress-paste-embed.html', 500, 300 ) ) ;

// Create the "Abbr" toolbar button.
var oPasteEmbed = new FCKToolbarButton( 'PasteEmbed', 'Insert Flowplayer' ) ;
oPasteEmbed.IconPath = FCKPlugins.Items['foliopress-paste-embed'].Path + 'icon.png' ;
FCKToolbarItems.RegisterItem( 'PasteEmbed', oPasteEmbed ) ;

// The object used for all Abbr operations.
var FCKFVWPFlowplayer = new Object() ;

// Insert a new Abbr
FCKFVWPFlowplayer.Insert = function(val) {
	FCK.InsertHtml(val);
}