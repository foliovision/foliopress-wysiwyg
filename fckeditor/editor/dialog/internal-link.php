<?php

//	WP < 2.7 fix
if( file_exists( dirname(__FILE__) . '/../../../../../../wp-load.php' ) )
	@require_once( realpath( dirname(__FILE__) . '/../../../../../../wp-load.php' ) );
else
	@require_once( realpath( dirname(__FILE__) . '/../../../../../../wp-config.php' ) );

global $current_user;

if(!$current_user->id)
    die('Access denied.');


require_once(ABSPATH.'wp-admin/includes/internal-linking.php');
require_once(ABSPATH.'wp-admin/includes/template.php');
$url =  get_bloginfo('wpurl');
$includes_dir = $url.'/'.WPINC;
$admin_dir = $url.'/wp-admin' ;
   wp_enqueue_script( 'jquery' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<link rel='stylesheet' href='<?php echo $admin_dir; ?>/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin&amp' type='text/css' media='all' />
<link rel='stylesheet' id='thickbox-css'  href='<?php echo $includes_dir; ?>/js/thickbox/thickbox.css' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='<?php echo $admin_dir; ?>/css/colors-fresh.css' type='text/css' media='all' />
<link media="all" type="text/css" href="<?php echo $includes_dir; ?>/js/tinymce/plugins/wplink/css/wplink.css?" id="wplink-css" rel="stylesheet">
<link media="all" type="text/css" href="<?php echo $includes_dir; ?>/css/jquery-ui-dialog.css" id="wp-jquery-ui-dialog-css" rel="stylesheet">
<title>Foliopress WYSIWYG WP Link Dialog</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="noindex, nofollow" name="robots">

<?php wp_head(); ?>
<style type="text/css">
/*#message, #inputfield, #checkbox { display: none; }*/
abbr { border-bottom: 1px dotted rgb(102, 102, 102); cursor: help; }
html, * html body { margin-top: 0 !important; }
#wp-link label span {  display: inline-block;  padding-right: 5px;  text-align: right;  width: 50px;}
#wp-link label input[type="text"] {  margin-top: 5px;  width: 300px;}
html, .wp-dialog {  background-color: transparent;}
</style>
<script type="text/javascript">
var wp_ajax = '<?php echo $admin_dir; ?>/admin-ajax.php';
var nonce = '<?php echo  wp_create_nonce('internal-linking'); ?>'

jQuery(document).ready(function() {

  jQuery("#wp-link-cancel").hide();
  jQuery("#wp-link-update").hide();

	jQuery("#internal-toggle").click(function() {

	jQuery("#search-panel").slideToggle();
	});
	jQuery('li.alternate').click(function() {
	
	});
	
	jQuery("#search-field").keyup(function() {
	val = jQuery(this).val();
	leng = val.length;
	if(leng >=3) {
	   jQuery.ajax({
   type: "POST",
   url: wp_ajax,
   data: "search="+val+"&action=wp-link-ajax&page=1&_ajax_linking_nonce="+nonce,
   success: function(msg){
   jQuery("li.alternate").remove();
   jQuery(".query-notice").hide();
   jQuery(".river-waiting").hide();
   jQuery(".waiting").hide();
   	data = jQuery.parseJSON(msg);
    
     jQuery.each(data, function(key, value) { 
  		jQuery("#most-recent-results ul").append('<li class="alternate"><input type="hidden" value="'+value.permalink+'" class="item-permalink"><span class="item-title"><input id="item-title" value="'+value.title+'" type="hidden">'+value.title+'</span><span class="item-info">'+value.info+'</span></li>');
		});
		jQuery('li.alternate').click(function() {
		title = jQuery(this).find('#item-title').val();
		jQuery("#link-title-field").val(title);
		val = jQuery(this).find('.item-permalink').val()
		jQuery("#url-field").val(val);
	});	
   }
 });	
	} else {
	jQuery.ajax({
   type: "POST",
   url: wp_ajax,
   data: "action=wp-link-ajax&page=1&_ajax_linking_nonce="+nonce,
   success: function(msg){
   jQuery(".query-notice").show();
   jQuery("li.alternate").remove();
   jQuery(".river-waiting").hide();
   jQuery(".waiting").hide();
   	data = jQuery.parseJSON(msg);
    
     jQuery.each(data, function(key, value) { 
  		jQuery("#most-recent-results ul").append('<li class="alternate"><input type="hidden" value="'+value.permalink+'" class="item-permalink"><span class="item-title"><input id="item-title" value="'+value.title+'" type="hidden">'+value.title+'</span><span class="item-info">'+value.info+'</span></li>');
		});
		jQuery('li.alternate').click(function() {
		title = jQuery(this).find('#item-title').val();
		jQuery("#link-title-field").val(title);
		val = jQuery(this).find('.item-permalink').val()
		jQuery("#url-field").val(val);
	});	
   }
 });
	}
	
	});
   jQuery.ajax({
   type: "POST",
   url: wp_ajax,
   data: "action=wp-link-ajax&page=1&_ajax_linking_nonce="+nonce,
   success: function(msg){
   jQuery(".river-waiting").hide();
   jQuery(".waiting").hide();
   	data = jQuery.parseJSON(msg);
    
     jQuery.each(data, function(key, value) { 
  		jQuery("#most-recent-results ul").append('<li class="alternate"><input type="hidden" value="'+value.permalink+'" class="item-permalink"><span class="item-title"><input id="item-title" value="'+value.title+'" type="hidden">'+value.title+'</span><span class="item-info">'+value.info+'</span></li>');
		});
		jQuery('li.alternate').click(function() {
		title = jQuery(this).find('#item-title').val();
		jQuery("#link-title-field").val(title);
		val = jQuery(this).find('.item-permalink').val()
		jQuery("#url-field").val(val);
		
	});	
   }
 });
});


</script>

</head>
<body scroll="no" style="OVERFLOW: hidden">

</body>


<?php wp_link_dialog(); ?>
</html>