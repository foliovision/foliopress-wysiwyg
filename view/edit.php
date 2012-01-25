<div class="wrap">
	<h2>Editing file: &#39;<?php echo basename( $strFile ); ?>&#39;</h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<p style="margin-right: 10px;"><textarea style="width: 100%; height: 800px;" name="textFile"><?php echo htmlspecialchars( file_get_contents( $strFile ) ); ?></textarea></p>
		<p style="text-align: right;">
			<input style="width: 130px; height: 2em;" type="submit" name="save_file" value="Save" /> | 
			<input style="width: 130px; height: 2em;" type="submit" name="cancel_file" value="Cancel" />
		</p>
	</form>
</div>