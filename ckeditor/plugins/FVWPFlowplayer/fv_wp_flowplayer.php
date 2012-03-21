<?php
//	WP < 2.7 fix
if( file_exists( dirname(__FILE__) . '/../../../../../../wp-load.php' ) )
	require_once( realpath( dirname(__FILE__) . '/../../../../../../wp-load.php' ) );
else
	require_once( realpath( dirname(__FILE__) . '/../../../../../../wp-config.php' ) );

global $current_user;

if(!$current_user->id)
    die('Access denied.');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>FV WP Flowplayer</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex, nofollow" name="robots">

<style type="text/css">
/*#message, #inputfield, #checkbox { display: none; }*/
abbr { border-bottom: 1px dotted rgb(102, 102, 102); cursor: help; }
</style>

</head>
<body scroll="no" style="OVERFLOW: hidden">
<?php 

$plugins = get_option('active_plugins');$found = false;

foreach ( $plugins AS $plugin ) {
	if( stripos($plugin,'fv-wordpress-flowplayer') !== FALSE )
		$found = true;
}
if($found) {
?>
<table height="100%" cellSpacing="0" cellPadding="0" width="100%" border="0">
<tr>
<td>
<table cellSpacing="2" cellPadding="2" align="center" width="100%" border="0">
	<tr>
		<td width="100">Filename <small>(in /videos/)</small>:</td><td><input id="src" name="src" style="width: 99%" /></td>
	</tr>
	<tr>
		<td>Width <small>(px; optional)</small>:</td><td><input id="width" name="width" style="width: 99%" /></td>
	</tr>
	<tr>
		<td>Height <small>(px; optional)</small>:</td><td><input id="height" name="height" style="width: 99%" /></td>
	</tr>
	<tr>
		<td>Splash Image:</td><td><input id="splash" name="splash" style="width: 99%" /></td>
	</tr>
	<tr>
		<td valign="top">HTML Popup:</td><td><textarea style="width: 100%; height: 5eM;" id="popup" name="popup"></textarea></td>
	</tr>
</table>
</td>
</tr>
</table>
<?php } else { ?>
<p><strong>FV Wordpress Flowplayer not installed and activated!</strong></p>
<p>We have the best opensource flash video player for Wordpress - a free, easy-to-use, and complete solution for embedding FLV or MP4 videos into your posts or pages.</p>
<p>Main benefits of using FV WP Flowplayer are:</p>
<ul>
	<li>Supported video formats are FLV, H.264, and MP4. Multiple videos can be displayed in one post or page.</li>
	<li>Default options for all the embedded videos can be set in comprehensive administration menu.</li>
	<li>Simple usage not only in posts, but also templates and widgets.</li>
	<li>Supports splash images and popup boxes.</li>
</ul>
<p><a href="http://foliovision.com/seo-tools/wordpress/plugins/fv-wordpress-flowplayer" target=_blank>Read more</a> | <a href="http://downloads.wordpress.org/plugin/fv-wordpress-flowplayer.zip" target=_blank>Download now</a> | 
<a href="<?php bloginfo('wpurl') ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=fv-wordpress-flowplayer&TB_iframe=true&width=640&height=671" target=_blank>Install in a new window</a></p>
<?php } ?>
</body>

<script language="javascript">

