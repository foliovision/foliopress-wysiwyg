/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if( typeof FV_Regex == 'undefined' ) FV_includeJs( CKEDITOR_BASEPATH + 'plugins/foliopress-clean/foliovision-regex.js' );

var current_mode = CKEDITOR.instances.content.mode;
var mode_change_on_load = false;

STR_REGEXP_LT = "(?:\\x3C)";
STR_REGEXP_GT = "(?:\\x3E)";
STR_REGEXP_FS = "(?:\\x2F)";
STR_DEFAULT_TAGS = "p|div"

var FPClean = new Object();
FPClean.bLoaded = false;
FPClean.aRegexes = new Array();
FPClean.strTags = '';


CKEDITOR.plugins.add( 'foliopress-clean',
{
    requires  : [ 'fakeobjects', 'htmldataprocessor' ],
    
    afterInit : function( editor )
    {
        // Adds the comment processing rules to the data filter, so comments
        // are replaced by fake elements.
        
        editor.on( 'mode', function( e )
        {
            if( mode_change_on_load === false || current_mode != this.mode ) {
                var strText = '';

                if ( this.mode == 'source') {
                    strText = this.getData();

                    strText = media_source_filter(strText);
                    strText = FPClean_ClearTags(strText);
                    this.setData(strText);
                }
                if ( this.mode == 'wysiwyg') {
                    strText = this.getData();
                    strText = FPClean_ClearEmptyTags( media_wysiwyg_filter(strText));
                    this.setData(strText);
                    
                }
                
                mode_change_on_load = true;
                current_mode = this.mode;
                //console.log(strText);
            }
     
        });
        
        editor.on('insertText', function(e) {
              //console.log('insertText');
        });
        
        //CKEDITOR.editor.prototype.dataReady
        editor.on('insertHtml', function(e) {
            if(this.mode == 'wysiwyg') {
                e.data = media_wysiwyg_filter(e.data);
            } else {
                //console.log('WP media in source mode');
                TextToInsert = e.data;
                var input = document.getElementsByClassName('cke_source cke_enable_context_menu')[0];
                input.focus();
                if(typeof input.selectionStart != 'undefined')
                {
                    var start = input.selectionStart;
                    var end = input.selectionEnd;

                    input.value = input.value.substr(0, start) + TextToInsert + input.value.substr(end);
                    
                    var pos;

                    pos = start+TextToInsert.length;

                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
            }
            //console.log('insertHtml');
        });
        
        
        editor.on('removeFormatCleanup', function(e) {
            //console.log('removeFormatCleanup');
        });
    }
    
} );

var current_mode = CKEDITOR.instances.content.mode;

function media_wysiwyg_filter(content) {
    
    return content.replace(/(?:<p>)?\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\](?:<\/p>)?/g, function(a,b,c){
        var id, cls, w, cap, div_cls, img;

        id = b.match(/id=['"]([^'"]*)['"] ?/);
        b = b.replace(id[0], '');

        cls = b.match(/align=['"]([^'"]*)['"] ?/);
        b = b.replace(cls[0], '');

        w = b.match(/width=['"]([0-9]*)['"] ?/);
        b = b.replace(w[0], '');

        c = trim_text(c);
        img = c.match(/((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i);

        if ( img && img[2] ) {
            cap = trim_text( img[2] );
            img = trim_text( img[1] );
        } else {
            // old captions shortcode style
            cap = trim_text(b).replace(/caption=['"]/, '').replace(/['"]$/, '');
            img = c;
        }

        id = ( id && id[1] ) ? id[1] : '';
        cls = ( cls && cls[1] ) ? cls[1] : 'alignnone';
        w = ( w && w[1] ) ? w[1] : '';

        if ( !w || !cap )
            return c;

        div_cls = 'mceTemp';
        if ( cls == 'aligncenter' )
            div_cls += ' mceIEcenter';
        return '<div id="'+id+'" class="wp-caption '+cls+'" style="width: '+( 10 + parseInt(w) )+
        'px">'+img+'<p class="wp-caption-dd">'+cap+'</p></div>';
        //return '<div class="'+div_cls+'"><div id="'+id+'" class="wp-caption '+cls+'" style="width: '+( 10 + parseInt(w) )+
        //'px">'+img+'<p class="wp-caption-dd">'+cap+'</p></div></div>';
    });
}
                
function trim_text(str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}


function media_source_filter(content) {
    
    //return content.replace(/<div (?:id="attachment_|class="mceTemp)[^>]*>([\s\S]+?)<\/div>/g, function(a, b){
      //  b = b+'</div>';
        //var ret = b.replace(/<dl ([^>]+)>\s*<dt [^>]+>([\s\S]+?)<\/dt>\s*<dd [^>]+>([\s\S]*?)<\/dd>\s*<\/dl>/gi, function(a,b,c,cap){
        return content.replace(/<div ([^>]+)>\s*([\s\S]+?)\s*<p [^>]+>([\s\S]*?)<\/p>\s*<\/div>/gi, function(a,b,c,cap){
            var id, cls, w;
            
            //console.log(a);
            //console.log(b);
            //console.log(c);
            //console.log(cap);
                                       
            w = c.match(/width="([0-9]*)"/);
            w = ( w && w[1] ) ? w[1] : '';

            if ( !w || !cap )
                return c;
                                       
            id = b.match(/id="([^"]*)"/);
            id = ( id && id[1] ) ? id[1] : '';
                                        
                                        

            cls = b.match(/class="([^"]*)"/);
            cls = ( cls && cls[1] ) ? cls[1] : '';
            cls = cls.match(/align[a-z]+/) || 'alignnone';
                                        

            cap = cap.replace(/\r\n|\r/g, '\n').replace(/<[a-zA-Z0-9]+( [^<>]+)?>/g, function(a){
                // no line breaks inside HTML tags
                return a.replace(/[\r\n\t]+/, ' ');
            });
                                        
            // convert remaining line breaks to <br>
            //cap = cap.replace(/\s*\n\s*/g, ' ');
            cap = cap.replace(/\s*\n\s*/g, '<br />');
            cap = cap.replace(/(<br\s*\/?>\s*)+/gi, '<br />');

            //console.log('[caption id="'+id+'" align="'+cls+'" width="'+w+'"]'+c+' '+cap+'[/caption]');                            
            return '[caption id="'+id+'" align="'+cls+'" width="'+w+'"]'+c+' '+cap+'[/caption]';
        }); /*
        //return ret;
        //if ( ret.indexOf('[caption') !== 0 ) {
            // the caption html seems brocken, try to find the image that may be wrapped in a link
            // and may be followed by <p> with the caption text.
            //ret = b.replace(/[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]    /gi, '<p>$1</p>$2');
        //}
        //console.log(ret);
        //return ret;*/
    }
    
function FPClean_ClearEmptyTags( strText ){
    var strChange = strText;
    strChange = strChange.replace (/<p>\s*&nbsp;<\/p>/gi,"");
    strChange = strChange.replace (/<p>\s*&#160;<\/p>/gi,"");
    strChange = strChange.replace (/<p[^>]*>\s*<br\s*[\/]?>\s*<\/p>/gi,"");
    //strChange = strChange.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    strChange = strChange.replace (/\s\s+/gi,"\n");
    strChange = strChange.replace (/<p>\n/gi,"<p>");
    strChange = strChange.replace (/<p>\s*(<br>|<br*\/>)\s*<\/p>/gi,"");
    strChange = strChange.replace (/<p><\/p>/gi,"");
    //strChange = strChange.replace (/<span style=\"display: none;\s*\">\s*<\/span>/gi,"");
    strChange = strChange.replace (/<span[^>]*>\s*&nbsp;<\/span>/gi,"");
    strChange = strChange.replace (/<div\s+id=\"wrc-float-icon\".*?>.*?<\/div>/gi, '');//safari/chrome clean up
    

    return strChange;
}

function FPClean_ClearTags( strText ){
    if( false == FPClean.bLoaded ) FPClean_LoadConfigs();
	
    var strChange = strText;
    for( var i=0; i<FPClean.aRegexes.length; i++ ){
        strChange = strChange.replace( FPClean.aRegexes[i], "$1" );
    }

    strChange = strChange.replace (/<p>\s*&nbsp;<\/p>/gi,"");
    strChange = strChange.replace (/<p>\s*&#160;<\/p>/gi,"");
    strChange = strChange.replace (/<p[^>]*>\s*<br\s*[\/]?>\s*<\/p>/gi,"");
    //strChange = strChange.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    strChange = strChange.replace (/\s\s+/gi,"\n");
    strChange = strChange.replace (/<p>\n/gi,"<p>");
    strChange = strChange.replace (/<p>\s*(<br>|<br*\/>)\s*<\/p>/gi,"");
    strChange = strChange.replace (/<p><\/p>/gi,"");
    //strChange = strChange.replace (/<span style=\"display: none;\s*\">(&nbsp;|\s*)<\/span>/gi,"");
    strChange = strChange.replace (/<span[^>]*>\s*&nbsp;<\/span>/gi,"");
    strChange = strChange.replace (/<div\s+id=\"wrc-float-icon\".*?>.*?<\/div>/gi, '');//safari/chrome clean up

    return strChange;
}

function FPClean_LoadConfigs(){
    if( typeof CKEDITOR.instances.content.config.FPClean_SpecialText == 'undefined' ) return;
    if( typeof FV_Regex == 'undefined' ) return;
    if( typeof CKEDITOR.instances.content.config.FPClean_Tags == 'undefined' ) FPClean.strTags = STR_DEFAULT_TAGS;
    else FPClean.strTags = CKEDITOR.instances.content.config.FPClean_Tags;
	
    var strREText = '';
    for( var i=0; i<CKEDITOR.instances.content.config.FPClean_SpecialText.length; i++ ){
        strREText = STR_REGEXP_LT + "(?:" + FPClean.strTags + ")" + STR_REGEXP_GT + "(";
        strREText += "[\\s\\n]*?";
        strREText += FV_Regex.ConvertString( CKEDITOR.instances.content.config.FPClean_SpecialText[i] );
        strREText += ")" + STR_REGEXP_LT + STR_REGEXP_FS + "(?:" + FPClean.strTags + ")" + STR_REGEXP_GT;
        FPClean.aRegexes.push( new RegExp( strREText, "g" ) );
    }
	
    FPClean.bLoaded = true;
}

function FV_includeJs(jsFilePath) {
    var js = document.createElement("script");

    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}