/// PLEASE CHANGE THIS FILE ONLY IF YOU KNOW WHAT YOU ARE DOING !!!!!

/*
You can add any ToolbarSet in here and it will load automaticaly into Options
page of FolioPress WYSIWYG. Just make sure that code you add is correct, because
it may cause your Wordpress page to crash.
*/

/*
If you want to add skin, just copy it into default skins directory, it will
automatically load into Options page. Again make sure skin you are adding is
correct.
*/

/*<?php require_once( dirname( __FILE__ ) . '/../foliopress-wysiwyg-class.php' ); ?>*/

<?php $options = get_option( FV_FCK_OPTIONS ); ?>

FCKConfig.ProcessHTMLEntities	= <?php if( $options['ProcessHTMLEntities'] ) echo 'true'; else echo 'false' ?> ;  /*  affects quotes on = &quot;, off = "  */

<?php if( $options[fp_wysiwyg_class::FVC_LANG] != 'auto' ) : ?>
FCKConfig.AutoDetectLanguage	= false ;
FCKConfig.DefaultLanguage		= '<?php echo $options[fp_wysiwyg_class::FVC_LANG]; ?>' ;
<?php else : ?>
FCKConfig.AutoDetectLanguage	= true ;
FCKConfig.DefaultLanguage		= 'en' ;
<?php endif; ?>
FCKConfig.ContentLangDirection	= '<?php echo $options['FCKLangDir']; ?>' ;

/* toolbars */

FCKConfig.ToolbarSets["Default"] = [ 
  ['Source','DocProps','-','Save','NewPage','Preview','-','Templates'], 
	['Cut','Copy','Paste','foliopress-paste','PasteText','PasteWord','-','Print','SpellCheck'], 
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'], 
	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'], 
	'/', 
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'], 
	['OrderedList','UnorderedList','-','Outdent','Indent'], 
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'], 
	['Link','Unlink','Anchor'], 
	['kfmBridge','FVWPFlowplayer','Table','Rule','Smiley','SpecialChar','PageBreak'], 
	'/', 
	['Style','FontFormat','FontName','FontSize'], 
	['TextColor','BGColor']
];

FCKConfig.ToolbarSets["Basic"] = [ 
	['Source', 'Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About'] 
];

FCKConfig.ToolbarSets["Foliovision"] = [
	['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','FontFormat','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','kfmBridge','FVWPFlowplayer','PasteEmbed','-','Source','-','FitWindow']
	//wp_buttons,
];

FCKConfig.ToolbarSets["Foliovision-Full"] = [ 
	['Cut','Copy','Paste','-','Undo','Redo','-','Bold','Italic','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','OrderedList','UnorderedList','-','Outdent','Indent','-','Link','Unlink','Anchor','-','kfmBridge','FVWPFlowplayer'], 
	//['Subscript','Superscript','-', 
	//media_buttons, 
	'/', 
	//['Style'], 
	//['Find','Replace','-', , 
	//['FontName','FontSize'], 
	//['TextColor','BGColor'], 
	//['GoogleSpellCheck'], 
	['FontFormat','RemoveFormat','-','Replace','Table','Rule','SpecialChar','-','foliopress-more','foliopress-next','-','Source','-','FitWindow']
];

FCKConfig.ToolbarSets["Custom"] = [ <?php echo stripslashes($options['customtoolbar']); ?> ];

/* dropdown */
//FCKConfig.FontFormats	= 'h5center;h5left;h5right;p;h1;h2;h3;h4;pre;del;code' ;
FCKConfig.FontFormats	= '<?php echo $options['customdropdown-fontformats']; ?>' ;

FCKConfig.CoreStyles =
{
    // custom formating
	/*'h5left'	: { Element : 'h5', Attributes : { 'class' : 'left' } },
	'h5center'	: { Element : 'h5', Attributes : { 'class' : '' } },
	'h5right'	: { Element : 'h5', Attributes : { 'class' : 'right' } },
	'del'       : { Element : 'del' },
	'code'       : { Element : 'code' },*/
	/* custom stuff here: */
	<?php echo $options['customdropdown-corestyles']; ?>,

	// Basic Inline Styles.
	'Bold'			: { Element : 'strong', Overrides : 'b' },
	'Italic'		: { Element : 'em', Overrides : 'i' },
	'Underline'		: { Element : 'u' },
	'StrikeThrough'	: { Element : 'strike' },
	'Subscript'		: { Element : 'sub' },
	'Superscript'	: { Element : 'sup' },

	// Basic Block Styles (Font Format Combo).
	'p'				: { Element : 'p' },
	'div'			: { Element : 'div' },
	'pre'			: { Element : 'pre' },
	'address'		: { Element : 'address' },
	'h1'			: { Element : 'h1' },
	'h2'			: { Element : 'h2' },
	'h3'			: { Element : 'h3' },
	'h4'			: { Element : 'h4' },
	'h5'			: { Element : 'h5' },
	'h6'			: { Element : 'h6' },

	// Other formatting features.
	'FontFace' :
	{
		Element		: 'span',
		Styles		: { 'font-family' : '#("Font")' },
		Overrides	: [ { Element : 'font', Attributes : { 'face' : null } } ]
	},

	'Size' :
	{
		Element		: 'span',
		Styles		: { 'font-size' : '#("Size","fontSize")' },
		Overrides	: [ { Element : 'font', Attributes : { 'size' : null } } ]
	},

	'Color' :
	{
		Element		: 'span',
		Styles		: { 'color' : '#("Color","color")' },
		Overrides	: [ { Element : 'font', Attributes : { 'color' : null } } ]
	},

	'BackColor'		: { Element : 'span', Styles : { 'background-color' : '#("Color","color")' } },

	'SelectionHighlight' : { Element : 'span', Styles : { 'background-color' : 'navy', 'color' : 'white' } }
};

