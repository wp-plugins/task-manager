<li class="<?php echo !empty($_GET['account_dashboard_part']) && $_GET['account_dashboard_part'] == 'my-task' ? 'wps-activ' : ''; ?>">
	<a data-target="menu1" href='?account_dashboard_part=my-task'>
		<i class="dashicons dashicons-format-status"></i>
		<span><?php _e( 'My task', 'wpeotasks-i18n' ); ?></span>
	</a>
</li>
