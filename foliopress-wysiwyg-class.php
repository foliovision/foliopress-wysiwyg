<?php
/**
 * Foliopress WYSIWYG class file
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled here
 *  
 * @author Foliovision s.r.o. <info@foliovision.com>
 * @version 0.9.22
 * @package foliopress-wysiwyg
 */
/**
 * Including wordpress
 */
//	WP < 2.7 compatibility
if (file_exists(dirname(__FILE__) . '/../../../wp-load.php'))
    require_once( realpath(dirname(__FILE__) . '/../../../wp-load.php') );
else
    require_once( realpath(dirname(__FILE__) . '/../../../wp-config.php') );

/**
 * Some basic functions for this class to work
 */
require_once( 'include/foliopress-wysiwyg-load.php' );
require_once( 'include/fp-api.php' );


if (isset($_POST['recreate_submit'])) {
    //require_once( dirname(__FILE__).'/ckeditor/plugins/kfm/cleanup.php' );
}

/**
 * Main Foliopress WYSIWYG class
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled in it
 *
 * @author Foliovision s.r.o. <info@foliovision.com>
 */
class fp_wysiwyg_class extends Foliopress_Plugin {
///  --------------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Properties   --------------------------------------------------
///  --------------------------------------------------------------------------------------------------------------------

    /**
     * Correctly formated url to blog
     * @var string
     */
    var $strSiteUrl = "";

    /**
     * Correctly formated url to fckeditor installed in here
     * @var string
     */
    var $strFCKEditorPath = "";

    /**
     * Correctly formated url to ckeditor installed in here
     * @var string
     */
    var $strCKEditorPath = "";

    /**
     * Correctly formated url to this plugin
     * @var string
     */
    var $strPluginPath = "";

    /**
     * Height of FCKEditor
     * @var integer
     */
    var $iEditorSize = 240;

    /**
     * Plugin version
     * @var string
     */
    var $strVersion = '0.9.20';

    /**
     * Custom options array.
     * Array of options that are stored in database:
     * - 'images' {@link fp_wysiwyg_class::FVC_IMAGES related constant} : Relative path to images folder on server from document root
     * - 'FCKToolbar' {@link fp_wysiwyg_class::FVC_TOOLBAR related constant} : Currently used toolbar in FCKEditor
     * - 'FCKSkin' {@link fp_wysiwyg_class::FVC_SKIN related constant} : Currently used skin in FCKEditor
     * - 'FCKWidth' {@link fp_wysiwyg_class::FVC_WIDTH related constant} : Width of FCKEditor text area
     * - 'KFMLink' {@link fp_wysiwyg_class::FVC_KFM_LINK related constant} : Tells KFM to wrap sended images in link to pure original image
     * - 'KFMLightbox' {@link fp_wysiwyg_class::FVC_KFM_LIGHTBOX related constant} : Tells KFM to add 'rel="lightbox"' to link, to lunch lightbox
     * - 'KFMThumbs' {@link fp_wysiwyg_class::FVC_KFM_THUMBS related constant} : Array of thumbnail sizes for KFM
     * - 'FPCTexts' {@link fp_wysiwyg_class::FVC_FPC_TEXTS related constant} : Array of FP-Regex texts which should not be enclosed with <p> or <div> tag that FCK puts everywhere
     *
     * @var array
     */
    var $aOptions = array();

    /**
     * Stores if Rich text editing is turned on for current user
     * @var bool
     */
    var $bUseFCK = false;
    var $has_wpautop;
    var $has_wptexturize;
    var $loading;
    var $process_featured_images;

///  -------------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Constants   --------------------------------------------------
///  -------------------------------------------------------------------------------------------------------------------

    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */

    const FVC_IMAGES = 'images';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_TOOLBAR = 'FCKToolbar';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_SKIN = 'FCKSkin';
    const FVC_LANG = 'FCKLang';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_WIDTH = 'FCKWidth';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_KFM_LINK = 'KFMLink';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_KFM_LIGHTBOX = 'KFMLightbox';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_KFM_THUMBS = 'KFMThumbs';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_KFM_THUMB_SIZE = 'KFMThumbnailSize';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_FPC_TEXTS = 'FPCTexts';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_JPEG = 'JPEGQuality';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_PNG = 'PNGTransform';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_DIR = 'DIRset';
    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_PNG_LIMIT = 'PNGLimit';

    /**
     * Relative path to FCKEditor skins folder
     */
    const FVC_SKINS_RELATIVE_PATH = '/ckeditor/skins';
    /**
     * Relative path to FCKEditor languages folder
     */
    const FVC_LANG_RELATIVE_PATH = '/ckeditor/lang';
    /**
     * Relative path to KFM skins folder
     */
    const KFM_LANG_RELATIVE_PATH = '/ckeditor/plugins/kfm/lang';
    /**
     * Relative path to custom FCKEditor config file
     */
    const FVC_FCK_CONFIG_RELATIVE_PATH = 'custom-config/foliopress-wysiwyg-config-js.php';
    /**
     * Relative path to options page js file
     */
    const FVC_OPTIONS_JS_PATH = 'foliopress-wysiwyg.js';
    /**
     * Relative path to Foliovision Regex class file
     */
    const FVC_FV_REGEX_PATH = 'include/foliovision-regex.js';
    const FVC_HIDEMEDIA = 'HideMediaButtons';
    const FVC_MAXW = 'MaxWidth';
    const FVC_MAXH = 'MaxHeight';

    /**
     * Key for {@link fp_wysiwyg_class::$aOptions Options array} 
     */
    const FVC_USE_FLASH_UPLOADER = 'UseFlashUploader';

    ///	Addition 2010/03/16	zUhrikova	Foliovision
    const FVC_IMAGES_CHANGED = 'image_path_changed';
    ///	End of addition
    const FV_SEO_IMAGES_POSTMETA = 'postmeta';
    const FV_SEO_IMAGES_IMAGE_TEMPLATE = 'image_template';
    /// Addition 2012/02/15
    const forcePasteAsPlainText = 'forcePasteAsPlainText';
    const CKE_autogrow = 'CKE_autogrow';
    const CKE_autoGrow_minHeight = 'CKE_autoGrow_minHeight';
    const CKE_autoGrow_maxHeight = 'CKE_autoGrow_maxHeight';

///  -----------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Methods   --------------------------------------------------
///  -----------------------------------------------------------------------------------------------------------------

