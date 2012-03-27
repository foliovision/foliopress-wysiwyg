/*
* @example An iframe-based dialog with custom button handling logics.
*/
( function() {
    CKEDITOR.plugins.add( 'fvpasteembed',
    {
        requires: [ 'iframedialog' ],
        init: function( editor )
        {
            var me = this;
            CKEDITOR.dialog.add( 'MediaEmbedDialog', function ()
            {
                return {
                    title : 'Paste Embed Code',
                    minWidth : 550,
                    minHeight : 200,
                    contents :
                    [
                    {
                        id : 'iframe',
                        label : 'Insert Flowplayer',
                        expand : true,
                        elements :
                        [
                        {
                            type : 'html',
                            id : 'pageFvpasteembed',
                            label : 'Insert Embedded Code',
                            style : 'width : 100%;',
                            html : '<iframe src="'+me.path+'/dialogs/mediaembed.html" frameborder="0" name="iframeMediaEmbed" id="iframeMediaEmbed" allowtransparency="1" style="width:100%;margin:0;padding:0;"></iframe>'
                        }
                        ]
                    }
                    ],
                    onOk : function()
                    {
                        for (var i=0; i<window.frames.length; i++) {
                            if(window.frames[i].name == 'iframeMediaEmbed') {
                                var content = window.frames[i].document.getElementById("embed").value;
                            }
                        }
                        final_html = content;
                        editor.insertHtml(final_html);
                    }
                };
            } );

            editor.addCommand( 'Fvpasteembed', new CKEDITOR.dialogCommand( 'MediaEmbedDialog' ) );

            editor.ui.addButton( 'Fvpasteembed',
            {
                label: 'Insert Embedded Code',
                command: 'Fvpasteembed',
                icon: this.path + 'images/icon.png'
            } );
        }
    } );
} )();
