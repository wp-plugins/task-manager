<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<form id="form-tags" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
	<input type="hidden" name="action" value="update-tags" />
	<input type="hidden" name="task_id" class="wpeo-task-id" value="<?php echo !empty($_GET['task_id']) ? $_GET['task_id'] : null; ?>" />
	<input type="text" name="name_tag" class="wpeo-name-tag" data-list=".wpeo-tags-list" autocomplete="off" placeholder="<?php _e( 'The tag name', 'wpeotasks-i18n' ); ?>" />
	<input type="button" data-nonce="<?php echo wp_create_nonce( 'nonce_create_tag' ); ?>" class="button-primary alignright wpeo-create-tag" value="<?php _e( "Create tag", "wpeotask-i18n"); ?>" />
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'nonce_update_tags' ); ?>" />
	
	<?php if( !empty ( $tags ) ) : ?>
		<ul class="wpeo-tags-list">
		<?php foreach( $tags as $tag ) : ?>
			<li>
				<label for="tags_<?php echo $tag->term_id; ?>">
				<input name="tags[]" value="<?php echo $tag->slug; ?>" type="checkbox" id="tags_<?php echo $tag->term_id; ?>" <?php if( in_array( $tag->slug, is_array( $tags_in_task ) ? $tags_in_task : array() ) ): ?>checked='checked' <?php endif; ?> />
				<?php echo $tag->name; ?></label>
			</li>			
		<?php endforeach; ?>
		</ul>
		
	<?php endif; ?>
	<div class="wpeo-task-submit-button">
		<input type='submit' class='button-primary alignright' value="<?php _e( 'Ok', 'wpeotasks-i18n'); ?>" id='wpeo-btn-ok-tags' />
	</div>
</form>

<script type="text/javascript">
jQuery('.wpeo-name-tag').hideseek();
</script>