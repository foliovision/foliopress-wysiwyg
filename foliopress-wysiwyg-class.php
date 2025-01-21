<?php 
/**
 * Foliopress WYSIWYG class file
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled here
 *  
 * @author Foliovision s.r.o. <info@foliovision.com>
 * @version 2.6.17
 * @package foliopress-wysiwyg
 */

/**
 * Including wordpress
 */ 
//	WP < 2.7 compatibility
if( file_exists( dirname(__FILE__) . '/../../../wp-load.php' ) )
	require_once( realpath( dirname(__FILE__) . '/../../../wp-load.php' ) );
else
	require_once( realpath( dirname(__FILE__) . '/../../../wp-config.php' ) );
	
/**
 * Some basic functions for this class to work
 */
require_once( 'include/foliopress-wysiwyg-load.php' );
require_once( 'include/fp-api.php' );


/**
 * Main Foliopress WYSIWYG class
 *
 * Main class that handles all implementation of plugin into WordPress. All WordPress actions and filters are handled in it
 *
 * @author Foliovision s.r.o. <info@foliovision.com>
 */
class fp_wysiwyg_class extends Foliopress_WYSIWYG_Plugin {

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
	var $strVersion = '2.6.15';
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
	const FVC_SKINS_RELATIVE_PATH = '/fckeditor/editor/skins';
	/**
	 * Relative path to FCKEditor languages folder
	 */
	const FVC_LANG_RELATIVE_PATH = '/fckeditor/editor/lang';
	/**
	 * Relative path to KFM skins folder
	 */
	const KFM_LANG_RELATIVE_PATH = '/fckeditor/editor/plugins/kfm/lang';
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


///  -----------------------------------------------------------------------------------------------------------------
///  --------------------------------------------------   Methods   --------------------------------------------------
///  -----------------------------------------------------------------------------------------------------------------
	
	/**
	 * Class constructor. Sets all basic variables ({@link fp_wysiwyg_class::$strSiteUrl $strSiteUrl}, {@link fp_wysiwyg_class::$strPluginPath $strPluginPath},
	 * {@link fp_wysiwyg_class::$strFCKEditorPath $strFCKEditorPath}, {@link fp_wysiwyg_class::$iEditorSize $iEditorSize},
	 * {@link fp_wysiwyg_class::$aOptions $aOptions}) to proper values
	 */
	function __construct(){

    $this->readme_URL = 'http://plugins.trac.wordpress.org/browser/foliopress-wysiwyg/trunk/readme.txt?format=txt';    
	  add_action( 'in_plugin_update_message-foliopress-wysiwyg/foliopress-wysiwyg.php', array( &$this, 'plugin_update_message' ) );
	 
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
    
    
    
		$this->iEditorSize = 20 * intval( get_option( 'fv_default_post_edit_rows' ) );
		if( $this->iEditorSize < 240 ) $this->iEditorSize = 240;

		$this->aOptions = get_option( FV_FCK_OPTIONS );
		if( !isset( $this->aOptions[self::FVC_IMAGES] ) ) $this->aOptions[self::FVC_IMAGES] = '/images/';
		if( !isset( $this->aOptions[self::FVC_TOOLBAR] ) ) $this->aOptions[self::FVC_TOOLBAR] = 'Foliovision';
		if( !isset( $this->aOptions[self::FVC_SKIN] ) ) $this->aOptions[self::FVC_SKIN] = 'foliovision';
		if( !isset( $this->aOptions[self::FVC_WIDTH] ) ) $this->aOptions[self::FVC_WIDTH] = 0;
		if( !isset( $this->aOptions[self::FVC_KFM_LINK] ) ) $this->aOptions[self::FVC_KFM_LINK] = true;
		if( !isset( $this->aOptions[self::FVC_KFM_LIGHTBOX] ) ) $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;
		if( !isset( $this->aOptions[self::FVC_KFM_THUMBS] ) ) $this->aOptions[self::FVC_KFM_THUMBS] = array( 400, 200, 150 );
		if( !isset( $this->aOptions[self::FVC_FPC_TEXTS] ) ) $this->aOptions[self::FVC_FPC_TEXTS] = array( "*** (\\\\w\\\\*) ***", "\\\\[sniplet (\\\\w\\\\*)\\\\]" );
		if( !isset( $this->aOptions[self::FVC_JPEG] ) ) $this->aOptions[self::FVC_JPEG] = 80;
		if( !isset( $this->aOptions[self::FVC_PNG] ) ) $this->aOptions[self::FVC_PNG] = true;
		if( !isset( $this->aOptions[self::FVC_PNG_LIMIT] ) ) $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
		if( !isset( $this->aOptions[self::FVC_DIR] ) ) $this->aOptions[self::FVC_DIR] = true;
		if( !isset( $this->aOptions[self::FVC_KFM_THUMB_SIZE] ) ) $this->aOptions[self::FVC_KFM_THUMB_SIZE] = 128;
		/// Addition 2009/06/02  mVicenik Foliovision
		if( !isset( $this->aOptions[self::FVC_HIDEMEDIA] ) ) $this->aOptions[self::FVC_HIDEMEDIA] = true;
		/// End of addition
		/// Addition 2009/10/29   Foliovision
		if( !isset( $this->aOptions['customtoolbar'] ) ) $this->aOptions['customtoolbar'] =
            "['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','FontFormat','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','kfmBridge','FVWPFlowplayer','PasteEmbed','-','Source','-','FitWindow']";
        
        //  todo - add content
        if( !isset( $this->aOptions['customdropdown'] ) ) $this->aOptions['customdropdown'] = '<h5 class="">Centered image</h5>
<h5 class="left">Left aligned image</h5>
<h5 class="right">Right aligned image</h5>
<p>Normal paragraph</p>
<h1>Header 1</h1>
<h2>Header 2</h2>
<h3>Header 3</h3>
<h4>Header 4</h4>';
        $this->parse_dropdown_menu();
		
		if ( !isset( $this->aOptions['multipleimageposting'] ) ) $this->aOptions['multipleimageposting'] = true;
		
		if ( !isset( $this->aOptions['wysiwygstyles'] ) ) $this->aOptions['wysiwygstyles'] = "body { width: 600px; margin-left: 10px; }";
		if ( !isset( $this->aOptions['postmeta'] ) ) $this->aOptions['postmeta'] = "";
		if ( !isset( $this->aOptions['bodyid'] ) ) $this->aOptions['bodyid'] = "";
		if ( !isset( $this->aOptions['bodyclass'] ) ) $this->aOptions['bodyclass'] = "";

		if ( !isset( $this->aOptions['autowpautop'] ) ) $this->aOptions['autowpautop'] = true;
		
		
		if( !isset( $this->aOptions[self::FVC_MAXW] ) ) $this->aOptions[self::FVC_MAXW] = 960;
		if( !isset( $this->aOptions[self::FVC_MAXH] ) ) $this->aOptions[self::FVC_MAXH] = 960;
		if( !isset( $this->aOptions[self::FVC_USE_FLASH_UPLOADER] ) ) $this->aOptions[self::FVC_USE_FLASH_UPLOADER] = true;
		/// End of addition	
		
		if( !isset( $this->aOptions['ProcessHTMLEntities'] ) ) $this->aOptions['ProcessHTMLEntities'] = false;
		if( !isset( $this->aOptions['UseWPLinkDialog'] ) ) $this->aOptions['UseWPLinkDialog'] = false;
		
		/// Addition 2010/06/30
		if( !isset( $this->aOptions['FCKLang'] ) ) $this->aOptions['FCKLang'] = 'auto';
		if( !isset( $this->aOptions['FCKLangDir'] ) ) $this->aOptions['FCKLangDir'] = 'ltr';
		if( !isset( $this->aOptions['kfmlang'] ) ) $this->aOptions['kfmlang'] = 'en';
		if( !isset( $this->aOptions['dirperm'] ) ) $this->aOptions['dirperm'] = '777';
		if( !isset( $this->aOptions['fileperm'] ) ) $this->aOptions['fileperm'] = '666';
		/// End of addition
		if( !isset( $this->aOptions['filter_wp_thumbnails'] ) ) $this->aOptions['filter_wp_thumbnails'] = true;
		//if( !isset( $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] ) || $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] == '' ) $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = addslashes( '"<h5>"+sHtmlCode+"<br />"+sAlt+"</h5>"' );    
    
    if( !isset( $this->aOptions['image_h5'] ) && isset( $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] ) ) $this->aOptions['image_h5'] = true;    
    if( !isset( $this->aOptions['image_h5'] ) ) $this->aOptions['image_h5'] = false;
    
