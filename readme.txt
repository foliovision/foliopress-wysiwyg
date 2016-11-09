=== Foliopress WYSIWYG ===
Contributors: FolioVision
Tags: wysiwyg,editor,foliopress,image,images,seo,lightbox,fck,tinymce,editor
Requires at least: 3.1 
Tested up to: 4.6.1
Stable tag: trunk

Foliopress WYSIWYG is the editor you were always hoping for, every time you installed a new content management system.

== Description ==

Foliopress WYSIWYG is the editor you were always hoping for, every time you installed a new content management system.

* Foliopress WYSIWYG is simple and correctly configured straight out of the box.
* Foliopress WYSIWYG handles images and text equally well.
* Foliopress WYSIWYG gives you **SEO ready images** (properly labelled with caption and alt and title tags).
* Foliopress WYSIWYG is simple enough to use that your clients will love it.
* Foliopress WYSIWYG has all the **extra control and flexibility** you want to be able to do advanced coding on your content pages.
* Foliopress WYSIWYG looks great in your browser window. No more eyesore when using an online text editor. We aren't living in the 90's anymore and our online text editors shouldn't look like WordStar.
* Foliopress WYSIWYG has easy and quick access to source code for experts so your programmers won't get frustrated and turn it off.
* Foliopress WYSIWYG produces **standards compliant html quickly and easily**.
* Foliopress WYSIWYG is forgiving: even if you make some terrible HTML/XHTML errors Foliopress WYSIWYG? will always give you or your clients it's best version of your document without erasing it.
* Foliopress WYSIWYG does **true WYSIWYG**. You can actually see the text in your edit box the same way it is in your content box (simple three step point and click configuration).
* Foliopress WYSIWYG will never go out of date: Foliopress WYSIWYG is assembled from best of breed open source projects so it will always be on the cutting edge of web design. The parts are carefully assembled as modules with no modifications to core code so you can always drop the latest version of the core libraries in for a seamless and instant upgrade.
* Foliopress WYSIWYG is so easy and fun to use, that you just might want to retire your word processor and write all your documents online.
* Uses FCKEditor (FCK) with upgrades, equivalent to CKEditor.
* Includes a fully extended toolbar just like tinyMCE Advanced.

[Support](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg) |
[Change Log](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/changelog)

== Installation ==

You can use the built in installer and upgrader, or you can install the plugin
manually. 

== Screenshots ==

1. Foliopress WYSIWYG default toolbar
2. Image Management Window with the insert thumbnail options
3. Options screen

== Frequently Asked Questions ==

= Is there any illustrated user guide? =

Yes, right here: http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/end-user-guide

= What about plugin configuration? =

Check out <a href="http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/installation">Foliopress WYSIWYG Installation page</a> for all the advanced stuff.

= Is Foliopress WYSIWYG able to work with the images already stored on the site? =

Yes it is. Depending on your PHP configuration, you might have to change the directory permissions according to [this guide](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/prepare-ftp-files-for-seo-images) in order to make thumbnails work.

= What about more styling buttons for font color, size etc.?  =

You can configure it in the Advanced options. Check out "Toolbar customization" on our <a href="http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/installation">Foliopress WYSIWYG Installation page</a> for instructions. However we recommend you to customize the styling dropdown instead and put in all the styling classes you need.

= How do I add a caption to the image? =

First of all, make sure your image is properly named before uploading. Our image uploader will make sure that the actual uploaded filename will be SEO optimized. Then insert the image with the "Send thumbnail" function and the image name will be used as its caption. Then you can change it to anything you want, or remove it.

The caption is bound to the image using an H5 tag. Here's the example HTML:

`<h5><a rel="lightbox[slideshow]" title="testing image" href="/images/2009/11/testing-image.png"><img height="419" width="400" alt="testing image" src="/images/2010/06/400/testing-image.png" /></a><br />Image description</h5>`

= What CSS is needed for the image alignment functions your editor provides? =

