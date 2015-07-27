<div class="wpeo-notification wpeo-notification-<?php echo $_POST['type']; ?> animated fadeInLeft">
	<span class="icon-notification">
		<i class="dashicons dashicons-<?php echo $_POST['dashicons']; ?>"></i>
	</span>
	<p>
		<?php echo $message; ?> 
	</p>
	<?php if( ! empty( $method ) ):?>
	<a class="wpeo-notification-cancel">
		<?php _e( 'Cancel', 'wpeotasks-i18n' ); ?>
	</a>
	<?php endif; ?>
	<a>
		<span class="dashicons dashicons-no"></span>
	</a>
</div>