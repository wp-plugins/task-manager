<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty($task) && !empty( $task->ID ) ): ?>
	<?php if( !empty ( $task->new ) ):?>
		<div class="grid-item wpeo-project-task-<?php echo $task->ID; ?> <?php echo $task->informations->class_tags; ?> <?php echo $task->informations->my_task; ?>">
	<?php endif; ?>

		<div class="wpeo-project-task">
			<span class="task-marker"></span>
			<p class="wpeo-task-project-error"></p>

			<?php  if( !$task->informations->user_can_write ) : ?>
				<div data-mode="<?php echo ($task->user_can_write) ? 'w' : 'r'; ?>" data-nonce="<?php echo wp_create_nonce( 'wp_nonce_change_user_write' ); ?>" class="wpeo-task-mask">
					<span class="wpeo-project-task-change-user-write dashicons dashicons-edit" title="<?php _e( 'Switch to write mode', 'wpeotasks-i18n' ); ?>" ></span>
				</div>
			<?php endif; ?>

			<!-- For refresh task -->
			<input type="hidden" class="wpeo-project-task-nonce" value="<?php echo wp_create_nonce( 'wp_nonce_refresh_task' ); ?>" />

			<!--  Header  -->
			<ul class="wpeo-task-header">
				<li class="wpeo-task-id">#<?php echo $task->ID . ' -'; ?></li>
				<li class="wpeo-task-title">
					<?php if( $task->informations->user_can_write ) : ?>
						<input data-nonce="<?php echo wp_create_nonce( 'wp_nonce_title' ); ?>" type="text" class="wpeo-project-task-title" value="<?php echo htmlspecialchars($task->post_title); ?>" />
					<?php else: ?>
						<?php echo $task->post_title; ?>
					<?php endif; ?>
				</li>
				<li class="wpeo-task-time">
					<span class="dashicons dashicons-clock"></span> 
					<span class="wpeo-project-task-time"><?php echo $task->informations->time; ?></span> /
					<input data-nonce="<?php echo wp_create_nonce( 'wp_nonce_time_estimated' ); ?>" class="wpeo-project-task-time-estimated" type="text" value="<?php echo $task->informations->estimated_time; ?>" />
					<span class="dashicons dashicons-menu wpeo-task-additional-buttons"></span>
					<ul class="wpeo-bloc-additional-buttons">
						<li>
							<i class="dashicons dashicons-admin-settings"></i> 
							<a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=wpeo-view-setting&task_id=' . $task->ID, 'nonce_setting' ); ?>" title="Task #<?php echo $task->ID . ' : ' . __( 'Settings', 'wpeotasks-i18n' ); ?>" class="my-thickbox wpeo-task-settings"><?php _e( 'Settings', 'wpeotasks-i18n' );?></a>
						</li>
						<a download="Export.txt" class="wpeo-download-file" href="#"></a>
							<?php 
							$file_filter = '';
							
							if( has_filter( 'wpeo_tasks_file_filter' ) )
								$file_filter = apply_filters( 'wpeo_tasks_file_filter', $file_filter, $task->ID ); 
							
							echo $file_filter;
							?>
						<li class="wpeo-export" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_export' ); ?>"  title="<?php _e( 'Export', 'wpeotasks-i18n' ); ?>">
							<i class="dashicons dashicons-media-text"></i> <?php _e( 'Export', 'wpeotasks-i18n' ); ?>
						</li>
						<li class="wpeo-send-task-to-trash"  title="<?php _e( 'Move to trash', 'wpeotasks-i18n'); ?>" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_trash' ); ?>" ><i class="dashicons dashicons-trash"></i>  <?php _e( 'Trash', 'wpeotasks-i18n' ); ?></li>	
					</ul>
				</li>
			
				
			</ul>
			
			<!-- Points -->
			<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'form', 'points' ) ); ?>

			<!-- Footer -->
			<div class="wpeo-task-footer">				
				<ul class="wpeo-project-task-categories">
					<?php if( !empty( $task->informations->tags ) ) :?>
						<?php foreach( $task->informations->tags as $tag ) :?>
							<li><?php echo $tag; ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
					<li class="wpeo-force-alignright"><a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=view-tag&task_id=' . $task->ID, 'wp_nonce_view_tag' ); ?>" title="Task #<?php echo $task->ID . ' : ' . __( 'Tags', 'wpeotasks-i18n' ); ?>" class="my-thickbox dashicons dashicons-category"></a></li>
				</ul>

				<ul class="wpeo-project-task-users">
					<?php if( !empty( $task->informations->users ) ) :?>
						<?php foreach( $task->informations->users as $user ) :?>
							<li title="<?php echo $user->ID . ' - ' . $user->user_login . ' ' . $user->user_email; ?>" class="wpeo-user-<?php echo $user->ID; ?>"><?php echo get_avatar($user->ID, 24); ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
					<li class="wpeo-force-alignright"><a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php' ) . '?action=view-user&type=task&task_id=' . $task->ID, 'view_user' ); ?>" title="Task #<?php echo $task->ID . ' : ' . __( 'Users', 'wpeotasks-i18n' ); ?>" class="my-thickbox dashicons dashicons-plus"></a></li>
				</ul>
				
				<?php if( shortcode_exists( '' ) ):
					echo do_shortcode( '[wpeofiles id="' . $task->ID . '" file_list_association ]' );
				endif; ?>
				
				
			</div>


		</div>

	<?php if( !empty ( $task->new ) ):?>
		</div>
	<?php endif; ?>
<?php endif; ?>