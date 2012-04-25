/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if( typeof FV_Regex == 'undefined' ) FV_includeJs( CKEDITOR_BASEPATH + 'plugins/foliopress-clean/foliovision-regex.js' );

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
            var strText = '';
            //if ( this.mode == 'wysiwyg')
            //    strText = this.getData();
            
            if ( this.mode == 'source') {
                strText = this.getData();
            
                strText = FPClean_ClearTags(strText);
                this.setData(strText);
            }
     
        });
    }
    
} );


function FPClean_ClearTags( strText ){
	if( false == FPClean.bLoaded ) FPClean_LoadConfigs();
	
	var strChange = strText;
	for( var i=0; i<FPClean.aRegexes.length; i++ ){
		strChange = strChange.replace( FPClean.aRegexes[i], "$1" );
	}

	strChange = strChange.replace (/<p>\s*&nbsp;<\/p>/gi,"");
        strChange = strChange.replace (/<p>\s*&#160;<\/p>/gi,"");
        strChange = strChange.replace (/\s\s+/gi,"\n");
        strChange = strChange.replace (/<p>\n/gi,"<p>");

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