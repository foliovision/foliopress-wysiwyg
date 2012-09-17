/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Justify commands.
 */

(function()
{
    function getAlignment( element, useComputedState )
    {
        try {
            //check if is selected caption (or any element inside div 
            var captionSelected = false;
                
            //console.log(element);

            if (element.is('div') && element.hasClass('wp-caption')) {
                //alert('ie-div');
                var caption = element;
                var captionSelected = true;
            }
            if (!captionSelected) {
                var parent = element.getParent();
                if (parent.is('div') && parent.hasClass('wp-caption')) {
                    //alert('par-div');
                    var caption = parent;
                    var captionSelected = true;
                }
            }
            if (!captionSelected) {
                var wrap = parent.getParent();
                if (wrap.hasClass('wp-caption')) {
                    //alert('wrap-div');
                    var caption = wrap;
                    var captionSelected = true;
                }
            }
            
            //console.log(captionSelected);
                
            useComputedState = useComputedState === undefined || useComputedState;

            var align;
            if (captionSelected) {
                align = caption.getAttribute('class').replace('wp-caption', '').replace(/\s*/, '').replace('align','');
            } else if ( useComputedState ) {
                align = element.getComputedStyle( 'text-align' );
                        
            } else {
                while ( !element.hasAttribute || !( element.hasAttribute( 'align' ) || element.getStyle( 'text-align' ) ) )
                {
                    var parent = element.getParent();
                    if ( !parent )
                        break;
                    element = parent;
                }
                align = element.getStyle( 'text-align' ) || element.getAttribute( 'align' ) || '';
            }

            // Sometimes computed values doesn't tell.
            align && ( align = align.replace( /(?:-(?:moz|webkit)-)?(?:start|auto)/i, '' ) );

            !align && useComputedState && ( align = element.getComputedStyle( 'direction' ) == 'rtl' ? 'right' : 'left' );
            
            console.log(align);
            return align;
        }
        catch (e)
        {
            console.log(e.message);
        }
    }

    function onSelectionChange( evt )
    {
        if ( evt.editor.readOnly )
            return;
        
        if (!CKEDITOR.env.webkit && CKEDITOR.instances.content.getSelection().getType() == CKEDITOR.SELECTION_ELEMENT ) {
            console.log( 'An element is selected' );
            var elem = CKEDITOR.instances.content.getSelection().getSelectedElement();
        //console.log(elem);
        //console.log('ss');
        }
         
        evt.editor.getCommand( this.name ).refresh2( evt.data.path );
    }

    function justifyCommand( editor, name, value )
    {
        this.editor = editor;
        this.name = name;
        this.value = value;
                

        var classes = editor.config.justifyClasses;
        if ( classes )
        {
            switch ( value )
            {
                case 'left' :
                    this.cssClassName = classes[0];
                    break;
                case 'center' :
                    this.cssClassName = classes[1];
                    break;
                case 'right' :
                    this.cssClassName = classes[2];
                    break;
                case 'justify' :
                    this.cssClassName = classes[3];
                    break;
            }

            this.cssClassRegex = new RegExp( '(?:^|\\s+)(?:' + classes.join( '|' ) + ')(?=$|\\s)' );
        }
    }

    function onDirChanged( e )
    {
        var editor = e.editor;

        var range = new CKEDITOR.dom.range( editor.document );
        range.setStartBefore( e.data.node );
        range.setEndAfter( e.data.node );

        var walker = new CKEDITOR.dom.walker( range ),
        node;

        while ( ( node = walker.next() ) )
        {
            if ( node.type == CKEDITOR.NODE_ELEMENT )
            {
                // A child with the defined dir is to be ignored.
                if ( !node.equals( e.data.node ) && node.getDirection() )
                {
                    range.setStartAfter( node );
                    walker = new CKEDITOR.dom.walker( range );
                    continue;
                }

                // Switch the alignment.
                var classes = editor.config.justifyClasses;
                if ( classes )
                {
                    // The left align class.
                    if ( node.hasClass( classes[ 0 ] ) )
                    {
                        node.removeClass( classes[ 0 ] );
                        node.addClass( classes[ 2 ] );
                    }
                    // The right align class.
                    else if ( node.hasClass( classes[ 2 ] ) )
                    {
                        node.removeClass( classes[ 2 ] );
                        node.addClass( classes[ 0 ] );
                    }
                }

                // Always switch CSS margins.
                var style = 'text-align';
                var align = node.getStyle( style );

                if ( align == 'left' )
                    node.setStyle( style, 'right' );
                else if ( align == 'right' )
                    node.setStyle( style, 'left' );
            }
        }
    }

    justifyCommand.prototype = {
        exec : function( editor )
        {
            //console.log(this.value);
            var selection = editor.getSelection();
            var enterMode = editor.config.enterMode;
                                
            if (CKEDITOR.env.ie) {
                selection.unlock(true);
            }

            if ( !selection )
                return;
                            
            var bookmarks = selection.createBookmarks(),
            ranges = selection.getRanges( true );

            var cssClassName = this.cssClassName,
            iterator,
            block;
                            
            //check if is selected caption (or any element inside div 
            var captionSelected = false;
            var el = selection.getStartElement();
            if (!CKEDITOR.env.webkit && CKEDITOR.instances.content.getSelection().getType() == CKEDITOR.SELECTION_ELEMENT ) {
                //console.log( 'An element is selected' );
                var el = CKEDITOR.instances.content.getSelection().getSelectedElement();
                console.log('el');
                console.log(el);
            }
            console.log('aaa');
            console.log(CKEDITOR.instances.content.getSelection().getType());
            console.log(CKEDITOR.SELECTION_ELEMENT);
            

            if (el.is('div') && el.hasClass('wp-caption')) {
                //alert('ie-div');
                var caption = el;
                var captionSelected = true;
            }
            if (!captionSelected) {
                var parent = el.getParent();
                if (parent.is('div') && parent.hasClass('wp-caption')) {
                    //alert('par-div');
                    var caption = parent;
                    var captionSelected = true;
                }
            }
            if (!captionSelected) {
                var wrap = parent.getParent();
                if (wrap.hasClass('wp-caption')) {
                    //alert('wrap-div');
                    var caption = wrap;
                    var captionSelected = true;
                }
            }
            if (captionSelected) {
                var align = caption.getAttribute('class').replace('wp-caption', '').replace(/\s*/, '');
                                
                caption.removeClass(align);
                if(this.value != 'justify') {
                    if(align == 'align'+this.value) {
                        caption.addClass('alignnone');
                    } else {
                        caption.addClass('align'+this.value);
                    }
                        
                } else {
                    console.log('do nothing');
                }
                //console.log(align);
                //console.log(this.value);
                //console.log(caption);
                        
            
                return;
            } else {
                //default justify action

                var useComputedState = editor.config.useComputedState;
                useComputedState = useComputedState === undefined || useComputedState;

                for ( var i = ranges.length - 1 ; i >= 0 ; i-- )
                {
                    iterator = ranges[ i ].createIterator();
                    iterator.enlargeBr = enterMode != CKEDITOR.ENTER_BR;

                    while ( ( block = iterator.getNextParagraph( enterMode == CKEDITOR.ENTER_P ? 'p' : 'div' ) ) )
                    {
                        block.removeAttribute( 'align' );
                        block.removeStyle( 'text-align' );

                        // Remove any of the alignment classes from the className.
                        var className = cssClassName && ( block.$.className =
                            CKEDITOR.tools.ltrim( block.$.className.replace( this.cssClassRegex, '' ) ) );

                        var apply =
                        ( this.state == CKEDITOR.TRISTATE_OFF ) &&
                        ( !useComputedState || ( getAlignment( block, true ) != this.value ) );

                        if ( cssClassName )
                        {
                            // Append the desired class name.
                            if ( apply )
                                block.addClass( cssClassName );
                            else if ( !className )
                                block.removeAttribute( 'class' );
                        }
                        else if ( apply )
                            block.setStyle( 'text-align', this.value );
                    }

                }
            }

            editor.focus();
            editor.forceNextSelectionCheck();
            selection.selectBookmarks( bookmarks );
        },

        refresh2 : function( path )
        {
            
            var firstBlock = path.block || path.blockLimit;
            
            this.setState( firstBlock.getName() != 'body' &&
                getAlignment( firstBlock, this.editor.config.useComputedState ) == this.value ?
                CKEDITOR.TRISTATE_ON :
                CKEDITOR.TRISTATE_OFF );
            
        }
    };

    CKEDITOR.plugins.add( 'fvjustify',
    {
        init : function( editor )
        {
            var left = new justifyCommand( editor, 'justifyleft', 'left' ),
            center = new justifyCommand( editor, 'justifycenter', 'center' ),
            right = new justifyCommand( editor, 'justifyright', 'right' ),
            justify = new justifyCommand( editor, 'justifyblock', 'justify' );

            editor.addCommand( 'justifyleft', left );
            editor.addCommand( 'justifycenter', center );
            editor.addCommand( 'justifyright', right );
            editor.addCommand( 'justifyblock', justify );

            editor.ui.addButton( 'JustifyLeft',
            {
                label : editor.lang.justify.left,
                command : 'justifyleft'
            } );
            editor.ui.addButton( 'JustifyCenter',
            {
                label : editor.lang.justify.center,
                command : 'justifycenter'
            } );
            editor.ui.addButton( 'JustifyRight',
            {
                label : editor.lang.justify.right,
                command : 'justifyright'
            } );
            editor.ui.addButton( 'JustifyBlock',
            {
                label : editor.lang.justify.block,
                command : 'justifyblock'
            } );

            editor.on( 'selectionChange', CKEDITOR.tools.bind( onSelectionChange, left ) );
            editor.on( 'selectionChange', CKEDITOR.tools.bind( onSelectionChange, right ) );
            editor.on( 'selectionChange', CKEDITOR.tools.bind( onSelectionChange, center ) );
            editor.on( 'selectionChange', CKEDITOR.tools.bind( onSelectionChange, justify ) );
            editor.on( 'dirChanged', onDirChanged );
        },

        requires : [ 'domiterator' ]
    });
})();

 /**
 * List of classes to use for aligning the contents. If it's null, no classes will be used
 * and instead the corresponding CSS values will be used. The array should contain 4 members, in the following order: left, center, right, justify.
 * @name CKEDITOR.config.justifyClasses
 * @type Array
 * @default null
 * @example
 * // Use the classes 'AlignLeft', 'AlignCenter', 'AlignRight', 'AlignJustify'
 * config.justifyClasses = [ 'AlignLeft', 'AlignCenter', 'AlignRight', 'AlignJustify' ];
 */
