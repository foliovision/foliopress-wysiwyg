<?php require_once( dirname( __FILE__ ) . '/../foliopress-wysiwyg-class.php' ); ?>

kfm_userfiles: <?php print( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_IMAGES] ); ?>;
kfm_userfiles_output: <?php print( $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_IMAGES] ); ?>;
kfm_special_thumbs_sizes: <?php print( $fp_wysiwyg->getThumbsString() ); ?>;
kfm_return_image_link: <?php print( $fp_wysiwyg->getLink() ); ?>;
kfm_link_lightbox: <?php print( $fp_wysiwyg->getLightbox() ); ?>;
iJPGQuality: <?php echo $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_JPEG]; ?>;
bTransformTrueColorToPalette: <?php echo $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_PNG] ? 1 : 0; ?>;
iTrueColorToPaletteLimit: <?php echo $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_PNG_LIMIT]; ?>;
bMultipleImagePosting: <?php echo $fp_wysiwyg->aOptions['multipleimageposting']; ?>;
iMaxWidth: <?php echo $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_MAXW]; ?>;
iMaxHeight: <?php echo $fp_wysiwyg->aOptions[fp_wysiwyg_class::FVC_MAXH]; ?>;
