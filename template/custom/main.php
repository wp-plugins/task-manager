<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap wpeo-project-wrap">
	<input type="hidden" class="wpeo-task-post-parent" value="<?php echo $post_id; ?>" />

	<div class="wpeo-container-notification">
	</div>

	<div class="wpeo-task-dashboard-header" >
		<a href="#" class="wpeo-new-task add-new-h2" data-nonce="<?php echo wp_create_nonce( 'new_task' ); ?>"><?php _e( 'New task', 'wpeotasks-i18n' ); ?></a>
	</div> 

	<div class="wpeo-block-tasks grid">
		<div class="grid-sizer"></div>
		<?php if( !empty ($tasks) ) : ?>
			<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'tasks' ) ); ?>
		<?php endif; ?>
	</div>
	
	<?php if( empty( $tasks ) ) :?>
		<span class='wpeo-tasks-no-task'><?php _e( 'No tasks, press the "New task" button for create a task', 'wpeotasks-i18n' );?></span>
	<?php endif; ?>	
</div><!-- wps-pos-dashboard-wrap -->