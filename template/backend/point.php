<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<li class="point-<?php echo $point->comment_ID; ?> wpeo-task-point" data-id="<?php echo $point->comment_ID; ?>" >
	<ul>
		<li>
			<?php if ( empty ( $point->informations->done ) && $task->informations->user_can_write ) : ?>
				<span class="dashicons dashicons-menu" title="<?php _e( 'Drag and drop for set the order', 'wpeotasks-i18n' ); ?>"></span>
			<?php endif; ?>
			<input tabindex="-1" type="checkbox" <?php echo (!$task->informations->user_can_write) ? 'disabled="disabled"' : ''; ?> data-nonce="<?php echo wp_create_nonce( 'wp_nonce_done' ); ?>" class="wpeo-done-point" <?php echo ( !empty( $point->informations->done ) ) ? 'checked="checked"' : ''; ?> />
			<span class="wpeo-block-id wpeo-point-color<?php echo ( !empty( $point->informations->done ) ) ? '-done' : ''; ?>"><?php echo $point->comment_ID; ?></span>
		</li>
		<li class="wpeo-point-input">
			<textarea data-nonce="<?php echo wp_create_nonce( 'wp_nonce_update_point' ); ?>" data-id="<?php echo $point->comment_ID; ?>" name="points[<?php echo $point->comment_ID; ?>]" <?php echo (!$task->informations->user_can_write) ? 'disabled' : ''; ?> <?php if( !empty( $point->informations->done ) ) : ?>class="wp-input-disabled" disabled="disabled"<?php else: ?>class="wpeo-point-can-update"<?php endif; ?>><?php echo $point->comment_content; ?></textarea>
		</li>
		<li>
			<?php if( empty( $point->informations->done ) && $task->informations->user_can_write ) : ?>
				<a tabindex="-1" href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=wpeo-view-time-point&task_id=' . $task->ID . '&point_id=' . $point->comment_ID, 'wp_nonce_view_time' ); ?>" title="Task #<?php echo $task->ID; ?> -> Point #<?php echo $point->comment_ID . ' : ' . __( 'Add time', 'wpeotasks-i18n' ); ?>" class="my-thickbox wpeo-time-in-point"><?php echo $point->informations->comment_time; ?></a>
			<?php else: ?>
				<?php echo $point->informations->comment_time; ?>
			<?php endif;?>		
			
			<?php if( class_exists( '_wpeo_user_ctr' ) ) :?>
				<a tabindex="-1" href="#"  data-type="point" data-id="<?php echo $point->comment_ID; ?>" class="dashicons dashicons-admin-users wpeo-user-add"></a>	
			<?php endif; ?>
			
			<?php if( empty( $point->informations->done ) && $task->informations->user_can_write ) : ?>
					<i tabindex="-1" data-id="<?php echo $point->comment_ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_point_trash' ); ?>" title="Move to trash" class="dashicons dashicons-trash wpeo-send-point-to-trash"></i>
			<?php endif; ?>
		</li>
	</ul>
</li>

<?php if( shortcode_exists( 'wpeouser' ) ) :
	echo do_shortcode( '[wpeouser id="' . $point->comment_ID . '" use_dashicons="false" type="comment" ]' );
endif; ?>