jQuery( document ).ready( function() {	
	
	/** 
	 * When we press on the add button open the template where we can select user we wan't to add
	 * If already opened, close it and remove all user in
	 */
	jQuery( document ).on( 'click', '.wpeo-tag-add', function( e ) {
		
		var bloc_task = jQuery(this).closest('.wpeo-main-tags');
		var id = jQuery(this).data('id');
		
		/** If already open close it ! */
		if(jQuery('.wpeo-tag-point-' + id).is(':visible')) {
			jQuery('.wpeo-tag-point-' + id).fadeOut();
		}
		else {
			var data = {
				action: 'wpeo-view-tag',
				id: id,
//				_wpnonce: jQuery(this).data('nonce'),
			};
	
			jQuery('.wpeo-tag-point-' + id).load( ajaxurl, data, function() {
				jQuery(this).fadeIn();
				if( callback != undefined && callback['after_load'] != undefined) {
					callback['after_load']();
				}
				
				sortable_tag();

			});
		}
		e.preventDefault();
	});
});

function sortable_tag() {
	jQuery(".wpeo-adding-tags, .wpeo-current-tags").sortable({
		placeholder: "wpeo-state-placeholder",
		items: "li:not(.wpeo-state-disabled)",
		connectWith: ".wpeo-connected-sortable",
		update: function( event, ui ) {
			var array_slug 		= [];
			var bloc_main_tag	= jQuery(this).closest('.wpeo-main-tags');
			var post_id 		= bloc_main_tag.find('.wpeo-post-id').val();
			
			if(jQuery(this).hasClass('wpeo-current-tags')) {
				jQuery(this).find('li').each(function() {
					if(!jQuery(this).hasClass('wpeo-force-alignright'))
						array_slug.push( jQuery(this).data('slug') );
				});

				
				var data = {
					action: 'wpeo-update-tag',
					post_id: post_id,
					array_slug: array_slug,
				};
				
				jQuery.post( ajaxurl, data, function( response ) {});
			}
		}
	});
}