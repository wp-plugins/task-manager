<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wpeo-task-time-history">
	<h5><?php _e("Time history", "wpeotask-i18n"); ?></h5>
	<ul class="wpeo-task-list-point-comments">
		<?php 
		if( !empty( $comments ) ):
			foreach( $comments as $comment ):
				?>
				<li class="wpeo-point-comment-<?php echo $comment->comment_ID; ?>" title="<?php echo $comment->comment_author . ' ' . $comment->comment_author_email; ?>">
					<?php 
						echo get_avatar($comment->user_id, 24); ?> <?php comment_date( get_option('date_format') . ' ' . get_option('time_format'), $comment->comment_ID ); 
						echo '<span> ' . $comment->comment_time; ?> |</span> <?php echo $comment->comment_content; 
						?>
						<i data-id="<?php echo $comment->comment_ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_comment_trash' ); ?>" title="Move to trash" class="dashicons dashicons-trash wpeo-send-comment-to-trash"></i>
				</li>
				<?php 
			endforeach;
		endif;
		?>
	</ul>
</div>
