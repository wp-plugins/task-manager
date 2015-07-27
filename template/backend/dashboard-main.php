<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap wpeo-project-wrap">
	<div class="wpeo-container-notification">
	</div>

	<div class="wpeo-task-dashboard-header" >
		<h2><?php _e('Tasks manager', 'wpeotasks-i18n'); ?> <a href="#" class="wpeo-new-task add-new-h2" data-nonce="<?php echo wp_create_nonce( 'new_task' ); ?>"><?php _e( 'New task', 'wpeotasks-i18n' ); ?></a></h2>
		
		<a href="#" class="button-secondary button wpeo-button-my-task"><?php _e( 'My tasks', 'wpeotasks-i18n' ); ?></a>
		<a href="#" class="button-primary button wpeo-button-all-tasks"><?php _e( 'All tasks', 'wpeotasks-i18n' ); ?></a>
		<a href="#" data-nonce="<?php echo wp_create_nonce( 'nonce_export_all' ); ?>" class="button-secondary button wpeo-export-all"><?php _e( 'Export selected tasks', 'wpeotasks-i18n' ); ?></a>
		<!-- Link hidden for export -->
		<a download="Export_All.txt" class="wpeo-export-all-download-file" href="#"></a>
		
		<label for="wpeo-sort-by-tag"><?php _e( 'Sort : ', 'wpeotasks-i18n' ); ?></label>
		<select id="wpeo-sort-by-tag" class="wpeo-task-dashboard-tags" >
			<option value="all"><?php _e( 'All', 'wpeotasks-i18n' );?></option>
			<?php if( !empty( $tags ) ) :?>
				<?php foreach( $tags as $tag ) : ?>
					<option value="<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</div> 

	<div class="wpeo-block-tasks grid">
		<div class="grid-sizer" style='width: <?php echo $this->task_width[$per_page]; ?>%'></div>
	<?php if( !empty ($tasks) ) : ?>
		<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'tasks' ) ); ?>
	<?php else: ?>
		<span class='wpeo-tasks-no-task'><?php _e( 'No tasks, press the "New task" button for create a task', 'wpeotasks-i18n' );?></span>
	<?php endif; ?>
	</div>
	
</div><!-- wps-pos-dashboard-wrap -->