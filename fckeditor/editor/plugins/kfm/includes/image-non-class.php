<?php

/**
 * Checks if image has some colors with semi-alpha value.
 *
 * Checks alpha value of color. If this value is not 0 (oblique) or 127(transparent) it returns
 * true upon finding the first of such colors, or it goes through all pixels in image and returns
 * false. Order of going through pixels is by columns from 0.th column to last column. <b>This 
 * method works only with TrueColor images.</b>
 *
 * @param pointer &$img Pointer to image resource
 * @return bool True if TrueColor image is complex (e.g. contains semi-alpha color), false otherwise
 */
function FV_IsComplex( &$img, $aImageInfo ){
	if( !imageistruecolor( $img ) ) return false;
	
	for( $i=0; $i<$aImageInfo[0]; $i++ ){
		for( $j=0; $j<$aImageInfo[1]; $j++ ){
			$iColor = imagecolorat( $img, $i, $j );
			$iAlpha = ($iColor & 0x7F000000) >> 24;
			if( 0 != $iAlpha && 127 != $iAlpha ) return true;
		}
	}
	
	return false;
}

/**
 * Counts number of unique colors and gets position of first transparent color.
 *
 * Counts number of unique colors used in this image. If $bTransparency is set to 'true' then
 * this function also tries to get first transparent color (alpha = 127) and record it position.
 * Order of going through pixels is by columns from 0.th to last. <b>This method works only
 * with TrueColor images.</b>
 *
 * @param pointer &$img Pointer to image resource
 * @param integer $iWidth Width of area in which to search and count
 * @param integer $iHeight Height of area in which to search and count
 * @param boolean $bTransparency If true, then method also tries to get position of first transparency color.
 * @return array|boolean False if inserted image is not TrueColor, or is damaged in another way. Array keys:
 * - colors : Number of unique colors used in image found
 * - transparent : Array with 'x' and 'y' as keys determining position of pixel with fully transparent color	 	 
 */
function FV_CountUniqueColors( &$img, $iWidth, $iHeight, $bTransparency = false ){
   if( !imageistruecolor( $img ) ) return false;
   
   $aColors = array();
   $aTrans = array();
   $aTrans['position'] = '';
   
   $iMemlimit = str_replace('M','',ini_get('memory_limit') )*1048576;
   $iFreememBegin = ($iMemlimit - memory_get_usage());
   for( $i=0; $i<$iWidth; $i++ ){
      $freemem = ($iMemlimit - memory_get_usage());
      //  if more than 50% of the picture was checked and the process took more than half of free memory, we can predict, that it won't end happy
      if( $i > $iWidth/2 && $freemem < $iFreememBegin/2 )  {
         echo "Running low with memory, skipping color resize optimization. Picture will be stored as true color PNG. ";
         return array( 'colors' => count( $aColors ), 'transparent' => $aTrans['position'] );
      }  
      for( $j=0; $j<$iHeight; $j++ ){
         $iColor = imagecolorat( $img, $i, $j );
         if( isset( $aColors[$iColor] ) ) $aColors[$iColor] += 1;
         else $aColors[$iColor] = 1;
         
         if( $bTransparency ){
            $iAlpha = ($iColor & 0x7F000000) >> 24;
            if( 127 == $iAlpha ){
              $aTrans['position'] = array( 'x' => $i, 'y' => $j );
              $bTransparency = false;
            }
         }
      }
   }	
   return array( 'colors' => count( $aColors ), 'transparent' => $aTrans['position'] );
}

/**
 * Atempts to converts TrueColor image to Indexed image
 *
 * The conversion is done by GD function imagetruecolortopalette. In indexed image maximum of 255
 * colors will be used. If Second parameter ($aColorInfo) is suplied, function will atempt
 * to preserve transparency.
 *
 * @param pointer &$imgSource Pointer to image resource
 * @param array $aColorInfo Array that contains information about image {@link kfmImage::CountUniqueColors returned by CountUniqueColors}
 */
function FV_TrueColorToIndexed( &$imgSource, $aColorInfo = NULL ){
	imagetruecolortopalette( $imgSource, true, 255 );
	
	if( is_array( $aColorInfo ) && isset( $aColorInfo['transparent'] ) && $aColorInfo['transparent'] ){
		$aPosition = $aColorInfo['transparent'];
		$iColorIndex = imagecolorat( $imgSource, $aPosition['x'], $aPosition['y'] );
		imagecolortransparent( $imgSource, $iColorIndex );
	}
}

/**
 * Creates resampled copy if PNG image in destination
 *
 * This method resaples the image to new size, if set globaly, will try to index-color
 * the image and saves it to suplied destination. Indexation of colors is done by
 * {@link kfmImage::TrueColorToIndexed TrueColorToIndexed} and only if PNG is not
 * {@link kfmImage::IsComplex Complex} and doesn't have to much unique colors. If PNG already
 * is with indexed colors, it will stay that way.
 *
 * @global bool Used as settings if KFM shoud atempt to convert TrueColor images to Indexed images
 * @global integer Used as settings which tells KFM the limit of unique colors in image, from which KFM should not convert TrueColor image
 * @param string $strTo Destination path, where to save created image
 * @param integer $iDestWidth Width of destination image
 * @param integer $iDestHeight Height of destination image
 */
