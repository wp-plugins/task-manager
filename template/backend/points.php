<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if( !empty( $use_toggle ) ) : ?>
	<div class="wpeo-task-point-use-toggle">
		<p>
			<span class="dashicons dashicons-arrow-right wpeo-point-toggle-arrow"></span>
			<a class="wpeo-point-toggle-a" href="#" title="<?php __( 'Toggle completed point', 'wpeotasks-i18n' ); ?>"><?php _e( 'Completed point', '' ); ?> (<span class='wpeo-task-number-point-completed'><?php echo $task->informations->count_point_completed; ?></span>)</a>
		</p>
	<?php endif; ?>
		<ul class="wpeo-task-point <?php if( !empty( $new_point ) ): ?> wpeo-task-point-<?php echo ($task->informations->user_can_write) ? 'sortable' : 'no-sortable'; ?> <?php endif; ?>" style="<?php if( !empty( $use_toggle ) ) : ?>display: none;<?php endif; ?>" data-nonce="<?php echo wp_create_nonce( 'nonce_point_order' ); ?>" data-id="<?php echo $task->ID; ?>">
			<?php if(!empty($points)):?>
				<?php foreach($points as $key => $point):?>
					<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'point' ) ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<!-- The "add element" point -->
			<?php unset($point); ?>
			
			<?php if( !empty( $new_point ) ): ?>
				<?php require( wpeoTasksTemplate_ctr::get_template_part( WPEOMTM_TASK_DIR, WPEOMTM_TASK_TEMPLATES_MAIN_DIR, 'backend', 'new', 'point' ) ); ?>
			<?php endif; ?>
		</ul>
		
	<?php if( !empty( $use_toggle ) ) : ?>
	</div>
<?php endif; ?>