FCKToolbarFontFormatCombo.prototype.GetStyles = function()
{
	var styles = {} ;

	// Get the format names from the language file.
	var aNames = FCKLang['FontFormats'].split(';') ;
	var oNames = {
		// custom formating
		/*del     : 'Deleted text',
		code    : 'Computer code',
		h5left  : 'H5 Image left',
		h5center: 'H5 Image center',
		h5right : 'H5 Image right',*/
		<?php echo $options['customdropdown-fontformatnames']; ?>,
		
		p		: aNames[0],
		pre		: aNames[1],
		address	: aNames[2],
		h1		: aNames[3],
		h2		: aNames[4],
		h3		: aNames[5],
		h4		: aNames[6],
		h5		: aNames[7],
		h6		: aNames[8],
		div		: aNames[9] || ( aNames[0] + ' (DIV)')
	} ;

	// Get the available formats from the configuration file.
	var elements = FCKConfig.FontFormats.split(';') ;

	for ( var i = 0 ; i < elements.length ; i++ )
	{
		var elementName = elements[ i ] ;
		var style = FCKStyles.GetStyle( '_FCK_' + elementName ) ;
		if ( style )
		{
			style.Label = oNames[ elementName ] ;
			styles[ '_FCK_' + elementName ] = style ;
		}
		else
			alert( "The FCKConfig.CoreStyles['" + elementName + "'] setting was not found. Please check the fckconfig.js file" ) ;
	}

	return styles ;
}


/* This will be applied to the body element of the editor
This is the right way to make your textarea look the same way it does in the browser.
You need to have a unique identifier on your actual final content enclosing element.
You need to configure this with a custom stylesheet - 
use @import to bring in your own stylesheet.
It's quite possible to make this work you will need to rewrite your css file somewhat 
to make all the content area elements use only #content element instead of
div#content element. Good luck! Hopefully future editions of FCK will make
true WYSIWYG easier.
*/
FCKConfig.BodyId = '<?php echo $options['bodyid']; ?>' ;
FCKConfig.BodyClass = '<?php echo $options['bodyclass']; ?>' ;

/// Added for version 0.9.6


/* These are paths you don't want to change unless you really know what you are doing.
You've been warned. */

FCKConfig.ImageBrowserURL = FCKConfig.BasePath+'plugins/kfm/?lang='+FCKConfig.DefaultLanguage+'&kfm_caller_type=fck&type=Image';
FCKConfig.ImageUploadURL = FCKConfig.BasePath+'plugins/kfm/?lang='+FCKConfig.DefaultLanguage+'&kfm_caller_type=fck&type=Image';

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/<?php print( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_SKIN] ); ?>/';

FCKConfig.Plugins.Add( 'kfm' );
FCKConfig.Plugins.Add( 'kfmBridge' );
FCKConfig.Plugins.Add( 'foliopress-wp' );
FCKConfig.Plugins.Add( 'foliopress-paste-embed' );
FCKConfig.EditorAreaCSS = FCKConfig.BasePath + '../../custom-config/foliopress-editor.php';
if( FCKConfig.BodyId || FCKConfig.BodyClass ) {
	//FCKConfig.EditorAreaCSS = '<?php bloginfo('stylesheet_url'); ?>';
	FCKConfig.BodyClass = FCKConfig.BodyClass + ' wysiwyg';
}

FCKConfig.Plugins.Add( 'foliopress-clean' );
<?php  
	if( count( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_FPC_TEXTS] ) ){
		print( 'FCKConfig.FPClean_SpecialText = [' );
		$aFP = $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_FPC_TEXTS];
		$iFP = count( $aFP );
		for( $i=0; $i<$iFP; $i++ ){

			if( $i < $iFP - 1 ) print( " '".$aFP[$i]."'," );
			else print( " '".$aFP[$i]."' " );
		}
		print( "];\n" );
	}
?>
FCKConfig.FPClean_Tags = 'p|div';

FCKConfig.RemoveFormatTags = 'b,big,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var' ;

/// Added for version 0.4
FCKConfig.Plugins.Add( 'foliopress-preformated' );
//FCKConfig.Plugins.Add( 'foliopress-table-cleanup' );
//FCKConfig.Plugins.Add( 'foliopress-word-cleanup' );

FCKConfig.Plugins.Add( 'FVWPFlowplayer' ); 

FCKConfig.Plugins.Add( 'foliopress-rgb-colors-replacer' ); 

<?php die();  //  this is to prevent the w3tc comment to show up ?>
