<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<ul class="wpeo-main-user">
	<li>
		<!-- Variables used by JS -->
		<input type=hidden class="wpeo-post-id" value="<?php echo $id; ?>" />
		<input type=hidden class="wpeo-callback-js" value="<?php echo $callback_js; ?>" />
		<input type=hidden class="wpeo-type" value="<?php echo $type; ?>" />
		
		<?php if( $use_dashicons ) :?>
			<ul class="wpeo-ul-user wpeo-current-user wpeo-connected-user-sortable">
				<?php 
				$array_user_in_id = array();
				
				if( !empty( $array_user_in ) ) :
					foreach( $array_user_in as $user ) :
						$array_user_in_id[] = $user->ID;
						?><li data-id="<?php echo $user->ID; ?>" title="<?php echo $user->ID . ' - ' . $user->user_login . ' ' . $user->user_email; ?>" class="wpeo-user-<?php echo $user->ID; ?>"><?php echo get_avatar($user->ID, 32); ?></li><?php
					endforeach;
					
					/** I don't need you anymore */
					unset( $user );
				endif;
				?>
				
				<li class="wpeo-force-alignright wpeo-state-disable">
					<a href="#" data-id="<?php echo $id; ?>" data-nonce="<?php echo wp_create_nonce( 'eo_nonce_view_user' ); ?>" class="wpeo-user-add dashicons <?php echo $dashicons; ?>"></a>
				</li>
			</ul>
		<?php endif; ?>
	</li>
	
	<li>
		<div class="wpeo-no-display wpeo-user-bloc wpeo-user-point-<?php echo $id; ?>">
			
		</div>
	</li>
</ul>