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

/* 
 * kfmbridge for CKEditor
 * 
*/
CKEDITOR.plugins.add( 'FVWPFlowplayer',
{
    init: function( editor )
    {
        
        
        if( window.parent.g_fv_wp_flowplayer_found == true ) {
            //Flowplayer installed
            if ( editor.contextMenu )
            {
                editor.addMenuGroup( 'mygroup', 10 );
                editor.addMenuItem( 'Inset Flowplayer',
                {
                    label : 'Open dialog',
                    command : 'insetFlowplayer',
                    group : 'mygroup'
                });
                editor.contextMenu.addListener( function( element )
                {
                    return {
                        'Inset Flowplayer' : CKEDITOR.TRISTATE_OFF
                    };
                });
            }
                        
                        
            editor.addCommand( 'insetFlowplayer',
            {
                
                exec : function( editor )
                {
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
                            if( href.match( /type=/ ) ) {
                                href = href.replace( /type=.*?&/, 'type=fv-wp-flowplayer' );
                            } else {  //  for wordpress 3.3
                                href = href.replace( /TB_iframe=/, 'type=fv-wp-flowplayer&amp;TB_iframe=' );
                            }

                            window.parent.jQuery( buttons_id ).append( '<a id="fvwpfp_insert" class="thickbox" href='+href+'" title="Add FV WP Flowplayer"></a>' );
                            window.parent.jQuery( '#fvwpfp_insert' ).click();
                            window.parent.jQuery( '#fvwpfp_insert' ).remove();
                        }

                    }
                }
            });
            
            
        } else {
            //Flowplayer not installed
            editor.addCommand( 'insetFlowplayer',new CKEDITOR.dialogCommand( 'insetFlowplayer' ) );
            
            CKEDITOR.dialog.add('insetFlowplayer',function(){
                return{
                    title:'FV WP Flowplayer',
                    minWidth:500,
                    minHeight:300,
                    contents:[{
                        id:'iframe',
                        label:'FV WP Flowplayer',
                        title:'',
                        expand:true,
                        elements:[{
                            
                            type : 'html',
                            id : 'InsertFlowplayer',
                            label : 'FV WP Flowplayer',
                            style : 'width : 100%; height: 100%;',
                            html : '<iframe src="'+CKEDITOR.plugins.get('FVWPFlowplayer').path+'/dialogs/get_flowplayer.html" frameborder="0" name="InsertFlowplayer" id="InsertFlowplayer" allowtransparency="1"></iframe>'
                        }]
                    }],
                    buttons:[CKEDITOR.dialog.cancelButton]
                };
    
            });
        }
             
        editor.ui.addButton( 'FVWPFlowplayer',
        {
            label: 'Insert Flowplayer',
            command: 'insetFlowplayer',
            icon: this.path + 'icon.png'
        } );
    }
} );


function FCKSetHTMLfp( html ){
    // fixing the buggy InsertHtml in Safari and Chrome, adding empty span solves it (however it creates  <p>&nbsp;</p>)
    if (CKEDITOR.env.webkit) {
        CKEDITOR.instances.content.insertHtml( html+"<p class='cke_remove'></p>" );
    } else {
        CKEDITOR.instances.content.insertHtml( html);
    }
}