Inserted images are put into a H5 tag together with the image description. Here's our standard CSS we use in our templates to display the images in H5.

`/* Images in h5 */
a img { border: none; }
h5 { 	margin: 10px; padding: 0; line-height: 1.3em; font-size: 0.85em; font-weight: normal; text-align: center; }
h5 img { padding: 5px; background-color: white; border: 1px solid silver; }
h5.right { margin: 2px 0 2px 10px; text-align: center; font-weight: normal; float: right; }
h5.left { margin: 2px 10px 2px 0; text-align: center; font-weight: normal; float: left;	}
h5 a { text-decoration: none !important; color: #696969; }
h5 a:link { text-decoration: none !important; color: #696969;	}
h5.noborder img { border: none; }
img.noborder { border: none; }
.noborder { border: none; }`

= My empty paragraphs are being removed all the time. How can I stop this? =

For complex reasons we need to strip empty paragraphs but you can use <br /> break tags to create the same effect of empty lines. The keyboard shortcut is shift enter. We don't really recommend using either <br /> tags or empty paragraphs for formatting, you should be able to handle it in your CSS.

= How to do my own translation? =

This plugin consists of 3 parts:

* Wordpress plugin - check the languages/" directory for .po and .mo files - the standard way of how Wordpress translations work
* FCKEditor - check fckeditor/editor/lang - it contains JS files with definition of each language. Check then en.js file for entries below "Foliovision plugins" - these have to be translated. Note that vast majority of the phrases already has a translation. Set your language in plugin settings.
* SEO Images image manager - fckeditor/editor/plugins/kfm/lang/ - the missing translations are at the end of en.js file and they have to be copied into each language version. Note that vast majority of the phrases already has a translation. Set your language in plugin settings.

= Your plugin is not working, all I can see it this: =

> Parse error: syntax error, unexpected T_CONST, expecting T_OLD_FUNCTION or T_FUNCTION or T_VAR or '}' in {your web root}/wp-content/plugins/foliopress-wysiwyg/foliopress-wysiwyg-class.php on line 96

Contact your webhost to switch you to PHP 5, you are probably still running on obsolete PHP 4.

= I get a ugly message like this one: =

> Warning: mkdir() [function.mkdir]: Permission denied in /home/... ..../wp-content/plugins/foliopress-wysiwyg/fckeditor/editor/plugins/kfm/initialise.php on line 172

Make sure your /images directory has the 777 permissions. 755 can be enough too, depending on your PHP configuration (PHP Fast CGI). This directory should be located in the same place as your .htaccess file and Wordpress installation. It's referenced by your domain name.

= I get the paste as plain text dialogue box whenever I try to paste with Ctrl + V into a post. =

We are doing our best to protect your post from the bad HTML code in case you are pasting from programs like Microsoft Office or web sites.

However, if you still want to disable this dialog by default, do the following: 

1. Open this file: /wp-content/plugins/foliopress-wysiwyg/fckeditor/fckconfig.js
1. Change "FCKConfig.ForcePasteAsPlainText= true ;" to "FCKConfig.ForcePasteAsPlainText= false ;"

= How about different language versions? =

Check out the plugin settings. Please note that the image management system remains English for the moment but will also be updated to multilingual in 2010.

= I get 'Toolbar set "Foliovision" doesn't exist' error message when I edit a post. =

Since 0.9.14, this message should not appear. If you just updated you plugin, make sure you clear your browser cache properly and then reload the editing page.

= How do I get lightbox effect for images? =

