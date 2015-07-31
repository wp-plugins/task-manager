<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<li class="wpeo-add-point">
	<ul>
		<li></li>
		<li class="wpeo-point-input">
			<textarea <?php echo (!$task->informations->user_can_write) ? 'disabled' : ''; ?> class="add-point" name="comment[]" placeholder="<?php _e( 'Write your point here...', 'wpeotasks-i18n' ); ?>"></textarea>
		</li>
		<li>
			<div class="wpeo-tasks-add-new-point" title="<?php _e( 'Add this point', 'wpeotasks-i18n' ); ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_add_point' ); ?>">
				<i class="dashicons dashicons-plus-alt"></i>
			</div>
		</li>
	</ul>

</li>