    if( !isset( $this->aOptions['convertcaptions'] ) && isset( $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] ) ) $this->aOptions['convertcaptions'] = true;
    if( !isset( $this->aOptions['convertcaptions'] ) ) $this->aOptions['convertcaptions'] = false;
    
    
    update_option( FV_FCK_OPTIONS, $this->aOptions ); 

		//$this->KillTinyMCE( null );
    
    if( is_admin() ) {
      parent::__construct();
    }
  
	}
	
	
	/**
	 * Register script for compatibility with WP Media Uploader. Thanks to Dean's FCK plugin!
	 */			
	function add_admin_js()
	{
		wp_deregister_script(array('media-upload'));
		wp_enqueue_script('media-upload', $this->strPluginPath .'media-upload.js', array('thickbox'), '20080710'); 
		//wp_enqueue_script('fckeditor', $this->fckeditor_path . 'fckeditor.js');
    
    ?>
    <style>.foliopress_wysiwyg_seo_images_gone #pointer-primary { display: none}</style>
    <?php
	}	
	
	
	/**
	 * Adds Options page to Wordpress.
	 */
	function AddOptionPage(){
		add_options_page( FV_FCK_NAME, FV_FCK_NAME, 'activate_plugins', 'fv_wysiwyg', array( &$this, 'OptionsMenuPage' ) );
	}
	

	/**
	 * Checks for GD.
	 */	
  function AdminNotices() {

    if( !function_exists( 'gd_info') && !$this->checkImageMagick() ) {

      echo '<div class="error fade">' . __('PHP GD Library or ImageMagick not installed! Foliopress WYSIWYG will not be able to handle your images!', 'fp_wysiwyg'). '</div>'; 

    }

  }	
		

	/**
	 * Init certain variables
	 */			
	function admin_init() {
    if( !get_option( 'fv_default_post_edit_rows' ) || version_compare( $this->strVersion, get_option( 'fp_wysiwyg_version') ) == 1 ) {
      if( get_option( 'default_post_edit_rows' ) < 20 ) {
        update_option( 'fv_default_post_edit_rows', 30 );
      } else {
        update_option( 'fv_default_post_edit_rows', get_option( 'default_post_edit_rows' ) );
      }
      update_option( 'fp_wysiwyg_version', $this->strVersion );
    }
    
	  if( $this->is_min_wp( '3.3' ) ) {
	    $this->strPluginPath = trim( plugins_url( '', __FILE__ ), '/' ).'/';
	    $this->strFCKEditorPath = trim( plugins_url( 'fckeditor', __FILE__ ), '/' ).'/';
	  } else if( $this->is_min_wp( '2.6' ) ) {
	    $this->strPluginPath = trailingslashit( WP_PLUGIN_URL ) . basename( dirname( __FILE__ ) ) . '/';
	    $this->strFCKEditorPath = trailingslashit( WP_PLUGIN_URL ) . basename( dirname( __FILE__ ) ) . '/fckeditor/';
	  } else { 
  	 	if(function_exists('site_url'))
        $strSite = trailingslashit( site_url() );
      else
        $strSite = trailingslashit( get_option('siteurl') );
  		$this->strSiteUrl = $strSite; //echo '<!-- purl'.plugins_url().' -->';
  		if( function_exists( 'plugins_url' ) && 1<0 ) {
  		  $this->strPluginPath = trailingslashit( plugins_url() );
  		}
  		else {
  		  $this->strPluginPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/';
  		}
  		$this->strFCKEditorPath = $strSite . 'wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/fckeditor/';
	  }
	  
    wp_deregister_script( 'editor-expand' );
    
    if( !get_option('foliopress_wysiwyg_seo_images_gone') ) {
      $this->pointer_boxes['foliopress_wysiwyg_seo_images_gone'] = array(
        'id' => '#wp-admin-bar-new-content',
        'pointerClass' => 'foliopress_wysiwyg_seo_images_gone',
        'heading' => 'Foliopress WYSIWYG - SEO Images',
        'content' => "<p>Due to PHP 7 compatibility issues and WordPress upgrades we were forced to remove our image management tool built on KFM. Please use Foliopress WYSIWYG 2.6.15 if you really need it.</p>",
        'position' => array( 'edge' => 'top', 'align' => 'center' ),
        'button1' => 'Allow',
        'button2' => 'Acknowledge',
      );
    }
	}	
	

	/**
	 * Links "Featured image" metabox to Foliopress WYSIWYG - SEO Image image manager
	 *
	 * @param string $html Original metabox HTML.
	 *
	 * @return string Altered metabox HTML.
	 */			
	function admin_post_thumbnail_html( $html ) {
	  if( !$this->process_featured_images ) {
	    return $html;
	  }
    
    if( $this->aOptions['kfmlang'] != 'auto' ) {
      $url = $this->strPluginPath.'fckeditor/editor/plugins/kfm/?lang='.$this->aOptions['kfmlang'].'&kfm_caller_type=fck&type=Image';
    } else {
      $url = $this->strPluginPath.'fckeditor/editor/plugins/kfm/?lang=en&kfm_caller_type=fck&type=Image';
    }
	  $onclick = 'onclick="window.open( \''.$url.'\', \'FCKBrowseWindow\', \'toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes,width=\'+(screen.width*0.7)+\',height=\'+(screen.height*0.7)+\',left=\'+(screen.width-screen.width*0.7)/2+\',top=\'+(screen.height-screen.height*0.7)/2); return false"';
	  
	  if( stripos( $html, 'set-post-thumbnail' ) !== FALSE && stripos( $html, '<img' ) === FALSE ) {
      $html = preg_replace( '~<a.*?set-post-thumbnail.*?</a>~', '', $html );

      $html .= '<p class="hide-if-no-js"><a title="' . __('Set Featured image with Foliopress WYSIWYG\'s Image Manager', 'fp_wysiwyg') . '" href="#" id="seo-images-featured-image" '.$onclick.'>' . __('Set featured image with SEO Images', 'fp_wysiwyg') . '</a></p>';
  	  return $html;
	  } else if( stripos( $html, 'set-post-thumbnail' ) !== FALSE && stripos( $html, '<img' ) !== FALSE ) {
	    $html = str_replace( 'set-post-thumbnail', 'set-post-thumbnail-fp-wysiwyg', $html );
	    $html = str_replace( 'class="thickbox"', '', $html );	    
	    $html = preg_replace( '~href=".*?type=image.*?TB_iframe=1"~', 'href="#" '.$onclick, $html );
	    return $html;
	  }
	}	
	
	
	function admin_print_footer_scripts() {     
	  if( $this->checkUserAgent() ) return;
	  
	  if( $this->loading ) {
      //remove_action( 'admin_print_footer_scripts', array( '_WP_Editors', 'editor_js'), 50 );
	    remove_action( 'admin_footer', array( '_WP_Editors', 'enqueue_scripts'), 1 );
      ?>
      <script>
        var fp_wysiwyg_remove_floating_toolbar = setInterval( function() {
          jQuery('#ed_toolbar.quicktags-toolbar').remove();
          clearInterval(fp_wysiwyg_remove_floating_toolbar);
        }, 100 );
      </script>
      <?php
	  }
	}

	function ap_action_init()
    {
        // Localization
        load_plugin_textdomain('fp_wysiwyg', false, dirname(plugin_basename(__FILE__)) . "/languages");
    }

    function get_js_translations() {
    	return Array(
    		'bracket_error' => __('String you entered is not well bracketed !', 'fp_wysiwyg'),
    		'size_error' => __('Please write correct size into text box !', 'fp_wysiwyg'),
    		);
    }
    
  function ajax_pointers() {
    if( isset($_POST['key']) && $_POST['key'] == 'foliopress_wysiwyg_seo_images_gone' && isset($_POST['value']) ) {
      check_ajax_referer('foliopress_wysiwyg_seo_images_gone');
      update_option( 'foliopress_wysiwyg_seo_images_gone', true );
      die();
    }
    
  }
	
  //  can we set featured images?	
  function check_featured_image_capability() {
		$uploads = wp_upload_dir();  			
		$domain = preg_replace( '~^(.*?//.*?)/.*$~', '$1', get_bloginfo('url' ) );
		$wp_uploads = str_replace( $domain, '', $uploads['baseurl'] );

		if( current_theme_supports('post-thumbnails') && rtrim( $wp_uploads, '/' ) == rtrim( $this->aOptions["images"], '/' ) ) {
		  $this->process_featured_images = true; 
		}	      
  }	
	
	function checkImageMagick() {
	  return @is_executable( '/usr/bin/convert' );
	}
	
	
	function checkUserAgent() {
	  if( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0' ) !== FALSE ) return 'ie9';
          else if( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0' ) !== FALSE ) return 'ie10';
          else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== FALSE) return 'ie11';
	  else if( stripos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) !== FALSE ) return 'ipad';
	  else return false;
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
  		$meta = get_post_meta( $post->ID, 'wysiwyg', true );
  
  		if( isset($meta['plain_text_editing']) && $meta['plain_text_editing'] == 1 ) {
  			return $content;
  		}
  		
  		if( $this->aOptions['convertcaptions'] ) {
  		    ///
  		    $content = preg_replace_callback( '/\[caption.*?\[\/caption\]/', array( &$this, 'convert_caption' ), $content );
  		    ///
      }
  		
  		if( isset($meta['plain_text_editing']) && $meta['post_modified'] == $post->post_modified ) {
  		  return $content;
  		} 		
  
      if(!$this->aOptions['autowpautop']) {
          return $content;
      }
      if(strlen($content)>0) {   // try to guess if the post should use wpautop
          if(stripos($content,'<p>')===FALSE)
              return wpautop($content);      
          /*if(stripos($content,'&lt;p&gt;')===FALSE && (stripos($content,'<')===FALSE || stripos($content,'>')===FALSE) )
              return wpautop($content);*/      
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
  function convert_caption( $content ) {
    $content = $content[0];

    preg_match( '/caption="(.*?)"/', $content, $caption );
    preg_match( '/align="(.*?)"/', $content, $align );
    
    if( $align && $align[1] != 'none' ) {
      $align = str_replace( 'align', '', $align[1] );
      $sClass = $this->h5_markup_get_class($align);
    }
    
    $content = preg_replace( '/\[caption[^\]]*?\]/', '', $content );
    $content = preg_replace( '/\[\/caption\]/', '', $content );
    
    if( $caption[1] ) {
      return '<h5'.$sClass.'>'.$content.'<br />'.$caption[1].'</h5>';
    }

    $content = str_replace( '</a> ', '</a><br />', $content );    
    
    return '<h5'.$sClass.'>'.$content.'</h5>';
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
	function ExtractOption( &$strText, $strOption, $strEndText ){
		$iStart = strpos( $strText, $strOption );
		if( false === $iStart ) return false;
		$iEnd = strpos( $strText, $strEndText, $iStart );
		$iOptionLength = strlen( $strOption );
		
		if( false === $iEnd ) return trim( substr( $strText, $iStart + $iOptionLength ) );
		else return trim( substr( $strText, $iStart + $iOptionLength, $iEnd - $iStart - $iOptionLength ) );
	}	


	/**
	 * Outputs into head section of html document script for FCK to load
	 */
  function FckLoadAdminHead(){		  
    if( ( strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'post.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page.php' ) ) && post_type_supports( get_post_type(), 'editor' ) ) :
    ?>
  <script type="text/javascript" src="<?php print( $this->strFCKEditorPath ); ?>fckeditor.js?ver=<?php echo $this->strVersion; ?>"></script>
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
	  $post_ID = intval( $_POST['post_id'] );
	  if( !current_user_can( 'edit_post', $post_ID ) ) {
		  die( '-1' );
	  }
	  check_ajax_referer( 'seo-images-featured-image-'.$post_ID );

	  $wp_upload_dir = wp_upload_dir();
    $domain = preg_replace( '~^(.*?//.*?)/.*$~', '$1', get_bloginfo('url' ) );
		$wp_uploads = str_replace( $domain, '', $wp_upload_dir['baseurl'] );	  
	  
	  $file = rtrim($wp_upload_dir['basedir'],'/').'/'.preg_replace( '~^'.$wp_uploads.'~', '', $_POST['imageURL'] );

	  if( $this->process_featured_images && file_exists($file) ) {
        $wp_filetype = wp_check_filetype( basename($file), null );
        $attachment = array(
           'post_mime_type' => $wp_filetype['type'],
           'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
           'post_content' => '',
           'post_status' => 'inherit'
        );
        
        global $wpdb; //  check if post with the same name already exists in the same date
        $time = current_time( 'mysql' );
    		$y = substr( $time, 0, 4 );
    		$m = substr( $time, 5, 2 );
    		$date = "$y-$m%";
    		
        $attach_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_date LIKE %s LIMIT 1", $attachment['post_title'], $date ) );
        
        if( !$attach_id ) {
          $attach_id = wp_insert_attachment( $attachment, $file, $post_ID );
        }

        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id,  $attach_data );	 
                 
        $thumbnail_id = $attach_id;
        if( $thumbnail_id && get_post( $thumbnail_id ) ) {
          delete_post_meta( $post_ID, '_thumbnail_id' ); 
      		$thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'thumbnail' );
      		if ( !empty( $thumbnail_html ) ) {
      			update_post_meta( $post_ID, '_thumbnail_id', $thumbnail_id );
      			die( _wp_post_thumbnail_html( $thumbnail_id, $post_ID ) );
      		}          
        }        
         
	  } else {
	    _e('File not found in Wordpress Media directory. Is the image uploads path same as Foliopress WYSIWYG Path?' , 'fp_wysiwyg');
	  }
	  die();
	}
	
	
	function fv_remove_mediabuttons($content) {
			global $post;
			$meta = get_post_meta( $post->ID, 'wysiwyg', true );
			if( isset( $meta['plain_text_editing'] ) && $meta['plain_text_editing'] == 1 ) {
				$this->bUseFCK = false;
				$aOptions = get_option( FV_FCK_OPTIONS );
				return '';
			}
			
			return $content;
   }
	
	
	/**
	 * Returns option if images should be wrapped in link (<a>). This function returns integer '1' and '0' depending on settings stored by user.
	 * If user haven't specified this option default value is '1'.
	 *
	 * @return int '1' if images returned by KFM into FCKEditor should be wrapped in <a> tag with link to the original image, '0' otherwise.
	 */
	function getLink(){
		$iLink = 1;
		if( isset( $this->aOptions[self::FVC_KFM_LINK] ) ){
			if( !$this->aOptions[self::FVC_KFM_LINK] ) $iLink = 0;
		}
		return $iLink;
	}
	
	/**
	 * Returns option if link to original image should contain 'rel="lightbox"', which triggers lightbox. Return values are integers '1' and '0' 
	 * depending on settings stored by user. If user haven't specified this option default value is '1'.
	 *
	 * @return int '1' if link to original image should contain 'rel="lightbox"', '0' otherwise
	 */
	function getLightbox(){
		$iLightbox = 1;
		if( isset( $this->aOptions[self::FVC_KFM_LIGHTBOX] ) ){
			if( !$this->aOptions[self::FVC_KFM_LIGHTBOX] ) $iLightbox = 0;
		}
		return $iLightbox;
	}
	
	/**
	 * Returns special KFM thumnails in string separated by this string ', '. Again this options is editable from Options page. Default value is
	 * '400, 200, 150'.
	 *
	 * @return string All sizes for KFM special thumbnails separated by string ', '.
	 */
	function getThumbsString(){
		$strThumbs = '400, 200, 150';
		if( isset( $this->aOptions[self::FVC_KFM_THUMBS] ) ){
			$strThumbs = '';
			$aThumbs = $this->aOptions[self::FVC_KFM_THUMBS];
			$iThumbs = count( $aThumbs );
			for( $i = 0; $i < $iThumbs; $i++ ){
				if( $i < $iThumbs - 1 ) $strThumbs .= $aThumbs[$i] . ', ';
				else $strThumbs .= $aThumbs[$i];
			}
		}
		
		return $strThumbs;
	}

	/**
	 * This function disables TinyMCE and sets {@link $bUseFCK} to true or false depending on which page is loaded
	 */
	function KillTinyMCE( $in ){
		global $current_user;

		if ( 'true' == $current_user->rich_editing && strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/profile.php') === false ){
			$this->bUseFCK = true;
			$current_user->rich_editing = 'false';
		}elseif ( isset($current_user) && $this->bUseFCK === null ){
			$this->bUseFCK = false;
		}

		return $in;
	}
   
   
  /**
   * Checks if post is using Imact and it's active
   */
  function has_impact( $post_id ) {
    $_use_impact = get_post_meta( $post_id, '_use_impact', true );
    $_impact_template = get_post_meta( $post_id, '_impact_template', true );
    if( $_use_impact == 'yes' && $_impact_template != '' && function_exists( 'impact_get_template' ) ) {
      return true;
    } else {
      return false;
    }
  }
	   
  
  /**
   * This function starts FCKEditor through javascript.
   */
	function LoadFCKEditor(){
	  if( $this->checkUserAgent() || !post_type_supports( get_post_type(), 'editor' ) ) return;
?>		
		<script type="text/javascript">
		<?php  //  detect FV WP Flowplayer
		if( has_action( 'media_upload_fvplayer_video' ) ) : ?>
			var g_fv_wp_flowplayer_found = true; 
		<?php else : ?>
			var g_fv_wp_flowplayer_found = false; 
		<?php endif; ?>
			
		function fv_wysiwyg_load(){
			var oFCKeditor = new FCKeditor( 'content', 
					'<?php
						if( $this->aOptions[self::FVC_WIDTH] != 0 ) print( $this->aOptions[self::FVC_WIDTH] . "px" );
						else print( "100%" );
					?>', 
					<?php print( $this->iEditorSize ); ?> );
			//oFCKeditor.Config["CustomConfigurationsPath"] = "<?php print( $this->strPluginPath . self::FVC_FCK_CONFIG_RELATIVE_PATH ); ?>";
	
			///  MAGIC MAGIC MAGIC MAGIC *** ***
			<?php $options = $this->aOptions; ?>
			
			/// Impact support
			<?php 
			global $post;
			if( $this->has_impact( $post->ID ) ) {
			  $options['bodyid'] = 'impact-content';
        $options['bodyclass'] .= ' impact-content-wrap'; 
        if( $post->post_type == 'page' ) {
          $options['bodyclass'] .= ' page'; 
        } else {
          $options['bodyclass'] .= ' post'; 
        }
			}
			?>
						
			oFCKeditor.Config["ProcessHTMLEntities"]	= <?php if( $options['ProcessHTMLEntities'] ) echo 'true'; else echo 'false' ?> ;  /*  affects quotes on = &quot;, off = "  */
			<?php if( $options[fp_wysiwyg_class::FVC_LANG] != 'auto' ) : ?>
      oFCKeditor.Config["AutoDetectLanguage"]	= false ;
      oFCKeditor.Config["DefaultLanguage"]		= '<?php echo $options[fp_wysiwyg_class::FVC_LANG]; ?>' ;
      <?php else : ?>
      oFCKeditor.Config["AutoDetectLanguage"]	= true ;
      oFCKeditor.Config["DefaultLanguage"]		= 'en' ;
      <?php endif; ?>
      oFCKeditor.Config["ContentLangDirection"]	= '<?php echo $options["FCKLangDir"]; ?>' ;
      
      oFCKeditor.Config["FontFormats"]	= '<?php echo $options["customdropdown-fontformats"]; ?>' ;
      
      oFCKeditor.Config["CustomConfigurationsPath" ] = " \
      \
        FCKConfig.ToolbarSets['Default'] = [ \
          ['Source','DocProps','-','Save','NewPage','Preview','-','Templates'], \
        	['Cut','Copy','Paste','foliopress-paste','PasteText','PasteWord','-','Print','SpellCheck'], \
        	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'], \
        	['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'], \
        	'/', \
        	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'], \
        	['OrderedList','UnorderedList','-','Outdent','Indent'], \
        	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'], \
        	['Link','Unlink','Anchor'], \
        	['FVWPFlowplayer','Table','Rule','Smiley','SpecialChar','PageBreak'], \
        	'/', \
        	['Style','FontFormat','FontName','FontSize'], \
        	['TextColor','BGColor'] \
        ]; \
        \
        FCKConfig.ToolbarSets['Basic'] = [ \
        	['Source', 'Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About'] \
        ]; \
        \
        FCKConfig.ToolbarSets['Foliovision'] = [ \
        	['Cut','Copy','Paste','foliopress-paste','-','Bold','Italic','-','FontFormat','RemoveFormat','-','OrderedList','UnorderedList','-','Outdent','Indent','Blockquote','-','Link','Unlink','Anchor','-','foliopress-more','-','FVWPFlowplayer','PasteEmbed','-','Source','-','FitWindow'] \
        ]; \
        \
        FCKConfig.ToolbarSets['Foliovision-Full'] = [ \
           ['Cut','Copy','Paste','-','Undo','Redo','-','Bold','Italic','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyFull','-','OrderedList','UnorderedList','-','Outdent','Indent','-','Link','Unlink','Anchor','-','FVWPFlowplayer','PasteEmbed'], '/', \
        	['FontFormat','RemoveFormat','-','Replace','Table','Rule','SpecialChar','-','foliopress-more','foliopress-next','-','Source','-','FitWindow'] \
        ]; \
        \
        FCKConfig.ToolbarSets['Custom'] = [ <?php echo str_replace( "\r\n", " ", stripslashes($options['customtoolbar']) ); ?> ]; \
        \
        FCKConfig.CoreStyles = \
        { \
        	<?php echo str_replace( "\r\n", " ", $options['customdropdown-corestyles'] ); ?>, \
        	'Bold' : { Element : 'strong', Overrides : 'b' }, 'Italic' : { Element : 'em', Overrides : 'i' }, 'Underline' : { Element : 'u' }, 'StrikeThrough' : { Element : 'strike' }, 'Subscript' : { Element : 'sub' }, 'Superscript' : { Element : 'sup' }, \
        	'p' : { Element : 'p' }, 'div' : { Element : 'div' }, 'pre' : { Element : 'pre' }, 'address' : { Element : 'address' }, 'h1' : { Element : 'h1' }, 'h2' : { Element : 'h2' }, 'h3' : { Element : 'h3' }, 'h4' : { Element : 'h4' }, 'h5' : { Element : 'h5' }, 'h6' : { Element : 'h6' }, \
        	'FontFace' : { Element : 'span', Styles : { 'font-family' : '#(\"Font\")' }, Overrides : [ { Element : 'font', Attributes : { 'face' : null } } ] }, \
        	'Size' : { Element : 'span', Styles : { 'font-size' : '#(\"Size\",\"fontSize\")' }, Overrides : [ { Element : 'font', Attributes : { 'size' : null } } ] }, \
        	'Color' : { Element : 'span', Styles : { 'color' : '#(\"Color\",\"color\")' }, Overrides : [ { Element : 'font', Attributes : { 'color' : null } } ] }, \
        	'BackColor' : { Element : 'span', Styles : { 'background-color' : '#(\"Color\",\"color\")' } }, 'SelectionHighlight' : { Element : 'span', Styles : { 'background-color' : 'navy', 'color' : 'white' } } \
        }; \
\
        FCKToolbarFontFormatCombo.prototype.GetStyles = function() \
        { \
        	var styles = {} ; \
        	var aNames = FCKLang['FontFormats'].split(';') ; \
        	var oNames = { \
        		<?php echo str_replace( "\r\n", " ", $options['customdropdown-fontformatnames'] ); ?>, \
        		p : aNames[0], pre : aNames[1], address : aNames[2], h1 : aNames[3], h2 : aNames[4], h3 : aNames[5], h4 : aNames[6], h5 : aNames[7], h6 : aNames[8], div : aNames[9] || ( aNames[0] + ' (DIV)') \
        	} ; \
        	var elements = FCKConfig.FontFormats.split(';') ; \
        \
        	for ( var i = 0 ; i < elements.length ; i++ ) \
        	{ \
        		var elementName = elements[ i ] ; \
        		var style = FCKStyles.GetStyle( '_FCK_' + elementName ) ; \
        		if ( style ) \
        		{ \
        			style.Label = oNames[ elementName ] ; \
        			styles[ '_FCK_' + elementName ] = style ; \
        		} \
        		else { \
        		} \
        	} \
        \
        	return styles ; \
        }; \
        \
        FCKConfig.BodyId = '<?php echo $options['bodyid']; ?>' ;  \
        FCKConfig.BodyClass = '<?php echo $options['bodyclass']; ?>' ;  \
        FCKConfig.EditorAreaCSS = FCKConfig.BasePath + '../../custom-config/foliopress-editor.php?p=<?php echo $post->ID; ?>'; \
        if( FCKConfig.BodyId || FCKConfig.BodyClass ) { \
        	FCKConfig.BodyClass = FCKConfig.BodyClass + ' wysiwyg'; \
        } \
        FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/<?php print( $this->aOptions[fp_wysiwyg_class::FVC_SKIN] ); ?>/'; \
        \
        FCKConfig.Plugins.Add( 'foliopress-wp' ); \
        FCKConfig.Plugins.Add( 'foliopress-clean' ); \
        FCKConfig.Plugins.Add( 'foliopress-paste-embed' ); \
        <?php  
        	if( count( $options[fp_wysiwyg_class::FVC_FPC_TEXTS] ) ){
        		print( 'FCKConfig.FPClean_SpecialText = [' );
        		$aFP = $options[fp_wysiwyg_class::FVC_FPC_TEXTS];
        		$iFP = count( $aFP );
        		for( $i=0; $i<$iFP; $i++ ){
        
        			if( $i < $iFP - 1 ) print( " '".addslashes($aFP[$i])."'," );
        			else print( " '".addslashes($aFP[$i])."' " );
        		}
        		print( "];\\\n" );
        	}
        ?> \
        FCKConfig.FPClean_Tags = 'p|div'; \
        FCKConfig.RemoveFormatTags = 'b,big,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var' ; \
        FCKConfig.Plugins.Add( 'foliopress-preformated' ); \
        FCKConfig.Plugins.Add( 'FVWPFlowplayer' ); \
        FCKConfig.Plugins.Add( 'foliopress-rgb-colors-replacer' ); \
      ";

      <?php if( $options['UseWPLinkDialog'] ) : ?>
      oFCKeditor.Config["FVLinkingDialog"] = 'dialog/fck_link_wp.html';
			oFCKeditor.Config["FVLinkingDialogHeight"] = 580;	
			<?php else : ?>      
			oFCKeditor.Config["FVLinkingDialog"] = 'dialog/fck_link.html';
			oFCKeditor.Config["FVLinkingDialogHeight"] = 300;
			<?php endif; ?>
			
			///  MAGIC MAGIC MAGIC MAGIC *** ***
				
			oFCKeditor.BasePath = "<?php print( $this->strFCKEditorPath ); ?>";
			oFCKeditor.Config["BaseHref"] = "<?php print( $_SERVER['SERVER_NAME'] ); ?>";
			oFCKeditor.ToolbarSet = "<?php print( $this->aOptions[self::FVC_TOOLBAR] ); ?>";
			oFCKeditor.ReplaceTextarea();
		}
		
		<?php
		if( $this->bUseFCK ) {
			print( 'fv_wysiwyg_load();' );
		} ?>
		
		<?php if( $GLOBALS['wp_version'] >= 2.7 ) : ?>
		jQuery(document).ready(function() {
		  window.setTimeout("fv_wysiwyg_startup();", 1000);
    });
    
    function fv_wysiwyg_startup() {
      if( typeof(FCKeditorAPI) != 'undefined' ) {
        FCKeditorAPI.GetInstance('content').GetXHTML(); //  don't remove
        if( typeof( FCKeditorAPI.GetInstance('content').EditorDocument ) != 'undefined' ) { //  IE might not be ready to reset the dirty flag yet
          FCKeditorAPI.GetInstance('content').ResetIsDirty();
        } else {
          window.setTimeout("fv_wysiwyg_startup();", 1000);
        }
        window.setTimeout("fv_wysiwyg_update_content();", 5000);
      } else {
        setTimeout("fv_wysiwyg_startup();", 1000);
      }
    }
    
		function fv_wysiwyg_update_content() {
		  if( typeof(FCKeditorAPI) != 'undefined' ) {
		    if( FCKeditorAPI.GetInstance('content').IsDirty() ) {
		      jQuery('#content').val( FCKeditorAPI.GetInstance('content').GetXHTML() );
		    }

		    if( typeof(wpWordCount) != "undefined" ) wpWordCount.wc( FCKeditorAPI.GetInstance('content').GetXHTML() );
		    //if( typeof(window.wp) != "undefined" && typeof(window.wp.utils) != "undefined" && typeof(window.wp.utils.WordCounter) != "undefined" ) window.wp.utils.WordCounter.prototype.count( FCKeditorAPI.GetInstance('content').GetXHTML() );

		    setTimeout("fv_wysiwyg_update_content();", 5000);
		  }

		}
		<?php global $post;   /// 0.5.2 SEO Images  ?>
    /**
     *	Adds/updates post meta using WP posting screen
     */
    function FCKSetHTML( html ) {
      FCKeditorAPI.GetInstance('content').InsertHtml( html ); //  todo add Safari fix
    }		
		
		
    /**
     *	Adds/updates post meta using WP posting screen
     */
    function FCKSetWPMeta( metaKey, metaValue ) {
    	// id of the key field
    	
    	//var keyId = jQuery( '[id$=[key]][value='+metaKey+']' ).attr('id');
    	var keyId = jQuery( 'input[value="custom_image"]' ).attr('id');
    	if( keyId ) {
      	valueId = keyId.replace( /key/, 'value' );
      	
      	var reg = /\d+/gm;
      	var metaId = keyId.match( reg );
      	var textarea = window.parent.jQuery( '#meta\\['+metaId+'\\]\\[value\\]' )
      
      	textarea.val( metaValue );
      	window.parent.jQuery( '[class^=add:the-list:meta-'+metaId+'::]' ).click( );  //  update click
    	}
    	// if the field doesn't exist
    	else {
    	  jQuery( '#metakeyinput' ).val( metaKey );
    	  jQuery( '#metavalue' ).val( metaValue );
    	  jQuery( '#addmetasub' ).click( );  //  add click
    	}
    }
    
    
    /**
     *	Updates field on WP posting screen
     */
    function FCKSetWPEditorField( metaKey, metaValue ) {
      if( jQuery( '#'+metaKey ) ) 
        jQuery( '#'+metaKey ).val( metaValue );
      if( jQuery( '[name='+metaKey+']' ) ) 
        jQuery( '[name='+metaKey+']' ).val( metaValue );
    }
    
    var SEOImagesPostId = '<?php echo $post->ID; ?>';
    var SEOImagesAjaxUrl = '<?php echo admin_url('admin-ajax.php') ?>';

	  var SEOImagesAjaxNonce ='<?php echo wp_create_nonce( "seo-images-featured-image-".$post->ID ); ?>';
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
    var fp_wysiwyg_crh_interval = setInterval( function() {
      jQuery('#content-resize-handle').remove();
      window.clearInterval(fp_wysiwyg_crh_interval);
    }, 250 );
		</script>
<?php 
    $this->loading = true;
	}
  
  
  function h5_markup( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
    if( !isset($this->aOptions['image_h5']) || !$this->aOptions['image_h5'] ) {
      return $html;
    }
    
    $caption = apply_filters( 'image_add_caption_text', $caption, $id );
    
    $new = '<h5';  
    $new .= $this->h5_markup_get_class($align);    
    $html = str_replace( 'class="align'.$align.' ', 'class="', $html );
    $new .= ">".$html;
    
    if( $caption ) {
      $new .= "<br />".$caption;
    }
    
    $new .= "</h5>";
    return $new;
  }
  
  
  function h5_markup_get_class( $align ) {
    if( $align != 'none' ) {
      if( strlen($this->aOptions['customdropdown']) > 0 ) {
        $aFormats = explode( "\n", $this->aOptions['customdropdown'] );
        if( count($aFormats) > 0 ) {
          foreach( $aFormats AS $line ) {
            if( stripos($line,'<h5') !== false && stripos($line,$align) !== false ) {
              if( preg_match( '~[a-z]*'.$align.'[a-z]*~', $line, $match ) ) {
                $align = $match[0];
              }
              break;
            }
          }
        }
      }
      
      $sClass = " class='".$align."'";
    }

    return $sClass;
  }  
  
  
  function image_disable_captions( $html ) {
    if( isset($this->aOptions['image_h5']) && $this->aOptions['image_h5'] ) {
      remove_filter( 'image_send_to_editor', 'image_add_caption', 20, 8 );
    }
    return $html;
  }
  
  
  function image_link_to_file( $settings ) {
    $settings['defaultProps']['link'] = 'file';
    return $settings;
  }
	
	/**
	 * Custom "Post Author" editing meta box with "Plain text editing" checkbox.
	 */ 
  function meta_box() {
    global $current_user, $user_ID, $post;
    ?>
    <label class="screen-reader-text" for="post_author_override"><?php _e('Author', 'fp_wysiwyg'); ?></label>
    <?php      
    if( function_exists( 'get_users' ) ) {
    	wp_dropdown_users( array(
    		'capability' => 'edit_posts',
    		'name' => 'post_author_override',
    		'selected' => empty($post->ID) ? $user_ID : $post->post_author,
    		'include_selected' => true
    	) );
    } else {
      $authors = get_editable_user_ids( $current_user->id, true, $post->post_type ); // TODO: ROLE SYSTEM
      if ( $post->post_author && !in_array($post->post_author, $authors) ) {
        $authors[] = $post->post_author;
      }
      wp_dropdown_users( array('include' => $authors, 'name' => 'post_author_override', 'selected' => empty($post->ID) ? $user_ID : $post->post_author) ); ?>
      <?php
    }
    
    $meta = get_post_meta( $post->ID, 'wysiwyg', true );
    if( !is_array($meta) ) $meta = array();
    if( !isset( $meta['plain_text_editing'] ) ) {
      $meta['plain_text_editing'] = false;
    }
    ?><label for="plain_text_editing"><input name="plain_text_editing" type="checkbox" id="plain_text_editing" value="true" <?php checked(1, $meta['plain_text_editing']); ?> /> <?php _e('Plain text editing', 'fp_wysiwyg'); ?> <abbr title="<?php _e('This will disable WYSIWYG editor for this post, as well as all the WP formating routines (wptexturize and wpautop). Turn this option off only if you are sure this post won\'t get destroyed by it.', 'fp_wysiwyg') ?>">(?)</abbr>
    </label>
  <?php
  }
     
    
  function meta_box_add() {
    add_meta_box('foliopress-wysiwyg',__('Post Author', 'fp_wysiwyg'), array(&$this, 'meta_box'), 'post', 'side','high');
    add_meta_box('foliopress-wysiwyg',__('Post Author', 'fp_wysiwyg'), array(&$this, 'meta_box'), 'page', 'side','high');	
  }
  	
	
	/**
	 * Loads Options page. It also stores all changes and also loads KFM thumbnails recreation.
	 */
	function OptionsMenuPage(){
		$bError = false;
		$strCustomError = '';
		$strErrDesc = '';

		$strPath = dirname( __FILE__ );
		
		$iOldErrorSettings = error_reporting( 0 );
		
		try{
			
			/// This is regular saving of options that are on the main Options page
			if( isset( $_POST['options_save'] ) ){
			
			  if( isset( $_POST['fv_default_post_edit_rows'] ) ) {
			    update_option('fv_default_post_edit_rows', intval( $_POST['fv_default_post_edit_rows'] ) );
			  }

				$this->aOptions[self::FVC_IMAGES_CHANGED]=false;
				if ($this->aOptions[self::FVC_IMAGES] != $_POST['ImagesPath']){
					$this->aOptions[self::FVC_IMAGES_CHANGED]=true;
				}

				
				$this->aOptions[self::FVC_SKIN] = $_POST['FCKSkins'];
				$this->aOptions[self::FVC_TOOLBAR] = $_POST['FCKToolbar'];

				if(strrpos($_POST['ImagesPath'],'/')!=(strlen($_POST['ImagesPath'])-1) && $_POST['ImagesPath']!='/')
				  $_POST['ImagesPath'] = $_POST['ImagesPath'].'/';
				if($_POST['ImagesPath']=='')
				  $this->aOptions[self::FVC_IMAGES] = '/';
			   else
				  $this->aOptions[self::FVC_IMAGES] = $_POST['ImagesPath'];

				$this->aOptions[self::FVC_WIDTH] = $_POST['FCKWidth'];

				$this->aOptions[self::FVC_KFM_LINK] = false;
				if( isset( $_POST['KFMLink'] ) && 'yes' == $_POST['KFMLink'] ) $this->aOptions[self::FVC_KFM_LINK] = true;
				$this->aOptions[self::FVC_KFM_LIGHTBOX] = false;
				if( isset( $_POST['KFMLightbox'] ) && 'yes' == $_POST['KFMLightbox'] ) $this->aOptions[self::FVC_KFM_LIGHTBOX] = true;
			
				$this->aOptions[self::FVC_HIDEMEDIA] = true;
				if( isset( $_POST['HideMediaButtons'] ) ) $this->aOptions[self::FVC_HIDEMEDIA] = false;
				
				$this->aOptions['multipleimageposting'] = false;
				if( isset( $_POST['MultipleImagePosting'] ) ) $this->aOptions['multipleimageposting'] = true;
				
				$this->aOptions['autowpautop'] = false;
				if( isset( $_POST['PreWPAutop'] ) ) $this->aOptions['autowpautop'] = true;
				
				$this->aOptions['convertcaptions'] = false;
				if( isset( $_POST['convertcaptions'] ) ) $this->aOptions['convertcaptions'] = true;
				
				if( isset( $_POST['bodyid'] ) ) $this->aOptions['bodyid'] = $_POST['bodyid'];
				if( isset( $_POST['bodyclass'] ) ) $this->aOptions['bodyclass'] = $_POST['bodyclass'];
				if( isset( $_POST['customtoolbar'] ) ) $this->aOptions['customtoolbar'] = stripslashes($_POST['customtoolbar']);
				if( isset( $_POST['customdropdown'] ) ) $this->aOptions['customdropdown'] = stripslashes($_POST['customdropdown']);
				
        $this->parse_dropdown_menu();

				if( isset( $_POST['wysiwygstyles'] ) ) $this->aOptions['wysiwygstyles'] = stripslashes( $_POST['wysiwygstyles'] );
				
				$this->aOptions['ProcessHTMLEntities'] = false;
				if( isset( $_POST['ProcessHTMLEntities'] ) ) $this->aOptions['ProcessHTMLEntities'] = true;
				
				if( isset( $_POST['FCKLang'] ) ) $this->aOptions['FCKLang'] = $_POST['FCKLang'];
				if( isset( $_POST['FCKLangDir'] ) ) $this->aOptions['FCKLangDir'] = $_POST['FCKLangDir'];
				if( isset( $_POST['kfmlang'] ) ) $this->aOptions['kfmlang'] = $_POST['kfmlang'];
				if( isset( $_POST['fileperm'] ) ) $this->aOptions['fileperm'] = $_POST['fileperm'];
				if( isset( $_POST['dirperm'] ) ) $this->aOptions['dirperm'] = $_POST['dirperm'];				

				if( isset( $_POST['KFMThumbCount'] ) ){
					$aThumbs = array();
					for( $i=0; $i<$_POST['KFMThumbCount']; $i++ )
						if( isset( $_POST['KFMThumb'.$i] ) ) $aThumbs[] = $_POST['KFMThumb'.$i];
					$this->aOptions[self::FVC_KFM_THUMBS] = $aThumbs;
				}
				
				if( isset( $_POST['FPCleanCount'] ) ){
					$aFPTexts = array();
					for( $i=0; $i<$_POST['FPCleanCount']; $i++ )
						if( isset( $_POST['FPClean'.$i] ) ) $aFPTexts[] = $_POST['FPClean'.$i];
					$this->aOptions[self::FVC_FPC_TEXTS] = $aFPTexts;
				}
				
				$this->aOptions[self::FVC_JPEG] = intval( $_POST['JPEGQuality'] );
				if( $this->aOptions[self::FVC_JPEG] < 0 || $this->aOptions[self::FVC_JPEG] > 100 ) $this->aOptions[self::FVC_JPEG] = 80;
				$this->aOptions[self::FVC_PNG] = isset( $_POST['PNGTransform'] ) ? true : false;
				$this->aOptions[self::FVC_PNG_LIMIT] = intval( $_POST['PNGLimit'] );
				if( $this->aOptions[self::FVC_PNG_LIMIT] < 0 || $this->aOptions[self::FVC_PNG_LIMIT] > 50000 ) $this->aOptions[self::FVC_PNG_LIMIT] = 5000;
				$this->aOptions[self::FVC_DIR] = isset( $_POST['DIRset'] ) ? true : false;
				
				if( isset( $_POST['MaxWidth'] ) ) $this->aOptions[self::FVC_MAXW] = intval( $_POST['MaxWidth'] );
				if( isset( $_POST['MaxHeight'] ) ) $this->aOptions[self::FVC_MAXH] = intval( $_POST['MaxHeight'] );
				if( intval( $_POST['KFMThumbnailSize'] ) < 64 ) {
				  $_POST['KFMThumbnailSize'] = 64;
				} else if( intval( $_POST['KFMThumbnailSize'] ) > 256 ) {
				  $_POST['KFMThumbnailSize'] = 256;
				}							
				if( isset( $_POST['KFMThumbnailSize'] ) ) $this->aOptions[self::FVC_KFM_THUMB_SIZE] = intval( $_POST['KFMThumbnailSize'] );
				
				$this->aOptions[self::FVC_USE_FLASH_UPLOADER] = false;
				if( isset( $_POST[self::FVC_USE_FLASH_UPLOADER] ) ) $this->aOptions[self::FVC_USE_FLASH_UPLOADER] = true;
				
				$this->aOptions[self::FV_SEO_IMAGES_POSTMETA] = $_POST['postmeta'];
				
				if( $_POST[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] != '' ) {
				  $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = $_POST[self::FV_SEO_IMAGES_IMAGE_TEMPLATE];								
				} else {
				  $this->aOptions[self::FV_SEO_IMAGES_IMAGE_TEMPLATE] = addslashes( '"<h5>"+sHtmlCode+"<br />"+sAlt+"</h5>"' );
				}
        
				$this->aOptions['image_h5'] = false;
				if( isset( $_POST['image_h5'] ) ) $this->aOptions['image_h5'] = true;
				
				$this->aOptions['UseWPLinkDialog'] = false;
				if( isset( $_POST['UseWPLinkDialog'] ) ) $this->aOptions['UseWPLinkDialog'] = true;				
				
				update_option( FV_FCK_OPTIONS, $this->aOptions );
			}
		}catch( Exception $ex ){
			$bError = true;
			$strErrDesc = $ex->getMessage();
			
			$iPos = strpos( $strErrDesc, 'file_put_contents' );
			if( false !== $iPos ){
				if( strpos( $strErrDesc, 'config.ini' ) ){
					$strCustomError = __('SEO Images (KFM) config.ini file is read-only. In order to change options this file has to be rewritten.', 'fp_wysiwyg');
					$strCustomError .= __('Please adjust the file permissions to this file. For further help, read the manual !', 'fp_wysiwyg');
				}
			}
		}
		
		error_reporting( $iOldErrorSettings );
		
    /// Loading of pages based on request
    if( isset( $_POST['recreate'] ) ) include( $strPath . '/view/recreate.php' );
    elseif( !isset( $_GET['edit'] ) ) include( $strPath . '/view/options.php' );
	}
			

	/**
	 * Formats plugin options into FCKeditor JS configuration statements.
	 */  		
  function parse_dropdown_menu() {
    $items = explode("\n",$this->aOptions['customdropdown']);             //  one item per line
    $i = 0;                                                                 //  counter
    
    $corestyles = '';
    $fontformats = '';
    $fontformatnames = '';
    
    foreach ($items AS $item) {
      $i++;
      preg_match('/<(.*?)>/i',$item,$match);                              //  take only the part inside <>
      if(!$match[0])
      continue;
      preg_match('/<([^>]*?)[\s>]/i',$match[0],$element);                    //  match the element name
      preg_match('/>([^<]*?)(<|$)/i',$item,$name);                            //  match the enclosed text or the text after the singular tag - name
      
      preg_match_all('/([a-z]*?)="(.*?)"/i',$match[0],$attributes);       //  match the attributes
      $attr_text = '';
      $styles_text = '';
      if(isset($attributes[1])) {
        foreach($attributes[1] AS $key => $attribute) {
          if(strcasecmp('style',$attribute)==0) {                     //  style
            $styles_text .= $attributes[2][$key];
          }
          else {                                                      //  everything else
            if(isset($attributes[2][$key]))
              $attr_text .= '\''.$attribute.'\' : \''.$attributes[2][$key].'\', ';
            else
              $attr_text .= '\''.$attribute.'\', ';
          }
        }
      }
      $styles_text = preg_replace('/\b([^;]*?):/i','\'$1\' :',$styles_text);  //  put css property into ''
      $styles_text = preg_replace('/\b([^\s]*?);/i','\'$1\', ',$styles_text);  //  put css values into ''
      
      $attr_text = rtrim($attr_text,', ');
      $styles_text = rtrim($styles_text,', ');
      
      if(strlen($corestyles)>0)
        $corestyles .= ",\r\n";
      $corestyles .= "'".$element[1]."_".$i."' : { Element : '".$element[1]."'";        //  do the proper output
      if(strlen($attr_text)>0)
        $corestyles .= ", Attributes : { ".$attr_text." } ";
      if(strlen($styles_text)>0)
        $corestyles .= ", Styles : { ".$styles_text." } ";
      
      $corestyles .= " }";
      
      $fontformats .= "".$element[1]."_".$i.";";
      if(strlen($fontformatnames)>0)
        $fontformatnames .= ",\r\n";
      $fontformatnames .= "".$element[1]."_".$i." : '".$name[1]."'";   
    }
    $this->aOptions['customdropdown-fontformats'] = rtrim($fontformats,';');
    $this->aOptions['customdropdown-corestyles'] = rtrim($corestyles,',');
    $this->aOptions['customdropdown-fontformatnames'] = rtrim($fontformatnames,',');
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
  function remove_meta_boxes($type, $context = '', $post = 0){
    foreach (array('normal', 'advanced', 'side') as $context){
      remove_meta_box('authordiv', 'post', $context);
    }    
    foreach (array('normal', 'advanced', 'side') as $context){
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
    
    if( $this->has_wpautop === NULL ) { ///echo '<!--wysiwyg store status: wpautop '.var_export( has_filter( 'the_content', 'wpautop' ), true ).'-->';
      $this->has_wpautop = has_filter( 'the_content', 'wpautop' );
    }
    if( $this->has_wptexturize === NULL ) { ///echo '<!--wysiwyg store status: wptexturize '.var_export( has_filter( 'the_content', 'has_wptexturize' ), true ).'-->';
      $this->has_wptexturize = has_filter( 'the_content', 'wptexturize' );
    }    

    //echo '<!--wysiwyg has_filter wpautop '.var_export( has_filter( 'the_content', 'wpautop' ), true ).'-->';
    //echo '<!--wysiwyg has_filter wptexturize '.var_export( has_filter( 'the_content', 'wptexturize' ), true ).'-->';    

    

    $meta = get_post_meta( $post->ID, 'wysiwyg', true );
    ///echo '<!--wysiwyg'.var_export( $meta, true ).' vs '.$post->post_modified.'-->';

    if(
      ( isset($meta['plain_text_editing']) && $meta['plain_text_editing'] == 1 ) || 
      ( isset($meta['post_modified']) && $meta['post_modified'] == $post->post_modified )
    ) {

      remove_filter ('the_content',  'wpautop');

      remove_filter ('the_content',  'wptexturize');

    }

    else {
      if( $this->has_wpautop ) { ///echo '<!--wysiwyg +wpautop-->';

        add_filter ('the_content',  'wpautop');
      }
      if( $this->has_wptexturize ) { ///echo '<!--wysiwyg +wptexturize-->';     

        add_filter ('the_content',  'wptexturize');	
      }

    }

    return $content;

  }		
  
  
  function the_editor( $content ) {
    $userAgent = $this->checkUserAgent();
    if( $userAgent == 'ie9' ) {
      $content = '<p style="border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em; background-color: #FFEBE8; border-color: #CC0000; ">Internet Explorer 9 is currently having issues with Foliopress WYSIWYG. You can try to switch to IE8 in the IE9 Developer tools (hit F12 key).<br/><br/>For proper functionality we recomend you to use Safari, Firefox or Chromium!</p>'.$content;
    } else if($userAgent == 'ie10') {
      $content = '<p style="border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em; background-color: #FFEBE8; border-color: #CC0000; ">Internet Explorer 10 is currently having issues with Foliopress WYSIWYG. You can try to switch to IE8 in the IE10 Developer tools (hit F12 key -> "Browser Mode" and "Document Mode").<br/><br/>For proper functionality we recomend you to use Safari, Firefox or Chromium!</p>'.$content;
    } else if($userAgent == 'ie11') {
      $content = '<p style="border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em; background-color: #FFEBE8; border-color: #CC0000; ">Internet Explorer 11 is currently having issues with Foliopress WYSIWYG. You can try to switch to IE8 in the IE11 Developer tools (hit F12 key -> Emulation -> "Document mode" and "User agent string").<br/><br/>For proper functionality we recomend you to use Safari, Firefox or Chromium!</p>'.$content;  
    }else if( $userAgent == 'ipad' ) {
      $content = '<p style="border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em; background-color: #FFEBE8; border-color: #CC0000; ">Sorry, iPad is not currently supported. Please use Safari, Firefox, IE 7, IE 8 or Chromium!</p>'.$content;
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

    $post = get_post( $id );
    if( $post->post_type == 'revision' )
      return $id;
    
    $meta = get_post_meta( $id, 'wysiwyg', true );
    if( !is_array($meta) ) $meta = array();
    if( !isset($_POST['_inline_edit']) ) {  //  we can't check for this in quick edit       
      if( isset( $_POST['plain_text_editing']) ) {
        $meta['plain_text_editing'] = true;
      }
      else {
        $meta['plain_text_editing'] = false;
      }
    }
    
    if( isset( $meta['post_modified'] ) || !isset($_POST['_inline_edit']) ) { //  only process post_modified if it already exists or if you are not in quick edit
      $meta['post_modified'] = $post->post_modified;
      update_post_meta( $id, 'wysiwyg', $meta );      
    }
    


  }

	function wp_editor_settings( $settings ) {
		global $post;
		$meta = get_post_meta( $post->ID, 'wysiwyg', true );
		if( isset( $meta['plain_text_editing'] ) && $meta['plain_text_editing'] == 1 ) {
			$settings[ 'media_buttons' ] = false;
		}
		return $settings;
	}
	 
}

$fp_wysiwyg = new fp_wysiwyg_class();

?>
