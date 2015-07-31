<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty($task) && !empty( $task->ID ) ): ?>
	<?php if( !empty ( $task->new ) ):?>
		<div style='width: <?php echo $this->task_width[$this->task_per_page]; ?>%' class="grid-item wpeo-project-task-<?php echo $task->ID; ?> <?php echo $task->informations->class_tags; ?> <?php echo $task->informations->my_task; ?>">
	<?php endif; ?>
	
	<?php $serialized_data_heart = array( 'title' => $task->post_title ); ?>

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
			<a download="Export.txt" class="wpeo-download-file" href="#"></a>
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
							<?php 
							$file_filter = '';
							
							if( has_filter( 'wpeo_tasks_file_filter' ) )
								$file_filter = apply_filters( 'wpeo_tasks_file_filter', $file_filter, $task->ID ); 
							
							echo $file_filter;
							?>
							
						<li>
							<i class="dashicons dashicons-welcome-view-site"></i> 
							<a href="<?php echo get_post_permalink( $task->ID ); ?>" title="<?php _e( 'View the task', 'wpeotasks-i18n' ); ?>" target="_blank" class="wpeo-view-task"><?php _e( 'View the task', 'wpeotasks-i18n' );?></a>
						</li>
						
						<li class="wpeo-export" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_export' ); ?>"  title="<?php _e( 'Export', 'wpeotasks-i18n' ); ?>">
							<i class="dashicons dashicons-media-text"></i> <?php _e( 'Export', 'wpeotasks-i18n' ); ?>
						</li>
						<?php if( wpeo_tasks_mod::$task_state_archive == $task->post_status):?>
							<li class="wpeo-send-to-unpacked" title="<?php _e( 'Send to unpacked', 'wpeotasks-i18n' ); ?>" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_unpacked' ); ?>"><i class="dashicons dashicons-archive"></i>  <?php _e( 'Unpacked', 'wpeotasks-i18n' ); ?></li>
						<?php else: ?>
							<li class="wpeo-send-to-archive" title="<?php _e( 'Move to archive', 'wpeotasks-i18n' ); ?>" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_archive' ); ?>"><i class="dashicons dashicons-archive"></i>  <?php _e( 'Archive', 'wpeotasks-i18n' ); ?></li>
						<?php endif; ?>
						
						<li class="wpeo-send-task-to-trash"  title="<?php _e( 'Move to trash', 'wpeotasks-i18n'); ?>" data-id="<?php echo $task->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_trash' ); ?>" ><i class="dashicons dashicons-trash"></i>  <?php _e( 'Trash', 'wpeotasks-i18n' ); ?></li>	
					</ul>
				</li>
			
				
			</ul>
			
			<!-- Points -->
			<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'form', 'points' ) ); ?>

			<!-- Footer -->
			<div class="wpeo-task-footer">		
				<!--  The tags -->
				<?php if( shortcode_exists( 'wpeotags' ) ):
					echo do_shortcode( '[wpeotags id="' . $task->ID . '" callback_js="callback_tag" ]' );
				endif; ?>		
				
				<!-- The user task -->
				<?php if( shortcode_exists( 'wpeouser' ) ) :
					echo do_shortcode( '[wpeouser id="' . $task->ID . '" ]' );
				endif; ?>
				
				<!-- The file task -->
				<?php if( shortcode_exists( 'wpeofiles' ) ):
					echo do_shortcode( '[wpeofiles id="' . $task->ID . '" file_list_association ]' );
				endif; ?>
				
				
			</div>
			
			<!-- heart data -->
			<?php $serialized_data_heart = md5( serialize( $serialized_data_heart ) ); ?>
			<input type="hidden" class="wpeo-serialized-data-heart" value="<?php echo $serialized_data_heart; ?>" />
			<?php unset ($serialized_data_heart);?>

		</div>
	<?php if( !empty ( $task->new ) ):?>
		</div>
	<?php endif; ?>
<?php endif; ?>