    /**
     * Class constructor. Sets all basic variables ({@link fp_wysiwyg_class::$strSiteUrl $strSiteUrl}, {@link fp_wysiwyg_class::$strPluginPath $strPluginPath},
     * {@link fp_wysiwyg_class::$strFCKEditorPath $strFCKEditorPath}, {@link fp_wysiwyg_class::$iEditorSize $iEditorSize},
     * {@link fp_wysiwyg_class::$aOptions $aOptions}) to proper values
     */
    function __construct() {

        $this->readme_URL = 'http://plugins.trac.wordpress.org/browser/foliopress-wysiwyg/trunk/readme.txt?format=txt';
        add_action('in_plugin_update_message-foliopress-wysiwyg/foliopress-wysiwyg.php', array(&$this, 'plugin_update_message'));

        /*
          ///   Modification   2009/06/24
          if(function_exists('site_url'))
          $strSite = trailingslashit( site_url() );
          else
          $strSite = trailingslashit( get_option('siteurl') );
          //$strSite = trailingslashit( get_option( 'siteurl' ) );
          /// End of modification
          $this->strSiteUrl = $strSite; //echo '<!-- purl'.plugins_url().' -->';
          if( function_exists( 'plugins_url' ) ) {
          $this->strPluginPath = plugins_url();
          }
          else {
          $this->strPluginPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/';
          }
          $this->strFCKEditorPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/fckeditor/';
         */
        $this->iEditorSize = 20 * intval(get_option('default_post_edit_rows'));
        if ($this->iEditorSize < 240)
            $this->iEditorSize = 240;

        $this->aOptions = get_option(FV_FCK_OPTIONS);
        if (!isset($this->aOptions[self::FVC_IMAGES]))
            $this->aOptions[self::FVC_IMAGES] = '/images/';
        if (!isset($this->aOptions[self::FVC_TOOLBAR]))
            $this->aOptions[self::FVC_TOOLBAR] = 'Foliovision';
        if (!isset($this->aOptions[self::FVC_SKIN]))
            $this->aOptions[self::FVC_SKIN] = 'foliovision';
        if (!isset($this->aOptions[self::FVC_WIDTH]))
            $this->aOptions[self::FVC_WIDTH] = 0;
        if (!isset($this->aOptions[self::FVC_KFM_LINK]))
            $this->aOptions[self::FVC_KFM_LINK] = true;
        if (!isset($this->aOptions[self::FVC_KFM_LIGHTBOX]))
            $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;
        if (!isset($this->aOptions[self::FVC_KFM_THUMBS]))
            $this->aOptions[self::FVC_KFM_THUMBS] = array(400, 200, 150);
        if (!isset($this->aOptions[self::FVC_FPC_TEXTS]))
            $this->aOptions[self::FVC_FPC_TEXTS] = array("*** (\\\\w\\\\*) ***", "\\\\[sniplet (\\\\w\\\\*)\\\\]");
        if (!isset($this->aOptions[self::FVC_JPEG]))
            $this->aOptions[self::FVC_JPEG] = 90;
        if (!isset($this->aOptions[self::FVC_PNG]))
            $this->aOptions[self::FVC_PNG] = true;
        if (!isset($this->aOptions[self::FVC_PNG_LIMIT]))
            $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
        if (!isset($this->aOptions[self::FVC_DIR]))
            $this->aOptions[self::FVC_DIR] = true;
        if (!isset($this->aOptions[self::FVC_KFM_THUMB_SIZE]))
            $this->aOptions[self::FVC_KFM_THUMB_SIZE] = 128;
        /// Addition 2009/06/02  mVicenik Foliovision
        if (!isset($this->aOptions[self::FVC_HIDEMEDIA]))
            $this->aOptions[self::FVC_HIDEMEDIA] = true;
        /// End of addition
        /// Addition 2009/10/29   Foliovision
        if (!isset($this->aOptions['cke_customtoolbar']))
            $this->aOptions['cke_customtoolbar'] =
                    "Cut,Copy,Paste,-,Undo,Redo,-,Bold,Italic,-,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,-,NumberedList,BulletedList,-,Outdent,Indent,-,Link,Unlink,Anchor,-,Kfmbridge,FVWPFlowplayer,Fvpasteembed,\nStyles,RemoveFormat,-,Replace,Table,HorizontalRule,SpecialChar,-,Fvmore,Fvnextpage,-,Source,-,Maximize,Scayt";

        //  todo - add content
        if (!isset($this->aOptions['customdropdown']))
            $this->aOptions['customdropdown'] = '<h5 class="">Centered image</h5>
<h5 class="left">Left aligned image</h5>
<h5 class="right">Right aligned image</h5>
<p>Normal paragraph</p>
<h1>Header 1</h1>
<h2>Header 2</h2>
<h3>Header 3</h3>
<h4>Header 4</h4>
<pre>Formatted</pre>
<code>code</code>
<span style="background-color: green;">Highlight green</span>
<span style="background-color: red; font-style:italic;">Highlight italic red</span>';
        $this->parse_dropdown_menu();

        if (!isset($this->aOptions['multipleimageposting']))
            $this->aOptions['multipleimageposting'] = true;

        if (!isset($this->aOptions['wysiwygstyles']))
            $this->aOptions['wysiwygstyles'] = "body { width: 600px; margin-left: 10px; }";

        if (!isset($this->aOptions['autowpautop']))
            $this->aOptions['autowpautop'] = true;
        if (!isset($this->aOptions['convertcaptions']))
            $this->aOptions['convertcaptions'] = true;

        if (!isset($this->aOptions[self::FVC_MAXW]))
            $this->aOptions[self::FVC_MAXW] = 960;
        if (!isset($this->aOptions[self::FVC_MAXH]))
            $this->aOptions[self::FVC_MAXH] = 960;
        if (!isset($this->aOptions[self::FVC_USE_FLASH_UPLOADER]))
            $this->aOptions[self::FVC_USE_FLASH_UPLOADER] = true;
        /// End of addition	

        if (!isset($this->aOptions['ProcessHTMLEntities']))
            $this->aOptions['ProcessHTMLEntities'] = false;
        if (!isset($this->aOptions['UseWPLinkDialog']))
            $this->aOptions['UseWPLinkDialog'] = false;

        /// Addition 2010/06/30
        if (!isset($this->aOptions['FCKLang']))
            $this->aOptions['FCKLang'] = 'auto';
        if (!isset($this->aOptions['FCKLangDir']))
            $this->aOptions['FCKLangDir'] = 'ltr';
        if (!isset($this->aOptions['kfmlang']))
            $this->aOptions['kfmlang'] = 'en';
        if (!isset($this->aOptions['dirperm']))
            $this->aOptions['dirperm'] = '777';
        if (!isset($this->aOptions['fileperm']))
            $this->aOptions['fileperm'] = '666';
        /// End of addition
        if (!isset($this->aOptions['filter_wp_thumbnails']))
            $this->aOptions['filter_wp_thumbnails'] = true;
        if (!isset($this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE]) || $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] == '')
            $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = addslashes('"<h5>"+sHtmlCode+"<br />"+sAlt+"</h5>"');
        update_option(FV_FCK_OPTIONS, $this->aOptions);

        /// Addition 2012/02/15
        if (!isset($this->aOptions['forcePasteAsPlainText']))
            $this->aOptions['forcePasteAsPlainText'] = false;


        if (!isset($this->aOptions['CKE_autogrow']))
            $this->aOptions['CKE_autogrow'] = false;
        if (!isset($this->aOptions[self::CKE_autoGrow_minHeight]))
            $this->aOptions[self::CKE_autoGrow_minHeight] = 0;
        if (!isset($this->aOptions[self::CKE_autoGrow_maxHeight]))
            $this->aOptions[self::CKE_autoGrow_maxHeight] = 0;


        //$this->KillTinyMCE( null );
    }

    /**
     * Register script for compatibility with WP Media Uploader. Thanks to Dean's FCK plugin!
     */
    function add_admin_js() {
        wp_deregister_script(array('media-upload'));
        wp_enqueue_script('media-upload', $this->strPluginPath . 'media-upload.js', array('thickbox'), '20080710');
        //wp_enqueue_script('fckeditor', $this->fckeditor_path . 'fckeditor.js');
    }

    /**
     * Adds Options page to Wordpress.
     */
    function AddOptionPage() {
        add_options_page(FV_FCK_NAME, FV_FCK_NAME, 'activate_plugins', 'fv_wysiwyg', array(&$this, 'OptionsMenuPage'));
    }

    /**
     * Checks for GD.
     */
    function AdminNotices() {
        if (!function_exists('gd_info') && !$this->checkImageMagick()) {
            echo '<div class="error fade">PHP GD Library or ImageMagick not installed! Foliopress WYSIWYG will not be able to handle your images!</div>';
        }
    }

    /**
     * Init certain variables
     */
    function admin_init() {
        if ($this->is_min_wp('2.6')) {
            $this->strPluginPath = trailingslashit(WP_PLUGIN_URL) . basename(dirname(__FILE__)) . '/';
            $this->strFCKEditorPath = trailingslashit(WP_PLUGIN_URL) . basename(dirname(__FILE__)) . '/fckeditor/';
            $this->strCKEditorPath = trailingslashit(WP_PLUGIN_URL) . basename(dirname(__FILE__)) . '/ckeditor/';
        } else {
            if (function_exists('site_url'))
                $strSite = trailingslashit(site_url());
            else
                $strSite = trailingslashit(get_option('siteurl'));
            $this->strSiteUrl = $strSite; //echo '<!-- purl'.plugins_url().' -->';
            if (function_exists('plugins_url') && 1 < 0) {
                $this->strPluginPath = trailingslashit(plugins_url());
            } else {
                $this->strPluginPath = $strSite . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/';
            }
            $this->strFCKEditorPath = $strSite . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/fckeditor/';
            $this->strCKEditorPath = $strSite . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/ckeditor/';
        }
    }

    /**
     * Links "Featured image" metabox to Foliopress WYSIWYG - SEO Image image manager
     *
     * @param string $html Original metabox HTML.
     *
     * @return string Altered metabox HTML.
     */
    function admin_post_thumbnail_html($html) {
        if (!$this->process_featured_images) {
            return $html;
        }

        if ($this->aOptions['kfmlang'] != 'auto') {
            $url = $this->strPluginPath . 'ckeditor/plugins/kfm/?lang=' . $this->aOptions['kfmlang'] . '&kfm_caller_type=fck&type=Image';
        } else {
            $url = $this->strPluginPath . 'ckeditor/plugins/kfm/?lang=en&kfm_caller_type=fck&type=Image';
        }
        $onclick = 'onclick="window.open( \'' . $url . '\', \'FCKBrowseWindow\', \'toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes,width=\'+(screen.width*0.7)+\',height=\'+(screen.height*0.7)+\',left=\'+(screen.width-screen.width*0.7)/2+\',top=\'+(screen.height-screen.height*0.7)/2); return false"';

        if (stripos($html, 'set-post-thumbnail') !== FALSE && stripos($html, '<img') === FALSE) {
            $html = preg_replace('~<a.*?set-post-thumbnail.*?</a>~', '', $html);

            $html .= '<p class="hide-if-no-js"><a title="Set Featured image with Foliopress WYSIWYG\'s Image Manager" href="#" id="seo-images-featured-image" ' . $onclick . '>Set featured image with SEO Images</a></p>';
            return $html;
        } else if (stripos($html, 'set-post-thumbnail') !== FALSE && stripos($html, '<img') !== FALSE) {
            $html = preg_replace('~(id="set-post-thumbnail"[^>]*?)class="thickbox">~', '$1>', $html);
            $html = preg_replace('~href="media-upload.*?type=image.*?TB_iframe=1"~', 'href="#" ' . $onclick, $html);
            return $html;
        }
    }

    function admin_print_footer_scripts() {
        if ($this->checkUserAgent())
            return;

        if ($this->loading) {
            remove_action('admin_print_footer_scripts', array('_WP_Editors', 'editor_js'), 50);
            remove_action('admin_footer', array('_WP_Editors', 'enqueue_scripts'), 1);
        }
    }

    //  can we set featured images?	
    function check_featured_image_capability() {
        $uploads = wp_upload_dir();
        $domain = preg_replace('~^(.*?//.*?)/.*$~', '$1', get_bloginfo('url'));
        $wp_uploads = str_replace($domain, '', $uploads['baseurl']);

        if (current_theme_supports('post-thumbnails') && rtrim($wp_uploads, '/') == rtrim($this->aOptions["images"], '/')) {
            $this->process_featured_images = true;
        }
    }

    function checkImageMagick() {
        return @is_executable('/usr/bin/convert');
    }

    function checkUserAgent() {
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== FALSE)
            return 'ipad';
        else
            return false;
    }

    /**
     * Check if captions need to be converted and if wpautop needs to be applied before editing.
     *
     * @param string $content Raw Post content.
     *
     * @return string Post content with optionally converted captions and wpautop
     */
    function content_edit_pre($content) {

        global $post;

        if ($post->post_type != 'post' && $post->post_type != 'page')
            return $content;

        $meta = get_post_meta($post->ID, '_wysiwyg', true);
        if (!$meta) {
            $meta = get_post_meta($post->ID, 'wysiwyg', true); //	check old meta
        }

        if (isset($meta['plain_text_editing']) && $meta['plain_text_editing'] == 1) {
            return $content;
        }

        if ($this->aOptions['convertcaptions']) {
            ///
            $content = preg_replace_callback('/\[caption.*?\[\/caption\]/', array(&$this, 'convert_caption'), $content);
            ///
        }

        if (isset($meta['plain_text_editing']) && $meta['post_modified'] == $post->post_modified) {
            return $content;
        }

        if (!$this->aOptions['autowpautop']) {
            return $content;
        }
        if (strlen($content) > 0) {   // try to guess if the post should use wpautop
            if (stripos($content, '<p>') === FALSE)
                return wpautop($content);
            /* if(stripos($content,'&lt;p&gt;')===FALSE && (stripos($content,'<')===FALSE || stripos($content,'>')===FALSE) )
              return wpautop($content); */
        }
        return $content;
    }

    /**
     * Optionally convert captions from shortcodes into our prefered H5 tags.
     *
     * @param string $content Raw Post content.
     *
     * @return string Post content with converted captions
     */
    function convert_caption($content) {
        $content = $content[0];
        preg_match('/caption="(.*?)"/', $content, $caption);
        $content = preg_replace('/\[caption[^\]]*?\]/', '', $content);
        $content = preg_replace('/\[\/caption\]/', '', $content);
        return '<h5>' . $content . '<br />' . $caption[1] . '</h5>';
    }

    /**
     * Searches $strOption in $strText and extracts value after it till end of file or $strEndText. Return value is trimed of all white spaces. 
     *
     * @param string $strText Text from where the data should be extracted. It is passed as reference.
     * @param string $strOption Option which should be extracted. The real value is after this text until end of file or $strEndText.
     * @param string $strEndText This string indicates the string that is used in ending of each option.
     *
     * @return string Extracted value, of false otherwise.
     */
    function ExtractOption(&$strText, $strOption, $strEndText) {
        $iStart = strpos($strText, $strOption);
        if (false === $iStart)
            return false;
        $iEnd = strpos($strText, $strEndText, $iStart);
        $iOptionLength = strlen($strOption);

        if (false === $iEnd)
            return trim(substr($strText, $iStart + $iOptionLength));
        else
            return trim(substr($strText, $iStart + $iOptionLength, $iEnd - $iStart - $iOptionLength));
    }

    /**
     * Outputs into head section of html document script for FCK to load
     */
    function FckLoadAdminHead() {
        echo "<br /><h1>" . $this->strFCKEditorPath . "fckeditor.js</h1>";
        if (strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'page.php')) :
            ?>
            <script type="text/javascript" src="<?php print( $this->strFCKEditorPath); ?>fckeditor.js"></script>
            <style type="text/css">
                #quicktags { display: none; }
            </style>
            <?php
        endif;
    }

    function CkLoadAdminHead() {
        if (strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'page.php')) :
            ?>
            <script type="text/javascript" src="<?php print( $this->strCKEditorPath); ?>ckeditor.js"></script>
            <style type="text/css">
                #quicktags { display: none; }
            </style>
            <?php
        endif;
    }

    /**
     * AJAX handler for featured image
     */
    function featured_image() {
        $post_ID = intval($_POST['post_id']);
        if (!current_user_can('edit_post', $post_ID)) {
            die('-1');
        }
        check_ajax_referer('seo-images-featured-image-' . $post_ID);

        $wp_upload_dir = wp_upload_dir();
        $domain = preg_replace('~^(.*?//.*?)/.*$~', '$1', get_bloginfo('url'));
        $wp_uploads = str_replace($domain, '', $wp_upload_dir['baseurl']);

        $file = $wp_upload_dir['basedir'] . preg_replace('~^' . $wp_uploads . '~', '', $_POST['imageURL']);

        if ($this->process_featured_images && file_exists($file)) {
            $wp_filetype = wp_check_filetype(basename($file), null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            global $wpdb; //  check if post with the same name already exists in the same date
            $time = current_time('mysql');
            $y = substr($time, 0, 4);
            $m = substr($time, 5, 2);
            $date = "$y-$m%";

            $attach_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_date LIKE %s AND post_type = 'attachment' LIMIT 1", $attachment['post_title'], $date));

            if (!$attach_id) {
                $attach_id = wp_insert_attachment($attachment, $file, $post_ID);
            }

            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $thumbnail_id = $attach_id;
            if ($thumbnail_id && get_post($thumbnail_id)) {
                delete_post_meta($post_ID, '_thumbnail_id');
                $thumbnail_html = wp_get_attachment_image($thumbnail_id, 'thumbnail');
                if (!empty($thumbnail_html)) {
                    update_post_meta($post_ID, '_thumbnail_id', $thumbnail_id);
                    die(_wp_post_thumbnail_html($thumbnail_id));
                }
            }
        } else {
            echo 'File not found in Wordpress Media directory. Is the image uploads path same as Foliopress WYSIWYG Path?';
        }
        die();
    }

    function fv_remove_mediabuttons($content) {
        global $post;
        $meta = get_post_meta($post->ID, 'wysiwyg', true);
        if (isset($meta['plain_text_editing']) && $meta['plain_text_editing'] == 1) {
            $this->bUseFCK = false;
            $aOptions = get_option(FV_FCK_OPTIONS);
            return '';
        }

        $aOptions = get_option(FV_FCK_OPTIONS);
        if (isset($aOptions['HideMediaButtons']) && $aOptions['HideMediaButtons'] == true)
            return $content . '<style>#media-buttons, #wp-content-editor-tools { display: none; }</style>';
        else
            return $content;
    }

    /**
     * Returns option if images should be wrapped in link (<a>). This function returns integer '1' and '0' depending on settings stored by user.
     * If user haven't specified this option default value is '1'.
     *
     * @return int '1' if images returned by KFM into FCKEditor should be wrapped in <a> tag with link to the original image, '0' otherwise.
     */
    function getLink() {
        $iLink = 1;
        if (isset($this->aOptions[self::FVC_KFM_LINK])) {
            if (!$this->aOptions[self::FVC_KFM_LINK])
                $iLink = 0;
        }
        return $iLink;
    }

    /**
     * Returns option if link to original image should contain 'rel="lightbox"', which triggers lightbox. Return values are integers '1' and '0' 
     * depending on settings stored by user. If user haven't specified this option default value is '1'.
     *
     * @return int '1' if link to original image should contain 'rel="lightbox"', '0' otherwise
     */
    function getLightbox() {
        $iLightbox = 1;
        if (isset($this->aOptions[self::FVC_KFM_LIGHTBOX])) {
            if (!$this->aOptions[self::FVC_KFM_LIGHTBOX])
                $iLightbox = 0;
        }
        return $iLightbox;
    }

    /**
     * Returns special KFM thumnails in string separated by this string ', '. Again this options is editable from Options page. Default value is
     * '400, 200, 150'.
     *
     * @return string All sizes for KFM special thumbnails separated by string ', '.
     */
    function getThumbsString() {
        $strThumbs = '400, 200, 150';
        if (isset($this->aOptions[self::FVC_KFM_THUMBS])) {
            $strThumbs = '';
            $aThumbs = $this->aOptions[self::FVC_KFM_THUMBS];
            $iThumbs = count($aThumbs);
            for ($i = 0; $i < $iThumbs; $i++) {
                if ($i < $iThumbs - 1)
                    $strThumbs .= $aThumbs[$i] . ', ';
                else
                    $strThumbs .= $aThumbs[$i];
            }
        }

        return $strThumbs;
    }

    /**
     * This function connects to foliovision website and checks if there is a newer version of Foliopress WYSIWYG
     *
     * @return array Array with two values, or empty array on failure:
     * - ['version'] is string containing version information
     * - ['changes'] is URL address of changelog 
     */
    function GetLatestVersion() {
        $strPathToUpdate = "/version.php?version=" . urlencode($this->strVersion) . "&blog=" . urlencode($this->strSiteUrl);
        $strUpdateHost = 'www.foliovision.com';

        $strHTTPReq = "GET $strPathToUpdate HTTP/1.0\r\n";
        $strHTTPReq .= "Host: $strUpdateHost\r\n\r\n";

        $iErr = 0;
        $strErr = '';
        $strResponse = '';
        $aReturn = array();

        if (false !== ( $fs = @fsockopen($strUpdateHost, 80, $iErr, $strErr, 10) ) && is_resource($fs)) {
            fwrite($fs, $strHTTPReq);
            while (!feof($fs))
                $strResponse .= fgets($fs, 1160);
            fclose($fs);

            $strText = explode("\r\n\r\n", $strResponse, 2);
            $strText = $strText[1];

            $objValue = $this->ExtractOption($strText, '@ver:', "\n");
            if (false !== $objValue)
                $aReturn['version'] = $objValue;
            else
                return false;
            $objValue = $this->ExtractOption($strText, '@changes:', "\n");
            if (false !== $objValue)
                $aReturn['changes'] = $objValue;
        }

        return $aReturn;
    }

    /**
     * This function disables TinyMCE and sets {@link $bUseFCK} to true or false depending on which page is loaded
     */
    function KillTinyMCE($in) {
        global $current_user;

        if ('true' == $current_user->rich_editing && strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/profile.php') === false) {
            $this->bUseFCK = true;
            $current_user->rich_editing = 'false';
        } elseif (isset($current_user) && $this->bUseFCK === null) {
            $this->bUseFCK = false;
        }

        return $in;
    }

    /**
     * Checks if post is using Imact and it's active
     */
    function has_impact($post_id) {
        $_use_impact = get_post_meta($post_id, '_use_impact', true);
        $_impact_template = get_post_meta($post_id, '_impact_template', true);
        if ($_use_impact == 'yes' && $_impact_template != '' && function_exists('impact_get_template')) {
            return true;
        } else {
            return false;
        }
    }

    function LoadCKEditor() {
        if ($this->checkUserAgent())
            return;

        $toolbar['Default'] = array(
            array('Source', '-',
                'NewPage', 'Preview', 'Templates', '-',
                'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-',
                'Undo', 'Redo', '-',
                'Find', 'Replace', '-',
                'SelectAll', 'RemoveFormat', '-',
                'Maximize', 'ShowBlocks'),
            '/',
            array('Bold', 'Italic', 'Underline', 'Strike', '-',
                'Subscript', 'Superscript', '-',
                'NumberedList', 'BulletedList', '-',
                'Outdent', 'Indent', 'Blockquote', '-',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
                'Link', 'Unlink', 'Anchor', '-',
                'Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar'
            ),
            '/',
            array('Styles', 'RemoveFormat', 'Font', 'FontSize', '-', //'Format',
                'TextColor', 'BGColor')
        );

        $toolbar['Foliovision'] = array(
            array('Cut', 'Copy', 'Paste', 'PasteFromWord', '-', 'Bold', 'Italic', '-', 'Styles', 'RemoveFormat', '-', 'NumberedList', // 'Format',
                'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote', '-', 'Link', 'Unlink', 'Anchor', '-',
                'Fvmore', '-', 'Kfmbridge', 'FVWPFlowplayer', 'Fvpasteembed', '-', 'Source', '-', 'Maximize')
        );

        $toolbar['Basic'] = array(
            array('Source', 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About')
        );


        $toolbar['Foliovision-Full'] = array(
            array('Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo', '-', 'Bold', 'Italic', '-',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
                'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-',
                'Link', 'Unlink', 'Anchor', '-', 'Kfmbridge', 'FVWPFlowplayer', 'Fvpasteembed'),
            '/',
            array('Styles', 'RemoveFormat', '-', 'Replace', 'Table', 'HorizontalRule', 'SpecialChar', '-', //'Format',
                'Fvmore', 'Fvnextpage', '-', 'Source', '-', 'Maximize', 'Scayt')
        );

        //make custom toolbar
        $tmp_cust_toolbar = explode("\n", $this->aOptions['cke_customtoolbar']);
        $count = count($tmp_cust_toolbar);
        $i = 0;
        foreach ($tmp_cust_toolbar as $row) {
            $i++;
            $toolbar['Custom'][] = explode(",", $row);
            if ($i < $count)
                $toolbar['Custom'][] = "/";
        }


        //  detect FV WP Flowplayer
        if (has_action('media_upload_fv-wp-flowplayer')) :
            ?>
            <script type="text/javascript">
                var g_fv_wp_flowplayer_found = true; 
            </script>
        <?php else : ?>
            <script type="text/javascript">
                var g_fv_wp_flowplayer_found = false; 
            </script>
        <?php
        endif;



        //// Include the CKEditor class.
        include_once dirname(__FILE__) . "/ckeditor/ckeditor.php";
        // Create a class instance.
        $CKEditor = new CKEditor();
        // Path to the CKEditor directory, ideally use an absolute path instead of a relative dir.
        //   $CKEditor->basePath = '/ckeditor/'
        // If not set, CKEditor will try to detect the correct path.
        $CKEditor->basePath = trailingslashit(WP_PLUGIN_URL) . basename(dirname(__FILE__)) . '/ckeditor/';
        // Replace a textarea element with an id (or name) of "editor1".
//        $CKEditor->config['toolbar'] = $toolbar[$this->aOptions[self::FVC_TOOLBAR]];
//        $CKEditor->config['skin'] = $this->aOptions[self::FVC_SKIN];
        //add $editor_styles to CKEditor
        global $editor_styles, $post;
        $cssPath = get_bloginfo('stylesheet_directory');

        foreach ($editor_styles as $editor_style) {
            $CKEditor_style[] = "$cssPath/$editor_style";
        }

        $CKEditor_style[] = trailingslashit(WP_PLUGIN_URL) . basename(dirname(__FILE__)) . '/custom-config/foliopress-editor.php?p=' . $post->ID;
        if ($this->aOptions[self::FVC_LANG] != 'auto') {
            $config['language'] = $this->aOptions[self::FVC_LANG];
            $CKEditor->config['defaultLanguage'] = $this->aOptions[self::FVC_LANG];
        }
        $config['contentsLangDirection'] = $this->aOptions['FCKLangDir'];
        if ($this->aOptions['ProcessHTMLEntities']) /*  affects quotes on = &quot;, off = "  */
            $config['entities'] = true;
        else
            $config['entities'] = false;

        $config['contentsCss'] = $CKEditor_style;
        $config['disableObjectResizing'] = 'true';
        $config['extraPlugins'] = 'fvmore,timestamp,kfmbridge,fvpasteembed,fvnextpage,FVWPFlowplayer';
        if ($this->aOptions[self::CKE_autogrow]) {
            $config['extraPlugins'].= ",autogrow";
            if ($this->aOptions[self::CKE_autoGrow_minHeight] > 0)
                $config['autoGrow_minHeight'] = $this->aOptions[self::CKE_autoGrow_minHeight];
            $config['height'] = $this->aOptions[self::CKE_autoGrow_minHeight];
            if ($this->aOptions[self::CKE_autoGrow_maxHeight] > 0)
                $config['autoGrow_maxHeight'] = $this->aOptions[self::CKE_autoGrow_maxHeight];
        } else {
            $config['height'] = $this->iEditorSize;
        }


        if ($this->aOptions[self::FVC_WIDTH] > 0) {
            $config['width'] = $this->aOptions[self::FVC_WIDTH];
        }

        $config['resize_dir'] = 'vertical';

        $config['removeFormatTags'] = 'b,big,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var';
        $config['toolbar'] = $toolbar[$this->aOptions[self::FVC_TOOLBAR]];
        $config['skin'] = $this->aOptions[self::FVC_SKIN];

        $CKEditor->config['forcePasteAsPlainText'] = $this->aOptions[self::forcePasteAsPlainText];
        $CKEditor->config['filebrowserBrowseUrl'] = $CKEditor->basePath . 'plugins/kfm/?lang=' . $this->aOptions[self::FVC_LANG] . '&kfm_caller_type=fck&type=Image';
        $CKEditor->config['filebrowserImageUploadUrl'] = $CKEditor->basePath . 'plugins/kfm/?lang=' . $this->aOptions[self::FVC_LANG] . '&kfm_caller_type=fck&type=Image';
//        $CKEditor->config['filebrowserBrowseUrl'] = $CKEditor->basePath . 'plugins/kfm/';
        /// Impact support
        if ($this->has_impact($post->ID)) {
            $options['bodyid'] = 'impact-content';
            $options['bodyclass'] .= ' impact-content-wrap';
            if ($post->post_type == 'page') {
                $options['bodyclass'] .= ' page';
            } else {
                $options['bodyclass'] .= ' post';
            }
        }
        $CKEditor->config['bodyId'] = $options['bodyid'];
        $CKEditor->config['bodyClass'] = $options['bodyclass'];
        if ($options['bodyid'] || $options['bodyclass']) {
            $CKEditor->config['bodyClass'] .= ' wysiwyg';
        }

//        if($this->bUseFCK) echo "yes"; else echo "no";

        $CKEditor->replace("content", $config);
        ?>
        <script type="text/javascript">                                        
            CKEDITOR.stylesSet.add( 'default',
            [
        <? echo ($this->aOptions['customdropdown-corestyles']); ?>
                                                        
            ]);
                            
                            
            function removeEmptyPara() {
                var para = CKEDITOR.instances.content.document.getElementsByTag('p');
                if(para.count()>0) {
                    for ( var i = 0, len = para.count() ; i < len ; i++ )
                    {
                        var pa = para.getItem( i );
                        if ( pa.hasClass( 'cke_remove' ) )
                        {
                            pa.remove();
                        }
                    }
                }
            }
                    
            window.onunload = function() {
                if (typeof(kfm_window)!='undefined')
                {
                    if(false == kfm_window.closed)
                    {
                        kfm_window.close ();
                    }
                }
                return undefined;
            }

                            
                            
        <?php if ($GLOBALS ['wp_version'] >= 2.7) : ?>
                jQuery(document).ready(function() {
                    window.setTimeout("fv_wysiwyg_startup();", 1000);
                });
                                                                                                                                                                
                function fv_wysiwyg_startup() {
                    if( typeof(CKEDITOR.instances.content) != 'undefined' ) {
                        CKEDITOR.instances.content.getSnapshot(); //  don't remove
                        if( typeof( CKEDITOR.instances.content.document ) != 'undefined' ) { //  IE might not be ready to reset the dirty flag yet
                            CKEDITOR.instances.content.resetDirty();
                        } else {
                            window.setTimeout("fv_wysiwyg_startup();", 1000);
                        }
                        window.setTimeout("fv_wysiwyg_update_content();", 5000);
                    } else {
                        setTimeout("fv_wysiwyg_startup();", 1000);
                    }
                }
                                                                                                                                                                
                function fv_wysiwyg_update_content() {
                    if( typeof(CKEDITOR.instances.content) != 'undefined' ) {
                        if( CKEDITOR.instances.content.checkDirty() ) {
                            jQuery('#content').val( CKEDITOR.instances.content.getSnapshot() );
                        }
                        //if(CKEDITOR.env.webkit) { setTimeout("removeEmptyPara();", 1000);}
                        wpWordCount.wc( CKEDITOR.instances.content.getSnapshot());
                        setTimeout("fv_wysiwyg_update_content();", 5000);
                                   
                    }

                }
                                        
                                        
                                        
                var SEOImagesPostId = '<?php echo $post->ID; ?>';
                var SEOImagesAjaxUrl = '<?php echo admin_url('admin-ajax.php') ?>';
                var SEOImagesAjaxNonce ='<?php echo wp_create_nonce("seo-images-featured-image-" . $post->ID); ?>';
                function FCKSetFeaturedImage( ImageURL ) {
                    jQuery.ajax({

                        url: SEOImagesAjaxUrl,

                        cache: false,

                        data: ({ action: 'seo_images_featured_image', _ajax_nonce: SEOImagesAjaxNonce, imageURL: ImageURL, thumbnail_id: ImageURL, post_id: SEOImagesPostId }), //  we set image URL to thumbnail_id for SEO Images support

                        type: 'POST',

                        success: function(data) {

                            jQuery( '#postimagediv .inside' ).html( data );

                        }

                    });
                }
                               
                               
        <?php endif; ?>
                            
        </script>
        <style>
            .cke_styles_panel {

            }
            .cke_panel_frame {

            }
        </style>
        <?
        //  remove tinyMCE editor JS in WP 3.3
        $this->loading = true;
    }

    /**
     * Checks if some specified file with relative path is allowed to be editable by user. Note that this not checks if the file is also
     * available for editing by file system, for that see php native function 'is_writable'.
     *
     * @param string $strFile <b>RELATIVE</b> path to file
     *
     * @return bool True if the file is editable by user, false otherwise
     */
    function IsEditableFile($strFile) {
        if (false !== strpos(str_replace("\\", '/', $strFile), self::FVC_FCK_CONFIG_RELATIVE_PATH))
            return true;

        return false;
    }

    /**
     * Custom "Post Author" editing meta box with "Plain text editing" checkbox.
     */
    function meta_box() {
        global $current_user, $user_ID, $post;
        ?>
        <label class="screen-reader-text" for="post_author_override"><?php _e('Author'); ?></label>
        <?php
        if (function_exists('get_users')) {
            wp_dropdown_users(array(
                'who' => 'authors',
                'name' => 'post_author_override',
                'selected' => empty($post->ID) ? $user_ID : $post->post_author,
                'include_selected' => true
            ));
        } else {
            $authors = get_editable_user_ids($current_user->id, true, $post->post_type); // TODO: ROLE SYSTEM
            if ($post->post_author && !in_array($post->post_author, $authors)) {
                $authors[] = $post->post_author;
            }
            wp_dropdown_users(array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post->ID) ? $user_ID : $post->post_author));
            ?>
            <?php
        }

        $meta = get_post_meta($post->ID, '_wysiwyg', true);
        if (!$meta) {
            $meta = get_post_meta($post->ID, 'wysiwyg', true); //	check old meta
        }
        if (!isset($meta['plain_text_editing'])) {
            $meta['plain_text_editing'] = false;
        }
        ?><label for="plain_text_editing"><input name="plain_text_editing" type="checkbox" id="plain_text_editing" value="true" <?php checked(1, $meta['plain_text_editing']); ?> /> <?php _e('Plain text editing'); ?> <abbr title="This will disable WYSIWYG editor for this post, as well as all the WP formating routines (wptexturize and wpautop). Turn this option off only if you are sure this post won't get destroyed by it.">(?)</abbr>
        </label>
        <?php
    }

    function meta_box_add() {
        add_meta_box('foliopress-wysiwyg', 'Post Author', array(&$this, 'meta_box'), 'post', 'side', 'high');
        add_meta_box('foliopress-wysiwyg', 'Post Author', array(&$this, 'meta_box'), 'page', 'side', 'high');
    }

    /**
     * Loads Options page. It also stores all changes and also loads KFM thumbnails recreation.
     */
    function OptionsMenuPage() {
        $bError = false;
        $strCustomError = '';
        $strErrDesc = '';

        $strLatestVersion = '';
        $strLinkToChangesLog = '';
        //$aResult = $this->GetLatestVersion();
        if (isset($aResult) && false !== $aResult) {
            if (isset($aResult['version']))
                $strLatestVersion = $aResult['version'];
            if (isset($aResult['changes']))
                $strLinkToChangesLog = $aResult['changes'];
        }
        $strPath = dirname(__FILE__);

        $iOldErrorSettings = error_reporting(0);

        try {
            /// Saving of file changes to editable file
            if (isset($_POST['save_file']) && isset($_GET['edit'])) {

                $strFile = realpath($strPath . '/' . urldecode($_GET['edit']));
                if (is_writable($strFile) && $this->IsEditableFile($strFile)) {

                    $strText = $_POST['textFile'];
                    if (ini_get('magic_quotes_gpc'))
                        $strText = stripslashes($strText);

                    if (false === file_put_contents($strFile, stripslashes($_POST['textFile'])))
                        $strMessage = "Error while saving file '" . basename($strFile) . "' !";
                    else
                        $strMessage = "File '" . basename($strFile) . "' saved successfully.";
                }
            }

            /// When user returns from file editing page we need to remove $_GET['edit'] from it
            if ((isset($_POST['save_file']) || isset($_POST['cancel_file'])) && isset($_GET['edit'])) {
                $_SERVER['REQUEST_URI'] = str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) . 'page=' . $_GET['page'];
                unset($_GET['edit']);
            }

            /// When user returns from recreate page, thumbnails will be recreated
            if (isset($_POST['recreate_submit'])) {
                set_time_limit(300);
                require_once( dirname(__FILE__) . '/ckeditor/plugins/kfm/cleanup.php' );

                $aSetup = array();
                $aSetup['JPGQuality'] = $this->aOptions[self::FVC_JPEG];
                $aSetup['transform'] = $this->aOptions[self::FVC_PNG];
                $aSetup['transform_limit'] = $this->aOptions[self::FVC_PNG_LIMIT];
                $aSetup['ddir'] = $this->aOptions[self::FVC_DIR];


                KFM_RecreateThumbnailsSilent(realpath($_SERVER['DOCUMENT_ROOT'] . $this->aOptions[self::FVC_IMAGES]), $this->aOptions[self::FVC_KFM_THUMBS], $kfm_workdirectory, $aSetup);
                echo '<div class="updated"><p>Thumbnails recreated.</p></div>';
            }

            /// This is regular saving of options that are on the main Options page
            if (isset($_POST['options_save'])) {

                $this->aOptions[self::FVC_IMAGES_CHANGED] = false;
                if ($this->aOptions[self::FVC_IMAGES] != $_POST['ImagesPath']) {
                    $this->aOptions[self::FVC_IMAGES_CHANGED] = true;
                }


                $this->aOptions[self::FVC_SKIN] = $_POST['FCKSkins'];
                $this->aOptions[self::FVC_TOOLBAR] = $_POST['FCKToolbar'];

                if (strrpos($_POST['ImagesPath'], '/') != (strlen($_POST['ImagesPath']) - 1) && $_POST['ImagesPath'] != '/')
                    $_POST['ImagesPath'] = $_POST['ImagesPath'] . '/';
                if ($_POST['ImagesPath'] == '')
                    $this->aOptions[self::FVC_IMAGES] = '/';
                else
                    $this->aOptions[self::FVC_IMAGES] = $_POST['ImagesPath'];

                $this->aOptions[self::FVC_WIDTH] = $_POST['FCKWidth'];

                $this->aOptions[self::FVC_KFM_LINK] = false;
                if (isset($_POST['KFMLink']) && 'yes' == $_POST['KFMLink'])
                    $this->aOptions[self::FVC_KFM_LINK] = true;
                $this->aOptions[self::FVC_KFM_LIGHTBOX] = false;
                if (isset($_POST['KFMLightbox']) && 'yes' == $_POST['KFMLightbox'])
                    $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;

                $this->aOptions[self::FVC_HIDEMEDIA] = true;
                if (isset($_POST['HideMediaButtons']))
                    $this->aOptions[self::FVC_HIDEMEDIA] = false;

                /// Addition 2012/02/15
                $this->aOptions['forcePasteAsPlainText'] = false;
                if (isset($_POST['forcePasteAsPlainText']))
                    $this->aOptions['forcePasteAsPlainText'] = true;


                $this->aOptions['CKE_autogrow'] = false;
                if (isset($_POST['CKE_autogrow']))
                    $this->aOptions['CKE_autogrow'] = true;

                $this->aOptions[self::CKE_autoGrow_minHeight] = $_POST['CKE_autoGrow_minHeight'];
                $this->aOptions[self::CKE_autoGrow_maxHeight] = $_POST['CKE_autoGrow_maxHeight'];



                ///end addition

                $this->aOptions['multipleimageposting'] = false;
                if (isset($_POST['MultipleImagePosting']))
                    $this->aOptions['multipleimageposting'] = true;

                $this->aOptions['autowpautop'] = false;
                if (isset($_POST['PreWPAutop']))
                    $this->aOptions['autowpautop'] = true;

                $this->aOptions['convertcaptions'] = false;
                if (isset($_POST['convertcaptions']))
                    $this->aOptions['convertcaptions'] = true;

                if (isset($_POST['bodyid']))
                    $this->aOptions['bodyid'] = $_POST['bodyid'];
                if (isset($_POST['bodyclass']))
                    $this->aOptions['bodyclass'] = $_POST['bodyclass'];
                if (isset($_POST['cke_customtoolbar']))
                    $this->aOptions['cke_customtoolbar'] = stripslashes($_POST['cke_customtoolbar']);
                if (isset($_POST['customdropdown']))
                    $this->aOptions['customdropdown'] = stripslashes($_POST['customdropdown']);

                $this->parse_dropdown_menu();

                if (isset($_POST['wysiwygstyles']))
                    $this->aOptions['wysiwygstyles'] = stripslashes($_POST['wysiwygstyles']);

                $this->aOptions['ProcessHTMLEntities'] = false;
                if (isset($_POST['ProcessHTMLEntities']))
                    $this->aOptions['ProcessHTMLEntities'] = true;

                if (isset($_POST['FCKLang']))
                    $this->aOptions['FCKLang'] = $_POST['FCKLang'];
                if (isset($_POST['FCKLangDir']))
                    $this->aOptions['FCKLangDir'] = $_POST['FCKLangDir'];
                if (isset($_POST['kfmlang']))
                    $this->aOptions['kfmlang'] = $_POST['kfmlang'];
                if (isset($_POST['fileperm']))
                    $this->aOptions['fileperm'] = $_POST['fileperm'];
                if (isset($_POST['dirperm']))
                    $this->aOptions['dirperm'] = $_POST['dirperm'];

                if (isset($_POST['KFMThumbCount'])) {
                    $aThumbs = array();
                    for ($i = 0; $i < $_POST['KFMThumbCount']; $i++)
                        if (isset($_POST['KFMThumb' . $i]))
                            $aThumbs[] = $_POST['KFMThumb' . $i];
                    $this->aOptions[self::FVC_KFM_THUMBS] = $aThumbs;
                }

                if (isset($_POST['FPCleanCount'])) {
                    $aFPTexts = array();
                    for ($i = 0; $i < $_POST['FPCleanCount']; $i++)
                        if (isset($_POST['FPClean' . $i]))
                            $aFPTexts[] = $_POST['FPClean' . $i];
                    $this->aOptions[self::FVC_FPC_TEXTS] = $aFPTexts;
                }

                $this->aOptions[self::FVC_JPEG] = intval($_POST['JPEGQuality']);
                if ($this->aOptions[self::FVC_JPEG] < 0 || $this->aOptions[self::FVC_JPEG] > 100)
                    $this->aOptions[self::FVC_JPEG] = 80;
                $this->aOptions[self::FVC_PNG] = isset($_POST['PNGTransform']) ? true : false;
                $this->aOptions[self::FVC_PNG_LIMIT] = intval($_POST['PNGLimit']);
                if ($this->aOptions[self::FVC_PNG_LIMIT] < 0 || $this->aOptions[self::FVC_PNG_LIMIT] > 50000)
                    $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
                $this->aOptions[self::FVC_DIR] = isset($_POST['DIRset']) ? true : false;

                if (isset($_POST['MaxWidth']))
                    $this->aOptions[self::FVC_MAXW] = intval($_POST['MaxWidth']);
                if (isset($_POST['MaxHeight']))
                    $this->aOptions[self::FVC_MAXH] = intval($_POST['MaxHeight']);
                if (intval($_POST['KFMThumbnailSize']) < 64) {
                    $_POST['KFMThumbnailSize'] = 64;
                } else if (intval($_POST['KFMThumbnailSize']) > 256) {
                    $_POST['KFMThumbnailSize'] = 256;
                }
                if (isset($_POST['KFMThumbnailSize']))
                    $this->aOptions[self::FVC_KFM_THUMB_SIZE] = intval($_POST['KFMThumbnailSize']);

                $this->aOptions[self::FVC_USE_FLASH_UPLOADER] = false;
                if (isset($_POST[self::FVC_USE_FLASH_UPLOADER]))
                    $this->aOptions[self::FVC_USE_FLASH_UPLOADER] = true;

                $this->aOptions[self::FV_SEO_IMAGES_POSTMETA] = $_POST['postmeta'];

                if ($_POST[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] != '') {
                    $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = $_POST[self::FV_SEO_IMAGES_IMAGE_TEMPLATE];
                } else {
                    $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = addslashes('"<h5>"+sHtmlCode+"<br />"+sAlt+"</h5>"');
                }

                $this->aOptions['UseWPLinkDialog'] = false;
                if (isset($_POST['UseWPLinkDialog']))
                    $this->aOptions['UseWPLinkDialog'] = true;

                update_option(FV_FCK_OPTIONS, $this->aOptions);
            }
        } catch (Exception $ex) {
            $bError = true;
            $strErrDesc = $ex->getMessage();

            $iPos = strpos($strErrDesc, 'file_put_contents');
            if (false !== $iPos) {
                if (strpos($strErrDesc, 'config.ini')) {
                    $strCustomError = 'SEO Images (KFM) config.ini file is read-only. In order to change options this file has to be rewritten.';
                    $strCustomError .= 'Please adjust the file permissions to this file. For further help, read the manual !';
                }
            }
        }

        error_reporting($iOldErrorSettings);

        /// Loading of pages based on request
        if (isset($_POST['recreate']))
            include( $strPath . '/view/recreate.php' );
        elseif (!isset($_GET['edit']))
            include( $strPath . '/view/options.php' );
        else {
            $strFile = realpath($strPath . '/' . urldecode($_GET['edit']));
            if (is_writable($strFile) && $this->IsEditableFile($strFile))
                include( $strPath . '/view/edit.php' );
            else {
                $strMessage = 'You cannot edit this file. The requested link is invalid !';
                include( $strPath . '/view/message.php' );
            }
        }
    }

    /**
     * Formats plugin options into FCKeditor JS configuration statements.
     */
    function parse_dropdown_menu() {
        $items = explode("\r\n", $this->aOptions['customdropdown']);             //  one item per line
        $i = 0;                                                                 //  counter

        $corestyles = '';
        $fontformats = '';


        foreach ($items AS $item) {
            $i++;
            preg_match('/<(.*?)>/i', $item, $match);                              //  take only the part inside <>
            if (!$match[0])
                continue;
            preg_match('/<([^>]*?)[\s>]/i', $match[0], $element);                    //  match the element name
            preg_match('/>([^<]*?)(<|$)/i', $item, $name);                            //  match the enclosed text or the text after the singular tag - name

            preg_match_all('/([a-z]*?)="(.*?)"/i', $match[0], $attributes);       //  match the attributes
            $attr_text = '';
            $styles_text = '';
            if (isset($attributes[1])) {
                foreach ($attributes[1] AS $key => $attribute) {
                    if (strcasecmp('style', $attribute) == 0) {                     //  style
//                        echo "<pre>  <br />  <br />  ";
//                        print_r($attributes[2][$key]);
//                        echo "</pre>";
                        $styles_text .= $attributes[2][$key];
                    } else {                                                      //  everything else
                        if (isset($attributes[2][$key]))
                            $attr_text .= '\'' . $attribute . '\' : \'' . $attributes[2][$key] . '\', ';
                        else
                            $attr_text .= '\'' . $attribute . '\', ';
                    }
                }
            }
            $styles = explode(";", $styles_text);
            if (strlen($styles_text) > 0) {
                unset($style_array);
                unset($styles_array);
                foreach ($styles as $style) {
                    $style_array = explode(":", $style);
                    $styles_array[] = "'" . trim($style_array[0]) . "':'" . trim($style_array[1]) . "'";
                }
                $styles_text = implode(",", $styles_array);
            }
//            $styles_text = preg_replace('/\b([^;]*?):/i', '\'$1\' :', $styles_text);  //  put css property into ''
//            $styles_text = preg_replace('/\b([^\s]*?);/i', '\'$1\', ', $styles_text);  //  put css values into ''
//            echo "<p>" . $styles_text . "</p>";
//            echo "<p>".$attr_text."</p>";


            $attr_text = rtrim($attr_text, ', ');
            $styles_text = rtrim($styles_text, ', ');

            if (strlen($corestyles) > 0)
                $corestyles .= ",\r\n";
            $corestyles .= "{ name : '" . $name[1] . "' , element : '" . $element[1] . "'";        //  do the proper output
//            $corestyles .= "'" . $element[1] . "_" . $i . "' : { Element : '" . $element[1] . "'";        //  do the proper output
            if (strlen($attr_text) > 0)
                $corestyles .= ", attributes : { " . $attr_text . " } ";
            if (strlen($styles_text) > 0)
                $corestyles .= ", styles : { " . $styles_text . " } ";

            $corestyles .= " }";
        }

        $this->aOptions['customdropdown-corestyles'] = rtrim($corestyles, ',');
    }

    /**
     * Removes empty paragraphs from posts before saving. Editor JS/DOM engine will not allow this, so we strip it to be sure it won't appear sometimes.
     *
     * @param string $content Raw Post content.
     *
     * @return string Post content with no empty paragraph tags
     */
    function remove_blank_p($content) {
        return str_replace('<p>&nbsp;</p>', '', $content);
    }

    /**
     * Replaces author meta box with custom version with "Plain text editing" checkbox.
     */
    function remove_meta_boxes($type, $context = '', $post = 0) {
        foreach (array('normal', 'advanced', 'side') as $context) {
            remove_meta_box('authordiv', 'post', $context);
        }
        foreach (array('normal', 'advanced', 'side') as $context) {
            remove_meta_box('pageauthordiv', 'page', $context);
            remove_meta_box('authordiv', 'page', $context);
        }
    }

    /**
     * Checks if the post was most recently edited by Foliopress WYSIWYG and disables wpautop and wptexturize. Also remember in first the_content call if these functions were on or of and add them only if they were active and the post was not recently edited in FP WYSIWYG. This is for loops
     *
     * @param string $content Raw Post content.
     *
     * @return string Post content not touched by wpautop and wptexturize if it was edited in Foliopress WYSIWYG
     */
    function the_content($content) {

        global $post;

        global $wp_filter;

        ///echo '<!--wysiwyg has_wpautop '.var_export( $this->has_wpautop, true ).'-->';
        ///echo '<!--wysiwyg has_wptexturize '.var_export( $this->has_wptexturize, true ).'-->';

        if ($this->has_wpautop === NULL) { ///echo '<!--wysiwyg store status: wpautop '.var_export( has_filter( 'the_content', 'wpautop' ), true ).'-->';
            $this->has_wpautop = has_filter('the_content', 'wpautop');
        }
        if ($this->has_wptexturize === NULL) { ///echo '<!--wysiwyg store status: wptexturize '.var_export( has_filter( 'the_content', 'has_wptexturize' ), true ).'-->';
            $this->has_wptexturize = has_filter('the_content', 'wptexturize');
        }

        $meta = get_post_meta($post->ID, '_wysiwyg', true);
        if (!$meta) {
            $meta = get_post_meta($post->ID, 'wysiwyg', true); //	check old meta
        }
        ///echo '<!--wysiwyg'.var_export( $meta, true ).' vs '.$post->post_modified.'-->';

        if ($meta['plain_text_editing'] == 1 || $meta['post_modified'] == $post->post_modified) {
            remove_filter('the_content', 'wpautop');
            remove_filter('the_content', 'wptexturize');
        } else {
            if ($this->has_wpautop) { ///echo '<!--wysiwyg +wpautop-->';
                add_filter('the_content', 'wpautop');
            }
            if ($this->has_wptexturize) { ///echo '<!--wysiwyg +wptexturize-->';     
                add_filter('the_content', 'wptexturize');
            }
        }

        return $content;
    }

    function the_editor($content) {
        if ($this->checkUserAgent() == 'ipad') {
            $content = '<p style="border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em; background-color: #FFEBE8; border-color: #CC0000; ">Sorry, iPad is not currently supported. Please use Safari, Firefox, IE 7, IE 8 or Chromium!</p>' . $content;
        }

        return $content;
    }

    /**
     * Disables standard visual editor
     *
     * @param bool $can User's richedit capability.
     *
     * @return bool false To disable standard visual editor
     */
    function user_can_richedit($can) {
        return false;
    }

    /**
     * Records post modification date and plain text editing setting
     * 
     * @param integer $id Post ID
     */
    function wp_insert_post($id) {

        $post = get_post($id);
        if ($post->post_type == 'revision')
            return $id;

        if ($post->post_type != 'post' && $post->post_type != 'page')
            return $id;

        $meta = get_post_meta($post->ID, '_wysiwyg', true);
        if (!$meta) {
            $meta = get_post_meta($post->ID, 'wysiwyg', true); //	check old meta
        }
        if (!isset($_POST['_inline_edit'])) {  //  we can't check for this in quick edit       
            if (isset($_POST['plain_text_editing'])) {
                $meta['plain_text_editing'] = true;
            } else {
                $meta['plain_text_editing'] = false;
            }
        }

        if (isset($meta['post_modified']) || !isset($_POST['_inline_edit'])) { //  only process post_modified if it already exists or if you are not in quick edit
            $meta['post_modified'] = $post->post_modified;
            update_post_meta($id, '_wysiwyg', $meta);
        }
    }

}

$fp_wysiwyg = new fp_wysiwyg_class();
?>