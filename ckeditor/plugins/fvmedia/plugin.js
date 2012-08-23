
CKEDITOR.plugins.add( 'fvmedia',
{
    requires  : [ 'fakeobjects', 'htmldataprocessor' ],
    
    
    init: function( editor )
    {
        
        
        
        // This function effectively inserts the comment into the editor.
        function insertComment( text )
        {
            // Create the fake element that will be inserted into the document.
            // The trick is declaring it as an <hr>, so it will behave like a
            // block element (and in effect it behaves much like an <hr>).
            if ( !CKEDITOR.dom.comment.prototype.getAttribute ) {
                CKEDITOR.dom.comment.prototype.getAttribute = function() {
                    return '';
                };
                CKEDITOR.dom.comment.prototype.attributes = {
                    align : ''
                };
            }
            var fakeElement = editor.createFakeElement( new CKEDITOR.dom.comment( text ), 'cke_wordpress_' + text, 'hr' );

            // This is the trick part. We can't use editor.insertElement()
            // because we need to put the comment directly at <body> level.
            // We need to do range manipulation for that.

            // Get a DOM range from the current selection.
            var range = editor.getSelection().getRanges()[0],
            elementsPath = new CKEDITOR.dom.elementPath( range.getCommonAncestor( true ) ),
            element = ( elementsPath.block && elementsPath.block.getParent() ) || elementsPath.blockLimit,
            hasMoved;

            // If we're not in <body> go moving the position to after the
            // elements until reaching it. This may happen when inside tables,
            // lists, blockquotes, etc.
            while ( element && element.getName() != 'body' )
            {
                range.moveToPosition( element, CKEDITOR.POSITION_AFTER_END );
                hasMoved = 1;
                element = element.getParent();
            }

            // Split the current block.
            if ( !hasMoved )
                range.splitBlock( 'p' );

            // Insert the fake element into the document.
            range.insertNode( fakeElement );

            // Now, we move the selection to the best possible place following
            // our fake element.
            var next = fakeElement;
            while ( ( next = next.getNext() ) && !range.moveToElementEditStart( next ) )
            {}

            range.select();
        }
    },

    afterInit : function( editor )
    {
        // Adds the comment processing rules to the data filter, so comments
        // are replaced by fake elements.
        editor.dataProcessor.dataFilter.addRules(
        {
            comment : function( value )
            {
                if ( !CKEDITOR.htmlParser.comment.prototype.getAttribute ) {
                    CKEDITOR.htmlParser.comment.prototype.getAttribute = function() {
                        return '';
                    };
                    CKEDITOR.htmlParser.comment.prototype.attributes = {
                        align : ''
                    };
                }
                if ( value == 'nextpage' )
                    return editor.createFakeParserElement( new CKEDITOR.htmlParser.comment( value ), 'cke_wordpress_' + value, 'hr' );

                return value;
            }
        });
    }
    
} );
