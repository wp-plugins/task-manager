<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty($task) && !empty( $task->ID ) ): ?>
	<div class="wpeo-project-wrap">
		<div class="wpeo-project-task">
			<!--  Header  -->
			<?php 
				$points = array_merge( wpeo_tasks_points_mod::get_points( ( int ) $task->ID, false ), wpeo_tasks_points_mod::get_points( ( int ) $task->ID, true ) );
				require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'frontend', 'points' ) );
			?>
		</div>
	</div>
<?php endif; ?>