function FV_CreateResizedCopyPNG( $strSource, $strTo, $iDestWidth, $iDestHeight, $aImageInfo, $aOptions = array() ){
	global $bTransformTrueColorToPalette, $iTrueColorToPaletteLimit;
	if( !isset( $bTransformTrueColorToPalette ) ) $bTransformTrueColorToPalette = true;
	if( !isset( $iTrueColorToPaletteLimit ) ) $iTrueColorToPaletteLimit = 5000;
	
	if( isset( $aOptions['transform'] ) ) $bTransformTrueColorToPalette = $aOptions['transform'];
	if( isset( $aOptions['transform_limit'] ) ) $iTrueColorToPaletteLimit = $aOptions['transform_limit'];
	
	$imgSource = imagecreatefrompng( $strSource );
	$bIsTrueColor = imageistruecolor( $imgSource );
	
	if( false /*!$bIsTrueColor*/ ){
		$imgDest = imagecreate( $iDestWidth, $iDestHeight );
		
		imagepalettecopy( $imgDest, $imgSource );
		$iColorTransparent = imagecolortransparent( $imgSource );
		imagecolortransparent( $imgDest, $iColorTransparent );
		imagefill( $imgDest, 0, 0, $iColorTransparent );
		
		imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $aImageInfo[0], $aImageInfo[1] );
	}else{
		$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );
		
		imagealphablending( $imgDest, false );
		imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $aImageInfo[0], $aImageInfo[1] );
		imagesavealpha( $imgDest, true );
		
      if( $bTransformTrueColorToPalette ){
			$aColors = FV_CountUniqueColors( $imgDest, $iDestWidth, $iDestHeight, true );
			
			if( $aColors && $iTrueColorToPaletteLimit > $aColors['colors'] ){
				$bComplex = FV_IsComplex( $imgSource, $aImageInfo );
				
				if( !$bComplex ) FV_TrueColorToIndexed( $imgDest, $aColors );
			}
		}
	}
	
	imagepng( $imgDest, $strTo, 9 );
	
	imagedestroy( $imgSource );
	imagedestroy( $imgDest );
}

/**
 * Creates resampled copy of GIF image in destination
 *
 * This function is specialy designed to handle resampling (resizing) of GIF images, since
 * original KFM wasn't able to properly do this. This function maintains transparency and
 * palette that has been used.
 *
 * @param string $strTo Destination path, where to save created image
 * @param integer $iDestWidth Width of destination image
 * @param integer $iDestHeight Height of destination image
 */
function FV_CreateResizedCopyGIF( $strSource, $strTo, $iDestWidth, $iDestHeight, $aImageInfo ){
	$imgSource = imagecreatefromgif( $strSource );
	//$imgDest = imagecreate( $iDestWidth, $iDestHeight );
	$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );
		
	//imagepalettecopy( $imgDest, $imgSource );
	//$iColorTransparent = imagecolortransparent( $imgSource );
	//imagecolortransparent( $imgDest, $iColorTransparent );
	//imagefill( $imgDest, 0, 0, $iColorTransparent );
	
	imagealphablending( $imgDest, false );
	imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $aImageInfo[0], $aImageInfo[1] );
	imagesavealpha( $imgDest, true );
	
	imagegif( $imgDest, $strTo, 9 );
	
	imagedestroy( $imgSource );
	imagedestroy( $imgDest );
}

function FV_CreateResizedCopy( $strSource, $strTo, $iDestWidth, $iDestHeight, $aImageInfo = '', $iJPGQuality = 95, $aOptions = array() ){
	$iJPGQuality = intval( $iJPGQuality );
	if( $iJPGQuality < 1 || $iJPGQuality > 100 ) $iJPGQuality = 95; 	
   if( !$aImageInfo ) $aImageInfo = getimagesize( $strSource );
	
	$strType = substr( $aImageInfo['mime'], 6 );
	$strFunctionLoad = 'imagecreatefrom' . $strType;
	$strFunctionSave = 'image' . $strType;
	
	if( !function_exists( $strFunctionLoad ) || !function_exists( $strFunctionSave ) )
		return 'server cannot handle image of type "' . $strType . '"';
	
	if( 'png' == $strType ){
		FV_CreateResizedCopyPNG( $strSource, $strTo, $iDestWidth, $iDestHeight, $aImageInfo, $aOptions );
		return true;
	}
	
	if( 'gif' == $strType ){
		FV_CreateResizedCopyGIF( $strSource, $strTo, $iDestWidth, $iDestHeight, $aImageInfo );
		return true;
	}
	
	$imgSource = $strFunctionLoad( $strSource );
	$imgDest = imagecreatetruecolor( $iDestWidth, $iDestHeight );
	
	imagealphablending( $imgDest, false );
	imagecopyresampled( $imgDest, $imgSource, 0, 0, 0, 0, $iDestWidth, $iDestHeight, $aImageInfo[0], $aImageInfo[1] );
	imagesavealpha( $imgDest, true );
	
	$strFunctionSave( $imgDest, $strTo, ( $strType == 'jpeg' ? $iJPGQuality : 9 ) );
	
	imagedestroy( $imgDest );
	imagedestroy( $imgSource );
	
	return true;
}


function FV_RecreateSpecialThumb( $iSize, $strImagePath, $strImageDestPath, $aOptions = array() ){
	global $kfm_use_imagemagick, $rootdir, $iJPGQuality;
	
	if( isset( $aOptions['JPGQuality'] ) ) $iJPGQuality = $aOptions['JPGQuality'];
	
	$iWidth = $iSize;
	$iHeight = $iSize;
	
	$aImageInfo = getimagesize( $strImagePath );
	
	$ratio = $iWidth / $aImageInfo[0];
	$iThumbWidth = $aImageInfo[0] * $ratio;
	$iThumbHeight = $aImageInfo[1] * $ratio;
	 
	return FV_CreateResizedCopy( $strImagePath, $strImageDestPath, $iThumbWidth, $iThumbHeight, $aImageInfo, $iJPGQuality, $aOptions );
}
?>