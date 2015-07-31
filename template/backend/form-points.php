<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="block-points">
	<div>
		<input type="hidden" name="task_id" class="wpeo-point-task-id" value="<?php echo $task->ID; ?>" />
		
		<?php 
		$points = wpeo_tasks_points_mod::get_points( ( int ) $task->ID, false );
		$serialized_data_heart['point'] = $points;
		$new_point = true;
		require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'points' ) );
		unset($new_point); 
		?>
	</div>

	<?php 
	$points = wpeo_tasks_points_mod::get_points( ( int ) $task->ID, true );
	$serialized_data_heart['point_completed'] = $points;
	$use_toggle = true;
	require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'points' ) ); 
	unset($use_toggle);
	?>
</div>
