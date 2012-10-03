<?php
/*
Plugin Name: FolioPress WYSIWYG
Plugin URI: http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg
Description: WYSIWYG FCKEditor with custom Image Management and nice skin.
Version: 0.10
Author: Foliovision s r.o.
Author URI: http://www.foliovision.com
*/

register_activation_hook(__FILE__,'fp_wysiwyg_activate');

function fp_wysiwyg_activate() {
    if (is_plugin_active('ckeditor-for-wordpress/ckeditor_wordpress.php')) {
        //plugin is activated
        wp_die ('<h2>Warning, conflicting plugin is active!<br /><br />Please, deactivate CKEditor for WordPress</h2>');
    }
    if (is_plugin_active('fckeditor-for-wordpress-plugin/deans_fckeditor.php')) {
        //plugin is activated
        wp_die ('<h2>Warning, conflicting plugin is active!<br /><br />Please, deactivate Dean\'s FCKEditor For Wordpress</h2>');
    }

    if (get_option('default_post_edit_rows') < 20)
        update_option('default_post_edit_rows', 20);
}

define( 'FV_FCK_NAME', 'Foliopress WYSIWYG' );
define( 'FV_FCK_OPTIONS', 'fp_wysiwyg' );

require_once( 'foliopress-wysiwyg-class.php' );

if( $GLOBALS['wp_version'] > 2.9 ) {
  add_action( 'init', array( &$fp_wysiwyg, 'check_featured_image_capability' ), 999 );
}

if( $GLOBALS['wp_version'] < 2.5 ) {
  add_action( 'admin_head', array( &$fp_wysiwyg, 'admin_init' ) );
}
else {
  add_action( 'admin_init', array( &$fp_wysiwyg, 'admin_init' ) );
}

//add_action( 'admin_head', array( &$fp_wysiwyg, 'FckLoadAdminHead' ) );
add_action( 'admin_head', array( &$fp_wysiwyg, 'CkLoadAdminHead' ) );
add_action( 'admin_menu', array( &$fp_wysiwyg, 'AddOptionPage' ) );
add_action( 'admin_notices', array( &$fp_wysiwyg, 'AdminNotices') );

/* comment out by MM 25/1/2012 LoadCKEditor
add_action( 'edit_form_advanced', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'edit_page_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
add_action( 'simple_edit_form', array( &$fp_wysiwyg, 'LoadFCKEditor' ) );
*/


add_action( 'edit_form_advanced', array( &$fp_wysiwyg, 'LoadCKEditor' ) );
add_action( 'edit_page_form', array( &$fp_wysiwyg, 'LoadCKEditor' ) );
add_action( 'simple_edit_form', array( &$fp_wysiwyg, 'LoadCKEditor' ) );


add_filter( 'the_editor', array( &$fp_wysiwyg, 'the_editor' ) );

    /// 0.5.2 SEO Images support
    add_action( 'wp_ajax_seo_images_featured_image', array( &$fp_wysiwyg, 'featured_image' ) );
    add_filter( 'admin_post_thumbnail_html', array( &$fp_wysiwyg, 'admin_post_thumbnail_html' ) );

//  remove tinyMCE editor JS in WP 3.3
if( $GLOBALS['wp_version'] >= 3.3 ) {
  add_action( 'admin_print_footer_scripts', array( &$fp_wysiwyg, 'admin_print_footer_scripts' ) );
}

if( $GLOBALS['wp_version'] >= 3.0 ) {
  add_action( 'admin_head', array( &$fp_wysiwyg, 'KillTinyMCE' ) );
}
else {
  add_action( 'option_posts_per_page', array( &$fp_wysiwyg, 'KillTinyMCE' ) );
}

if( $GLOBALS['wp_version'] >= 3.0 ) {
  add_filter('user_can_richedit', array(&$fp_wysiwyg, 'user_can_richedit') );
}
//add_action( 'personal_options_update', array( &$fp_wysiwyg, 'PersonalOptionsUpdate' ) );
//register_activation_hook( __FILE__, array( &$fp_wysiwyg, 'PluginActivate' ) );

add_filter( 'media_buttons_context', array(&$fp_wysiwyg, 'fv_remove_mediabuttons') );
add_action('admin_print_scripts', array(&$fp_wysiwyg, 'add_admin_js'));
add_action('content_edit_pre', array(&$fp_wysiwyg, 'content_edit_pre'));

//add_filter('content_save_pre', array(&$fp_wysiwyg, 'cke_foliopress_clean'));
//add_filter('content_save_pre', array(&$fp_wysiwyg, 'remove_blank_p'));

if( $GLOBALS['wp_version'] >= 2.7 ) {
  add_action('admin_menu', array(&$fp_wysiwyg, 'meta_box_add') );
  add_action('admin_menu', array(&$fp_wysiwyg, 'remove_meta_boxes'), 99, 3 );
}
add_filter('wp_insert_post', array(&$fp_wysiwyg, 'wp_insert_post'));
add_filter('the_content', array(&$fp_wysiwyg, 'the_content'), 0);

//if(is_admin()) {
    add_action('admin_footer', 'fv_ckeditor_tabs_js');
//}


function fv_ckeditor_tabs_js() {
//    wp_enqueue_script('jquery');
    ?>
    <script type='text/javascript'>
        /* <![CDATA[ */
        jQuery(document).ready(function() {
                
            jQuery(".foliopress-wysiwyg-tabs a").click(function() {
                jQuery(".foliopress-wysiwyg-tabs .active").removeClass("active");
                jQuery(this).addClass("active");
                var section = jQuery(this).attr("rel");
                console.log(section);
                jQuery("#foliopress-wysiwyg-tables .foliopress-wysiwyg-single").hide();
                jQuery("#foliopress-wysiwyg-tables #" + section).show();
                return false;
            })
                
        })
        /* ]]> */
    </script>
    <?php
}
?>