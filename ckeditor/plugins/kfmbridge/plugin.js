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
        
        var iWidth  = CKEDITOR.instances.content.config.filebrowserImageWindowWidth;
        var iHeight = CKEDITOR.instances.content.config.filebrowserImageWindowHeight;
        var sUrl    = CKEDITOR.instances.content.config.filebrowserBrowseUrl;
                    
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
                kfm_window =  window.open(sUrl,'FCKBrowseWindow',sOptions);
            }
        } );
    }
} );


function FCKSetHTML( html ){
    // fixing the buggy InsertHtml in Safari and Chrome, adding empty span solves it (however it creates  <p>&nbsp;</p>)
    if (CKEDITOR.env.webkit) {
        CKEDITOR.instances.content.insertHtml( html+"<p class='cke_remove'>&nbsp;</p>" );
    } else {
        CKEDITOR.instances.content.insertHtml( html);
    }
}