var oEditor	= window.parent.InnerDialogLoaded() ;
var FCKLang	= oEditor.FCKLang ;
var FCKFVWPFlowplayer = oEditor.FCKFVWPFlowplayer ;
var content_original;
//var re = /\[flowplayer[^\[]*?<span id="FCKFVWPFlowplayerPlaceholder">.*?<\/span>[^\[]*?\]/mi;
//var re = /\[flowplayer[^\[]*?<span id=.?FCKFVWPFlowplayerPlaceholder.*?<\/span>[^\[]*?\]/mi;
var re = /\[flowplayer[^\[]*?<span id[^>]*?FCKFVWPFlowplayerPlaceholder.*?<\/span>[^\[]*?\]/mi;
var re2 = /<span id[^>]*?FCKFVWPFlowplayerPlaceholder.?>.*?<\/span>/gi;

Trick();
window.onload = function () {
	<?php if($found) : ?>
	window.parent.SetOkButton( true ) ;
	document.getElementById('src').focus();
	<?php endif; ?>
}

function Trick() {
  FCK = oEditor.FCK;
  oEditor.FCKUndo.SaveUndoStep() ;

	oText = FCK.EditorDocument.createElement( 'span' );
	oText.setAttribute( 'id', 'FCKFVWPFlowplayerPlaceholder', 0 );
	oFakeText = FCK.InsertElement( oText );
	//FCK = oEditor.FCK;
	//FCK.InsertHtml('<span id="FCKFVWPFlowplayerPlaceholder"></span>');
	
	//var placeholder = FCK.EditorDocument.createElement('span');
	//alert( FCK.EditorDocument.body.innerHTML );
	//placeholder.setAttribute( 'id', 'FCKFVWPFlowplayerPlaceholder' );
	//alert( FCK.EditorDocument.body.innerHTML );
	//placeholder.innerHTML = 'asdf';
	//alert( FCK.EditorDocument.body.innerHTML );

	content_original = FCK.EditorDocument.body.innerHTML;

	content = content_original.replace(/\n/g,'\uffff');
     
	var shortcode = content.match( re );   
	//alert(shortcode);
	
	FCK.EditorDocument.body.innerHTML = FCK.EditorDocument.body.innerHTML.replace( re2,'' );
	

	if( shortcode != null ) {
		shortcode = shortcode.join('');
		shortcode = shortcode.replace( /\\'/g,'&#039;' );
		
		shortcode = shortcode.replace( re2,'' );
		//alert(shortcode);
		srcurl = shortcode.match( /src='([^']*)'/ );
		if( srcurl == null )
			srcurl = shortcode.match( /src=([^,\]\s]*)/ );
		
		iheight = shortcode.match( /height=(\d*)/ );
		
		iwidth = shortcode.match( /width=(\d*)/ );
		
		ssplash = shortcode.match( /splash='([^']*)'/ );
		if( ssplash == null )
			ssplash = shortcode.match( /splash=([^,\]\s]*)/ );
		
		spopup = shortcode.match( /popup='([^']*)'/ );

		//alert( srcurl[1] + '\n' + iheight[1] + '\n' + iwidth[1] + '\n' + splash[1] + '\n' + popup[1] );
	
		if( srcurl != null && srcurl[1] != null )
			document.getElementById("src").value = srcurl[1];
		if( iheight != null && iheight[1] != null )
			document.getElementById("height").value = iheight[1];
		if( iwidth != null && iwidth[1] != null )
			document.getElementById("width").value = iwidth[1];
		if( ssplash != null && ssplash[1] != null )
			document.getElementById("splash").value = ssplash[1];
		if( spopup != null && spopup[1] != null ) {
			spopup = spopup[1].replace(/&#039;/g,'\'').replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
			spopup = spopup.replace(/&amp;/g,'&');
			document.getElementById("popup").value = spopup;
		}
	}

}

function Ok() {
	
	var shortcode = '';
	
	if(document.getElementById("src").value == '') {
		alert('Please enter the file name of your video file.');
		return false;
	}
	else
		shortcode = '[flowplayer src=\'' + document.getElementById("src").value + '\'';
		
	if( document.getElementById("width").value != '' && document.getElementById("width").value % 1 != 0 ) {
		alert('Please enter a valid width.');
		return false;
	}
	if( document.getElementById("width").value != '' )
		shortcode += ' width=' + document.getElementById("width").value;
		
	if( document.getElementById("height").value != '' && document.getElementById("height").value % 1 != 0 ) {
		alert('Please enter a valid height.');
		return false;
	}
	if( document.getElementById("height").value != '' )
		shortcode += ' height=' + document.getElementById("height").value;
	
	if( document.getElementById("splash").value != '' )
		shortcode += ' splash=\'' + document.getElementById("splash").value + '\'';
	
	if( document.getElementById("popup").value != '' ) {
			var popup = document.getElementById("popup").value;
			popup = popup.replace(/&/g,'&amp;');
			popup = popup.replace(/'/g,'\\\'');
			popup = popup.replace(/"/g,'&quot;');
			popup = popup.replace(/</g,'&lt;');
			popup = popup.replace(/>/g,'&gt;');
			shortcode += ' popup=\'' + popup +'\'';
	}
	
	shortcode += ']';
	
	if( content_original.match( re ) )
		FCK.EditorDocument.body.innerHTML = content_original.replace( re, shortcode);
	else
		FCK.EditorDocument.body.innerHTML = content_original.replace( re2, shortcode);
	//FCKFVWPFlowplayer.Insert('a');
	/*FCK = oEditor.FCK;
	FCK.InsertElement( 'asdf' );*/
	return true;
}

</script>

</html>