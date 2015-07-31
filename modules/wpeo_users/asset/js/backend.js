jQuery( document ).ready( function() {	
	
	/** 
	 * When we press on the add button open the template where we can select user we wan't to add
	 * If already opened, close it and remove all user in
	 */
	jQuery( document ).on( 'click', '.wpeo-user-add', function( e ) {
		
		var bloc_task  	= jQuery(this).closest('.wpeo-main-user');
		var id 			= jQuery(this).data('id');
		var type		= bloc_task.find('.wpeo-type').val();
		if( !type ) {
			type = jQuery(this).data('type');
		}
		
		/** If already open close it ! */
		if(jQuery('.wpeo-user-point-' + id).is(':visible')) {
			jQuery('.wpeo-user-point-' + id).fadeOut();
		}
		else {
			var data = {
				action: 'wpeo-view-user',
				id: id,
				type: type,
//				_wpnonce: jQuery(this).data('nonce'),
			};
	
			jQuery('.wpeo-user-point-' + id).load( ajaxurl, data, function() {
				jQuery(this).fadeIn();
				if( callback != undefined && callback['after_load'] != undefined) {
					callback['after_load']();
				}
				
				sortable_user();

			});
		}
		e.preventDefault();
	});
});

function sortable_user() {
	jQuery(".wpeo-adding-user, .wpeo-current-user").sortable({
		placeholder: "wpeo-state-placeholder",
		items: "li:not(.wpeo-state-disabled)",
		connectWith: ".wpeo-connected-user-sortable",
		update: function( event, ui ) {
			var array_id 		= [];
			var bloc_main_user	= jQuery(this).closest('.wpeo-main-user');
			var post_id 		= bloc_main_user.find('.wpeo-post-id').val();
			var type			= bloc_main_user.find('.wpeo-type').val();
			
			if(jQuery(this).hasClass('wpeo-current-user')) {
				jQuery(this).find('li').each(function() {
					if(!jQuery(this).hasClass('wpeo-force-alignright'))
						array_id.push( jQuery(this).data('id') );
				});

				
				var data = {
					action: 'wpeo-update-user',
					post_id: post_id,
					array_id: array_id,
					type: type,
				};
				
				jQuery.post( ajaxurl, data, function( response ) {});
			}
		}
	});
}