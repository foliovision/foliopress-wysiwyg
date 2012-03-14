
CKEDITOR.plugins.add( 'fvmore',
{
    init: function( editor )
    {
        editor.addCommand( 'insertFvmore',
        {
            exec : function( editor )
            {    
                editor.insertHtml( '<!--more-->' );
            }
        });
        editor.ui.addButton( 'Fvmore',
        {
            label: 'WordPress Read More',
            command: 'insertFvmore',
            icon: this.path + 'images/fv_more.png'
        } );
    }
} );
