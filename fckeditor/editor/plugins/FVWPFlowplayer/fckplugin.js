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
 
/**
 * Class that represents the button in FCK Toolbar
 */
var FVWPFV = function( strName ){
	this.Name = strName;
}

/**
 * Function that is called when you click FPMore button on FCK Toolbar
 * If a#fvwpfp_insert exists, click it. If no, grab any of the other media buttons, convert it to fv-wp-flowplayer type, insert it and click it. 
 */
FVWPFV.prototype.Execute=function(){

    if( window.parent.jQuery( 'a#fvwpfp_insert' ).length > 0 ) {
        window.parent.jQuery( 'a#fvwpfp_insert' ).click();
    } else {
        if( window.parent.jQuery( '#media-buttons a:first-child' ).length > 0 ) {
          buttons_id = '#media-buttons';
        } else if( window.parent.jQuery( '#wp-content-media-buttons a:first-child' ).length > 0 ) {
          buttons_id = '#wp-content-media-buttons';
        }

        if( buttons_id ) {
            var href = window.parent.jQuery( buttons_id+' a:first-child' ).attr( 'href' );
            if( href.match( /media-upload.php/ ) ) {  //  pre WP 3.5
              if( href.match( /type=/ ) ) {
                href = href.replace( /type=.*?&/, 'type=fv-wp-flowplayer' );
              } else {  //  for wordpress 3.3
                href = href.replace( /TB_iframe=/, 'type=fv-wp-flowplayer&amp;TB_iframe=' );
              }       
            } else {
              href= 'media-upload.php?post_id='+window.parent.jQuery('#post_ID').val()+'&type=fv-wp-flowplayer&TB_iframe=true&width=500&height=300';
            }

            window.parent.jQuery( buttons_id ).append( '<a id="fvwpfp_insert" class="thickbox" href='+href+'" title="Add FV WP Flowplayer"></a>' );
            window.parent.jQuery( '#fvwpfp_insert' ).click();
            window.parent.jQuery( '#fvwpfp_insert' ).remove();
        }

    }
}

/**
 * Unknown function, but probably important for FCK
 */
FVWPFV.prototype.GetState = function(){
	return FCK_TRISTATE_OFF;
}
 
if( window.parent.g_fv_wp_flowplayer_found == true ) {
    FCKCommands.RegisterCommand('FVWPFlowplayer',  new FVWPFV('FVWPFV') ) ;
}
else {
    FCKCommands.RegisterCommand('FVWPFlowplayer', new FCKDialogCommand( 'FVWPFlowplayer', 'FV WP Flowplayer', FCKPlugins.Items['FVWPFlowplayer'].Path + 'fv_wp_flowplayer.php', 500, 300 ) ) ;
}


// Create the "Abbr" toolbar button.
var oAbbrItem = new FCKToolbarButton( 'FVWPFlowplayer', 'Insert Flowplayer' ) ;
oAbbrItem.IconPath = FCKPlugins.Items['FVWPFlowplayer'].Path + 'icon.png' ;
FCKToolbarItems.RegisterItem( 'FVWPFlowplayer', oAbbrItem ) ;

// The object used for all Abbr operations.
var FCKFVWPFlowplayer = new Object() ;

// Insert a new Abbr
FCKFVWPFlowplayer.Insert = function(val) {
	FCK.InsertHtml(val);
}
