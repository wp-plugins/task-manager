<input type="hidden" class="wpeo-files-associated-element-id" value="<?php echo $params[ 'id' ]; ?>" />
<ul class="wpeomtm-attached-file-container" >
	<?php if ( !empty( $files ) ) : ?>
			<?php foreach ( $files as $file_id ) : ?>
				<?php
	 			$post = get_post( $file_id );
				?>
				<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_FILES_DIR, WPEOMTM_FILES_TEMPLATES_MAIN_DIR, "backend", "list", "render" ) ); ?>		
			<?php endforeach; ?>
	<?php else : ?>
		<li class="wpeomtm-no-result-msg" ><?php _e( 'No files affected', 'wpeo-files-i18n' ); ?></li>
	<?php endif; ?>
</ul>