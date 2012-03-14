/* 
 * kfmbridge for CKEditor
 * 
*/


CKEDITOR.plugins.add( 'kfmbridge',
{
    init: function( editor )
    {
        CKEDITOR.config.filebrowserImageWindowWidth = screen.width * 0.7;
        CKEDITOR.config.filebrowserImageWindowHeight = screen.height * 0.7;
        
        var iWidth  = CKEDITOR.config.filebrowserImageWindowWidth;
        var iHeight = CKEDITOR.config.filebrowserImageWindowHeight;
        var sUrl    = "/wp-content/plugins/foliopress-wysiwyg/ckeditor/plugins/kfm/";
                    
        var iLeft = ( screen.width   - iWidth ) / 2;
        var iTop  = ( screen.height - iHeight ) / 2;

        var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes";
        sOptions += ",width=" + iWidth;
        sOptions += ",height=" + iHeight;
        sOptions += ",left=" + iLeft;
        sOptions += ",top=" + iTop;
             
        editor.ui.addButton( 'Kfmbridge',
        {
            label: 'kfm Bridge',
            command: 'insertKfmbridge',
            icon: this.path + 'images/kfmBridge.png',
            click: function (editor) {
                window.open(sUrl,'FCKBrowseWindow',sOptions);
            }
        } );
    }
} );


function FCKSetHTML( html ){
    CKEDITOR.instances.content.insertHtml( html);
}