You need to use a plugin like [WP Slimbox 2](http://wordpress.org/extend/plugins/wp-slimbox2/). Foliopress WYSIWYG adds rel="lightbox" to the inserted thumbnail images, so the big images open with the lightbox effect when that plugin is installed.

= How do I use ImageMagick with this plugin? =

The ImageMagick binary must be executable from PHP and be available as /bin/convert. We will add automated checks for this. ImageMagick provides superior image quality than standard PHP GD library!

== Changelog ==

= 2.6.16 =

* SEO Images removed! Due to PHP 7 compatibility issues and WordPress upgrades we are forced to remove our image management tool built on KFM. Please use Foliopress WYSIWYG 2.6.15 if you really need it.

= 2.6.15 =

* Fix for WPEngine - conflict of __autoload function name
* Fix for WP Retina 2x images showing up in SEO Images

= 2.6.14 =

* Fix for bad license information in some files

= 2.6.13 =

* Fix for WebKit browsers - FCK.InsertHtml was acting weird when inserting span tags (required for FV Flowplayer)

= 2.6.12 =

* Fix for Wordpress 4.0 - tinyMCE toolbar appearing above FCKeditor toolbar

= 2.6.11 =

* Upgrade to latest FCKEditor (2.6.11), fixing security issues with built-in spell checker (not used by our plugin)

= 2.6.8.10 =

* Fix for HTTPS websites

= 2.6.8.9 =

* Fix for bad editor size - if you upgraded Foliopress WYSIWYG and then Worpdress to 3.8, it might remove the option.

= 2.6.8.8 =

* Due to compatibility issues editor was disabled for IE 10.0 and IE 11.0 and notice about using different browser is displayed.
* Added Serbian language translation. Thank you goes to http://www.webhostinghub.com !

= 2.6.8.7 =

* Important fix for featured image function

= 2.6.8.6 =

* Added support for translations for the Wordpress part
* Added missing language hooks for editor and image manager

= 2.6.8.5 =

* Fix for XSS vulnerability in unused part of plugin

= 2.6.8.4 =

* Fix for FV Flowplayer button
* Fix for editor height when upgrading Wordpress to 3.7 version

= 2.6.8.3 =

* Fix for new FV Flowplayer button
* Fix for some PHP warnings

= 2.6.8.2 =

* Bugfix for setting of featured image
* Bugfix for editor CSS loader (it was throwing PHP warnings)
* Bugfix for custom post types without editor support
* Bugfix for image size limit on upload

= 2.6.8.1 =

* Bugfix for editor size setting. New Wordpress goes not have a setting for number of editor rows anymore, so we have it in plugin settings now (Height of FCKEditor). 

= 2.6.8 =

* FCKeditor component upgraded to latest version
* Version number changed to match FCKeditor
* Ctrl + right click gets you spelling suggestions if you have browser spellchecker installed 
* Security enhancements

= 0.9.22 =
* Wordpress 3.3 display issues fix
* Featured Image functionality bugfix

= 0.9.21 =
* quick bug fix release for formating drop down

= 0.9.20 =
* quick edit bug fix (it was removing the plain text editing option)
* new button for easy insertion of embed codes (Youtube, Vimeo etc.)
* other bugfixes

= 0.9.19.7 =
* SEO Images autoload bugfix (thanks goes to Edrard!)

= 0.9.19.6 =
* bugfix for ImageMagick detection when using open_basedir restrictions.

= 0.9.19.5 =
* editor is for now disabled in IE 9.0 as both FCKEditor and CKEditor are having some issues
* WP Link Dialog is now optional - check our Advanced settings.

= 0.9.19 =
* Wordpress featured image support added! (beta)
* Support for Impact plugin templates
* New advanced options - custom field support and image HTML template
* Wordpress Link to post feature added

= 0.9.18 =
* Safari Toolbar disappearing bug fix
* Package optimization (contains less files)
* Other bug fixes

= 0.9.17 =
* Bug fixes

= 0.9.16 =
* Wordpress Media Uploader compatibility fixes

= 0.9.15 =
* Bug fixes
* 'There is a problem opening your configuration file' warning message removed

= 0.9.14 =
* Bug fixes
* custom-config configuration file replaced by inline JS - less trouble with compatibility issues

= 0.9.13 =
* Added language support
* Wordpress caption support
* Autosave glitch fixed
* Image uploader permissions are now configurable

= 0.9.12 =
* works with Wordpress 3.0
* working Word count 
* Flash/no Flash uploader option fixed

= 0.9.11 =
* Wordpress autosave support
* better Wordpress MU support
* HTML entities are not processed by default - keeping your accented characters unchanged

= 0.9.10 =
* Image management tool is now using new version of KFM which works with Safari
* Image management tool now allows multiple file uploads via built-in Flash uploader
* Plain text editing option for posts
* Wpautop and wptexturize are disabled on posts edited with Foliopress WYSIWYG - makes sure your posts have the cleanest and untouched HTML possible

= 0.9.8 =
* WYSIWYG style configuration now resides in plugin options - easier configuration
* Image management tool now appears with the right year/month/ directory opened
* All uploaded images above certain height and width (check out plugin options) are sized down to fit into it
* Works on sites with secured wp-config.
* Insert FV Wordpress Flowplayer button added
* Pasting dialog receives focus when it appears
* Dreamhost JSON glitch fixed

= 0.9.7 =
* Easy Toolbar customization
* Easy Formating dropdown customization

= 0.9.6 =
* Multiple image posting
* No need to edit any configuration files
* Available thumbnail sizes are limited by the size of the picture
* Better security
* Automatic wpautop can be turned off

= 0.9.5 =
* Safari editor window height issue fixed
* Firefox spellchecker enabled by default

= 0.9.4 =
* Blockquote button added

= 0.9.3 =
* Introducing the Paste Rich Text Mode button to override standard paste dialog in Firefox and Safari. This lets you select between plain/formated text pasting.
* Automatic wpautop

= 0.9.2 =
* Foliopress WYSIWYG now works on secure https sites.

= 0.9.1 =
* Bug fixes, new option to hide Wordpress Uploader Buttons.

= 0.9 =
* SEO Images are now compatible with FTP uploaded files. Read a manual on how to upload files and prepare them on handling with SEO Images.

== Upgrade Notice ==

= 2.6.16
* SEO Images removed! Due to PHP 7 compatibility issues and WordPress upgrades we are forced to remove our image management tool built on KFM. Please use Foliopress WYSIWYG 2.6.15 if you really need it.

= 2.6.8 =
* We decided to go back to FCKeditor (latest version - 2.6.8), as the new CKeditor was bringing too many new issues.

= 0.9.22 =
* IE9 still won't work, but try switching it to IE8 compatibility mode. If the upgrade takes too long, you might need to do the manual upgrade or first delete the plugin and then install it through Wordpress. Make sure you clear your browser cache after upgrade.

= 0.9.19.7 =
* Editor disabled in IE 9.0 due to compatibility issues, until it gets resolved. If Wordpress prompts for FTP information and the upgrade takes too long, you might need to do the manual upgrade or first delete the plugin and then install it through Wordpress. Please make sure you clear your browser cache after upgrade.

= 0.9.19.5 =
* This is a bugfix release for IE 9.0 users and Link Dialog issues. If Wordpress prompts for FTP information and the upgrade takes too long, you might need to do the manual upgrade or first delete the plugin and then install it through Wordpress. Please make sure you clear your browser cache after upgrade.

= 0.9.19 =
* New - Featured Image support (beta), Link to post feature, Impact support. If Wordpress prompts for FTP information and the upgrade takes too long, you might need to do the manual upgrade or first delete the plugin and then install it through Wordpress. Please make sure you clear your browser cache after upgrade.

= 0.9.18 =
* If Wordpress prompts for FTP information and the upgrade takes too long, you might need to do the manual upgrade or first delete the plugin and then install it through Wordpress. Please make sure you clear your browser cache after upgrade.

== Other Notes ==

= Featured image feature =

Since version 0.9.19 there is a support for native Wordpress Feature Image function. Wordpress Uploads folder has to be set to the same path as Foliopress WYSIWYG, otherwise it won't be displayed.
