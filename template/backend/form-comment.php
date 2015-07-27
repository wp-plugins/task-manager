<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="POST" id="wpeo-time-manager-form" >
    <!-- For admin-ajax -->
    <input type="hidden" name="action" value="wpeo-save-time-point" />
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wp_nonce_add_time' ); ?>" />
    <input type="hidden" class="wpeo-point-id" name="point_id" value="<?php echo $_GET['point_id']; ?>" />
    <input type="hidden" name="task_id" class="wpeo-point-task-id" value="<?php echo $_GET['task_id']; ?>" />
    
    <p>
      <label><?php _e( 'Date', 'wpeotasks-i18n' ); ?></label>
    	<input type="text" style="width: 100px;" placeholder="<?php _e( 'YYYY-MM-DD', 'wpeotasks-i18n' ); ?>" class="isDate" value="<?php echo date( 'Y-m-d' ); ?>" readonly="readonly" >
    	<span class="dashicons dashicons-calendar-alt wpeomtm-current-date-shortcut" title="<?php _e( 'Get the current date', 'wpeotasks-i18n' ); ?>"></span>
    </p>
  	<p>
      <label><?php _e( 'Minute', 'wpeotasks-i18n' ); ?></label>
    	<input type="text" name="minute" placeholder="<?php _e( 'MM', 'wpeoaction-i18n' ); ?>" value="15" />
    </p>
    
    <p>
      <label><?php _e( 'Comment', 'wpeotasks-i18n' ); ?></label>
      <textarea name="message" class="wpeo-point-comments" rows="4" placeholder="<?php _e( 'Write your comment here...', 'wpeotasks-i18n' );?>"></textarea>
    </p>
    
    <p>
    	<input type="submit" id="wpeo-btn-add-time" class="button-primary" value="<?php _e("Ok", "wpeotasks-i18n"); ?>" />
    </p>
</form>

<script type="text/javascript">
/** Date picker */
jQuery('.isDate').datepicker({
	dateFormat: 'yy-mm-dd',
});
</script>