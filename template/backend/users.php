<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty( $users ) ): ?>
	<input type="text" class="wpeo-name-user" data-list=".wpeo-users-list" autocomplete="off" placeholder="<?php _e( 'The username', 'wpeotasks-i18n' ); ?>" />
	<form id="form-users" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="post">
		<input type="hidden" name="action" value="wpeo-update-users" />
		<input type="hidden" class="wpeo-type-thickbox" name="type" value="<?php echo $_GET['type']; ?>" />
		<input type="hidden" name="task_id" class="wpeo-task-id" value="<?php echo !empty($_GET['task_id']) ? $_GET['task_id'] : null; ?>" />
		<input type="hidden" name="point_id" class="wpeo-users-id" value="<?php echo !empty($_GET['point_id']) ? $_GET['point_id'] : null; ?>" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'nonce_update_users' ); ?>" />
		<ul class="wpeo-users-list">
		<?php foreach( $users as $user ): ?>
			<li>
				<label for="user_<?php echo $user->ID; ?>">
				<?php echo $user->avatar; ?>
				<input name="users[]" value="<?php echo $user->ID; ?>" type="checkbox" id="user_<?php echo $user->ID; ?>" <?php if( in_array( $user->ID, is_array( $users_in_task ) ? $users_in_task : array() ) ): ?>checked='checked' <?php endif; ?> />
				<?php echo $user->user_login; ?></label>
			</li>			
		<?php endforeach; ?>
		</ul>
		<div class="wpeo-task-submit-button">
			<input type='submit' class='button-primary alignright' value="<?php _e( 'Ok', 'wpeotasks-i18n'); ?>" id='wpeo-btn-ok-users' />
		</div>
	</form>
<?php endif; ?>

<script type="text/javascript">
jQuery('.wpeo-name-user').hideseek();
</script>