<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<form id="form-setting" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
	<input type="hidden" name="action" value="wpeo-update-setting" />
	<input type="hidden" name="task_id" class="wpeo-task-id" value="<?php echo !empty($_GET['task_id']) ? $_GET['task_id'] : null; ?>" />
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'nonce_update_setting' ); ?>" />
	
	<h3><?php _e( 'Display the data in your theme', 'wpeotasks-i18n' ); ?></h3>	
	<label for="wpeo-setting-display-user">
		<?php _e( 'Display users : ', 'wpeotasks-i18n' );?>
		<input name="user" id="wpeo-setting-display-user" type="checkbox" <?php echo $meta_setting['user'] ? 'checked' : ''; ?> />
	</label>
	
	<label for="wpeo-setting-display-time">
		<?php _e( 'Display time : ', 'wpeotasks-i18n' );?>
		<input name="time" id="wpeo-setting-display-time" type="checkbox" <?php echo $meta_setting['time'] ? 'checked' : ''; ?> />
	</label>
	
	<div class="wpeo-task-submit-button">
		<input type='submit' class='button-primary alignright' value="<?php _e( 'Ok', 'wpeotasks-i18n'); ?>" id='wpeo-btn-ok-setting' />
	</div>

</form>