<div class="wrap">
	<h2>Recreation of thumbnails</h2>
	<p>Thumbnails that will be affected</p>
	<ol>
		<li>Special thumbnails that have the original file still present</li>
		<li>Preview thumbnails will be deleted so SEO Images will recreate them automatically</li> 
	</ol>
	<p>Images directory: '<?php echo realpath( $_SERVER['DOCUMENT_ROOT'].$this->aOptions[self::FVC_IMAGES] ); ?>'</p>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<ul>
			<?php
				set_time_limit( 300 );
				require( $strPath.'/fckeditor/editor/plugins/kfm/cleanup.php' );
				//echo '<li>'.$kfm_hidden_panels.'</li>';
				//echo '<li>'.var_export( $kfm_special_thumbs_sizes, true ).'</li>';

				$aSetup = array();
				$aSetup['JPGQuality'] = $this->aOptions[self::FVC_JPEG];
				$aSetup['transform'] = $this->aOptions[self::FVC_PNG];
				$aSetup['transform_limit'] = $this->aOptions[self::FVC_PNG_LIMIT];

				$fStart = microtime( true );
				KFM_ReadRecreatableThumbnails( realpath( $_SERVER['DOCUMENT_ROOT'].$this->aOptions[self::FVC_IMAGES] ), $this->aOptions[self::FVC_KFM_THUMBS], $kfm_workdirectory, $aSetup );
				$fEnd = microtime( true );
			?>
		</ul>
		<p>Time needed to generate this info: <?php echo intval( $fEnd - $fStart ); ?> seconds</p>
		<p style="text-align: right;">
			<input style="width: 130px; height: 2em;" type="submit" name="recreate_submit" value="Recreate" /> | 
			<input style="width: 130px; height: 2em;" type="submit" name="recreate_cancel" value="Cancel" />
		</p>
	</form>
</div>