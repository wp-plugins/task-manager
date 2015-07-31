<!-- The list of user -->
<?php 
if( !empty( $array_user ) ) :
	?><ul class="wpeo-ul-user wpeo-adding-user wpeo-connected-user-sortable"><?php 
	foreach( $array_user as $user ) :
		if( !in_array( $user->ID, is_array( $array_user_in_id ) ? $array_user_in_id : array() ) ) :
			echo '<li title="' . $user->ID . ' - ' . $user->user_login . ' ' . $user->user_email . '" class="wpeo-item-user" data-id="' . $user->ID . '">' . get_avatar( $user->ID, 32, '' ) . '</li>';
		endif;
	endforeach;
	?></ul><?php 
endif; 
?>

<div style="clear: both"></div>