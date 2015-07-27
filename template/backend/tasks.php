<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty ($tasks) ) : ?>
	<?php foreach ( $tasks as $task ) :?>
		<div style='width: <?php echo $this->task_width[$per_page]; ?>%' class="grid-item wpeo-project-task-<?php echo $task->ID; ?> <?php echo $task->informations->my_task; ?>  <?php echo $task->informations->class_tags; ?>">
			<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'task' ) ); ?>
		</div>
	<?php endforeach; ?>
<?php endif; ?>