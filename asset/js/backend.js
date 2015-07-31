var cancel;
var heart_task_time = 30000;
var interval;

jQuery( document ).ready( function(){	
	/** 
	 * Heart task 
	 * All task where i'm in write mode need to be checked by this heart
	 * This heart is only a ajax request "Hey you need to say me if you need to be update or not ?"
	 * If the task need to be updated, it means that some update was performed on the content
	 */
/**	interval = setInterval(function() {
//		clearInterval(interval);

		var serialized_data = [];
		jQuery('.wpeo-serialized-data-heart').each(function(key)	 {
			serialized_data[key] = jQuery(this).val();
		});
		
		var data = {
			action: 'wpeo-heart-task',
			data: serialized_data,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if( response.data.refresh_task_id ) {
				response.data.refresh_task_id.forEach(function( id ) {
					refresh_task( id );
				});

				if(response.data.refresh_task_id.length > 0) {
					create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
				}
			}
			
		});
	}, heart_task_time);*/
	
	
	/** My thickbox */
	jQuery(document).on('click', '.my-thickbox', function(e) {
		e.preventDefault();
		tb_show(jQuery(this).attr('title'), jQuery(this).attr('href'));
	});
	
	var array_marker = [];
	
	/** Dashicons marker */
	jQuery(document).find(".task-marker").click(function() {
		var block_task = jQuery(this).closest('.wpeo-project-task');
		var task_id = block_task.find('.wpeo-point-task-id').val();
		
		if(!block_task.hasClass( 'wpeo-task-selected' ) ) {
			block_task.addClass('wpeo-task-selected');

			/** Change the box shadow of the task to see that we selected */
			block_task.css( 'box-shadow', '0 0 0 4px #328DCF' );
			block_task.css( '-webkit-box-shadow', '0 0 0 4px #328DCF' );
			block_task.css( '-moz-box-shadow', '0 0 0 4px #328DCF' );
			block_task.css( '-o-box-shadow', '0 0 0 4px #328DCF' );

			jQuery( this ).css( 'background', '#328DCF' );
			jQuery( this ).css( 'border', '4px solid #fff' );
			
			/** Add task_id to the array_marker */
			array_marker.push(task_id);
		}
		else {
			block_task.removeClass('wpeo-task-selected');
			
			/** Get the index in the array for delete it */
			var index = array_marker.indexOf( task_id );
			
			if( index > -1 ) {
				array_marker.splice( index, 1 );
			}
			
			/** Change the box shadow of the task to see that we selected */
			block_task.css( 'box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-webkit-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-moz-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-o-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );

			jQuery( this ).css( 'background', '#fff' );
			jQuery( this ).css( 'border', '4px solid rgba(0,0,0,0.4)' );
		}
	});
	
	/** Selected items actions */
	jQuery( '.wpeo-export-all' ).click( function() {
		/** If 0 element selected stop action */
		if( array_marker.length == 0 )
			return;
		
		/** Security */
		var _wpnonce = jQuery( this ).data( 'nonce' );
		
		/** Call the export function */
		var data = {
			action: 'wpeo-export-tasks',
			array_tasks: array_marker,
			_wpnonce: _wpnonce,
		};
		
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery( '.wpeo-export-all-download-file' ).attr('href', response.data.link );
			jQuery( '.wpeo-export-all-download-file' )[0].click();
			create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			clean_items_selected();
		});
	});
	
	/** Flex text */
	jQuery(".wpeo-project-wrap textarea").flexText();
	
	/** Create task */
	jQuery(".wpeo-project-wrap .wpeo-new-task").click(function(e) {
		e.preventDefault();
		
		var data = {
			action: "wpeo-new-task",
			post_parent: jQuery(this).closest('#wpeo-tasks-metabox').find('.wpeo-task-post-parent').val(),
			_wpnonce: jQuery(this).data('nonce'),
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".wpeo-project-wrap .grid .wpeo-tasks-no-task").hide();
			var bloc = jQuery(response.data.template);
			jQuery('.grid').append( bloc ).masonry( 'appended', bloc, true );
			jQuery(".wpeo-project-wrap textarea").flexText();
			create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
		});	
	});
	
	/** Send to archive */
	jQuery(document).on('click', '.wpeo-send-to-archive', function() {
		var task_id = jQuery(this).data('id');
		var data_nonce = jQuery(this).data('nonce');
		var task = jQuery(this).closest('.grid-item');
		
		var data = {
			action: "wpeo-send-to-archive",
			task_id: task_id,
			_wpnonce: data_nonce,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(!display_error(task_id, response)) {
				task.addClass('wpeo-task-archived');
				task.fadeOut(function() {
					refresh_task ( task_id );
					jQuery('.grid').masonry();
				});
				create_notification( 'trash', 'info', response.data.notification_message, response.data.notification_method, task_id );
			}
		});
	});
	
	/** Send to unpacked */
	jQuery(document).on('click', '.wpeo-send-to-unpacked', function() {
		var task_id = jQuery(this).data('id');
		var data_nonce = jQuery(this).data('nonce');
		var task = jQuery(this).closest('.grid-item');
		
		var data = {
			action: "wpeo-send-to-unpacked",
			task_id: task_id,
			_wpnonce: data_nonce,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(!display_error(task_id, response)) {
				task.fadeIn(function() {
					task.removeClass('wpeo-task-archived');
					refresh_task ( task_id );
					jQuery('.grid').masonry();
				});
				create_notification( 'info', 'info', response.data.notification_message, undefined, task_id );
			}
		});
	});
	
	/** Delete a task */
	jQuery(document).on('click', '.wpeo-send-task-to-trash', function() {
		var task_id = jQuery(this).data('id');
		var data_nonce = jQuery(this).data('nonce');
		var task = jQuery(this).closest('.grid-item');
		
		var data = {
			action: "wpeo-delete-task",
			task_id: task_id,
			_wpnonce: data_nonce,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(!display_error( task_id, response ) ) {
				task.fadeOut(function() {
					jQuery('.grid').masonry();
				});
				create_notification( 'trash', 'info', response.data.notification_message, response.data.notification_method, task_id );
			}
		});
	});
	
	/** Delete a point in a task */
	jQuery(document).on('click', '.wpeo-send-point-to-trash', function() { delete_point( jQuery( this ), true ) } );
	
	/** Delete a comment in point */
	jQuery(document).on('click', '.wpeo-send-comment-to-trash', function() {
		if(confirm('Delete this comment ? ' ) ) {
			var comment_id = jQuery(this).data('id');
			var data_nonce = jQuery(this).data('nonce');
			var task_id = jQuery(this).closest('#TB_window').find('.wpeo-point-task-id').val();
			var point_id = jQuery(this).closest('#TB_window').find('.wpeo-point-id').val();
			
			var data = {
				action: 'wpeo-delete-comment',
				comment_id: comment_id,
				point_id: point_id,
				task_id: task_id,
				_wpnonce: data_nonce,
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				if(!display_error( task_id, response) ) {
					jQuery('.wpeo-point-comment-' + comment_id).fadeOut();
					create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
				}
			}); 
		}
	});
	
	/** Change color button add point */
	jQuery(document).on('keyup', '.wpeo-project-wrap .add-point', function() {
		if( 0 == jQuery(this).val().length) {
			jQuery(this).closest('ul').find('.wpeo-tasks-add-new-point').css('opacity', '0.4');
		}
		else {
			jQuery(this).closest('ul').find('.wpeo-tasks-add-new-point').css('opacity', '1');
		}
	});
	
	/** Add a point to a task */
	jQuery(document).on('click', '.wpeo-task-point li .wpeo-tasks-add-new-point', function() {
		var element = jQuery(this);
		var ul = jQuery(this).closest('ul');
		var task_id = jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		var message = ul.find('textarea').val();
		var nonce_add_point = jQuery(this).data('nonce');
		element.attr('disabled', true);
		
		var data = {
			action: "wpeo-add-point",
			can_empty: false,
			task_id: task_id,
			message: message,
			_wpnonce: nonce_add_point,
		}
		
		jQuery.post(ajaxurl, data, function(response) {
			element.attr('disabled', false);

			if(!display_error( task_id, response ) ) {	
				var li_last = jQuery('.wpeo-project-task-' + task_id).find('.wpeo-add-point');
				li_last.before(response.data.template);
				li_last.find('textarea').val('');
				element.css('opacity', '0.4');
				jQuery('.grid').masonry();
				jQuery(".wpeo-project-wrap textarea").flexText();
				create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			}
		});
	});
	
	/** Edit title */
	jQuery(document).on('blur', '.wpeo-project-task-title', function(e) { 
		var task_id =  jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		var title = jQuery(this).val();
		var _wpnonce = jQuery(this).data('nonce');
		
		var data = {
			action: "wpeo-save-title",
			task_id: task_id,
			title: title,
			_wpnonce: _wpnonce,
		};
		
		jQuery.post( ajaxurl, data, function( response ) {
			if( !display_error( task_id, response ) ) {
				create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			}	
		});
	});
	
	/** Edit time estimated */
	jQuery(document).on('blur', '.wpeo-project-task-time-estimated', function(e) {
		var task_id =  jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		var time = jQuery(this).val();
		var _wpnonce = jQuery(this).data('nonce');
		
		var data = {
			action: "wpeo-save-time-estimated",
			task_id: task_id,
			time: time,
			_wpnonce: _wpnonce,
		};
		
		jQuery.post( ajaxurl, data, function( response ) {
			if( !display_error( task_id, response ) ) {
				create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			}
		});
	});
	
	/** Additional menu */
	jQuery(document).on('click', '.wpeo-task-additional-buttons', function(e) {
		jQuery(this).closest('.wpeo-project-task').find('.wpeo-bloc-additional-buttons').toggle(200);
	});
	
	jQuery('body').click( function(e) {
		if(e.target.class == 'wpeo-bloc-additional-buttons')
			return;
		
		jQuery('.wpeo-bloc-additional-buttons').fadeOut();
	});
	
	/** Unblur point */
	jQuery(document).on('blur', '.wpeo-task-point .wpeo-point-can-update', function() {
		var task_id = jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		
		var data = {
			action: 'wpeo-update-point',
			point_id: jQuery(this).data('id'),
			task_id: task_id,
			message: jQuery(this).val(),
			_wpnonce: jQuery(this).data('nonce'),
		};
		
		if( jQuery(this).val() != "" ) {
			jQuery.post(ajaxurl, data, function(response) {
				if(!display_error( task_id, response ) ) {
					create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
				}
			});
		}
	});
	
	/** When close the users thickbox */
	jQuery(document).on('click', '#TB_window #wpeo-btn-ok-users', function() {
		jQuery('#TB_window #form-users').ajaxForm(function(response) {
			var task_id = jQuery('#TB_window').find('.wpeo-task-id').val();
			var id = jQuery('#TB_window').find('.wpeo-users-id').val();
			var type = jQuery('#TB_window').find('.wpeo-type-thickbox').val();
			
			jQuery('#TB_closeWindowButton').trigger( 'click' );
			
			if(!display_error( task_id, response ) ) {
				if(type == 'task') {
					refresh_task( task_id );
					jQuery( ".wpeo-project-task-" + task_id ).addClass('wpeo-my-task');
				}
			}
		});
	});
	
	/** When close the tags thickbox */
	jQuery(document).on('click', '#TB_window #wpeo-btn-ok-tags', function() {
		jQuery('#TB_window #form-tags').ajaxForm(function(response) {
			var task_id = jQuery('#TB_window').find('.wpeo-task-id').val();
			jQuery('#TB_closeWindowButton').trigger( 'click' );
			
			if(!display_error( task_id, response ) ) {
				refresh_task( task_id );
				jQuery( ".wpeo-project-task-" + task_id ).addClass( response.data.tags );
			}
		});
	});
	
	/** When close the setting thickbox */
	jQuery(document).on('click', '#TB_window #wpeo-btn-ok-setting', function() {
		jQuery('#TB_window #form-setting').ajaxForm(function(response) {
			var task_id = jQuery('#TB_window').find('.wpeo-task-id').val();
			jQuery('#TB_closeWindowButton').trigger( 'click' );
			
			if(!display_error( task_id, response ) ) {
				create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			}
		});
	});
	
	/** Export */
	jQuery(document).on('click', '.wpeo-export', function() {
		var task_id = jQuery(this).data('id');
		var element = jQuery(this);
		var link = element.closest('.grid-item').find('.wpeo-download-file');
		var data_nonce = jQuery(this).data('nonce');
		
		var data = {
			action: 'wpeo-export',
			task_id: task_id,
			_wpnonce: data_nonce,
		};
		
		jQuery.post(ajaxurl, data, function( response ) {
			if(!display_error( task_id, response ) ) {
				link.attr('href', response.data.link );
				link[0].click();
				create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
			}
		});
	});
	
	/** Add current date */
	jQuery(document).on("click", ".wpeomtm-current-date-shortcut", function(){
		  var d = new Date();
		  /** Current month */
		  var month = d.getMonth()+1;
		  /** Current day */
		  var day = d.getDate();
		  /** Prepare output */
		  var output = d.getFullYear() + '-' +
		  	(month<10 ? '0' : '') + month + '-' +
		  	(day<10 ? '0' : '') + day;
		  /** Output */
		  jQuery( this ).prev( ".isDate" ).val( output );
	  });
	
	/** Add time */
	jQuery(document).on('click', '#TB_window #wpeo-btn-add-time', function() {
		jQuery('#TB_window #wpeo-time-manager-form').ajaxForm(function(response) {
			if( !display_error( response.data.task_id, response ) ) {
				jQuery( '#TB_window #TB_closeWindowButton').trigger('click');
				
				/** Refresh the time */
				jQuery( ".wpeo-project-task-" + response.data.task_id + " .wpeo-project-task-time" ).html( response.data.time_in_task );
				jQuery( ".wpeo-project-task-" + response.data.task_id + " .point-" + response.data.point_id + " .wpeo-time-in-point" ).html( response.data.time_in_point );
				
				refresh_task( response.data.task_id );
			}
		});
	});
	
	/** Create tag */
	jQuery(document).on('click', '#TB_window .wpeo-create-tag', function() {
		var task_id = jQuery(this).closest('form').find('.wpeo-task-id').val();
		var name_tag = jQuery(this).closest('form').find('.wpeo-name-tag').val();
		var _wpnonce = jQuery(this).data('nonce');

		var data = {
			action: 'create-tag',
			name_tag: name_tag,
			task_id: task_id,
			_wpnonce: _wpnonce,
		};
		
		jQuery.post(ajaxurl, data, function( response ) {
			if(!display_error( task_id, response ) ) {
				jQuery('.wpeo-project-task-' + task_id).find('.dashicons-category').trigger('click');
				jQuery('.wpeo-task-dashboard-tags').append('<option value="' + response.data.slug_tag + '">' + name_tag + '</option>');
				create_notification( 'category', 'info', response.data.message, undefined, undefined );
			}
		});
	});
	
	/** Checkbox point finished */
	jQuery(document).on('click', '.wpeo-done-point', function() {
		var element_li = jQuery(this).closest('.wpeo-task-point');
		var task_id = jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		
		jQuery(this).attr('disabled', true);
		
		var data = {
			action: 'wpeo-point-done',
			task_id: task_id,
			point_id: element_li.data('id'),
			done: jQuery(this).is(':checked'),
			_wpnonce: jQuery(this).data('nonce'),
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(!display_error( task_id, response ) ) {
				element_li.fadeOut();
				var li_last = jQuery('.wpeo-project-task-' + task_id).find('.wpeo-add-point');
				var current_value = parseInt(jQuery('.wpeo-project-task-' + task_id).find('.wpeo-task-number-point-completed').text());
				
				if(response.data.done == 'false') {
					current_value--;
					li_last.before(response.data.html);
				}
				else {
					current_value++
					jQuery('.wpeo-project-task-' + task_id + ' .wpeo-task-point-use-toggle > ul:first').append(response.data.html);
				}
				jQuery(".wpeo-project-wrap textarea").flexText();
				jQuery('.wpeo-project-task-' + task_id).find('.wpeo-task-number-point-completed').text(current_value);
			}
		});
	});
	
	/** Toggle point completed */
	jQuery(document).on('click', '.wpeo-task-point-use-toggle p', function(e) {
		e.preventDefault();
		
		jQuery(this).find('.wpeo-point-toggle-arrow').toggleClass('dashicons-arrow-right dashicons-arrow-down');
		jQuery(this).closest('.wpeo-task-point-use-toggle').find('ul:first').toggle(200, function() {
			jQuery('.grid').masonry();
		});
	});
	
	/** Change user write task */
	jQuery(document).on('click', '.wpeo-task-mask', function() {
		var task_id = jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		var element = jQuery(this);
		element.fadeOut();
		
		var data = {
			action: 'wpeo-change-user-write',
			current_mode: jQuery(this).data('mode'),
			task_id: task_id,
			_wpnonce: jQuery(this).data('nonce'),
		};

		jQuery.post(ajaxurl, data, function(response) {
			refresh_task(task_id);
			create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
		});
	});
	
	/** Sortable point */
	jQuery(".wpeo-task-point-sortable").sortable({
		connectWith: ".wpeo-task-point-sortable",
		items: "> li:not(.ui-state-disabled)",
		dropOnEmpty: false,
		handle: '.dashicons-menu',
		update: function ( event, ui) {
			var task_id = jQuery(this).closest('.wpeo-task-point').data('id');
			set_order ( task_id );
		}
	});
	
	var array_elements = [];
	
	/** My task */
	jQuery('.wpeo-project-wrap').on('click', '.wpeo-button-my-task', function() {
		array_elements = [];
		
		jQuery(this).removeClass('button-secondary').addClass('button-primary');
		jQuery('.wpeo-button-all-tasks').removeClass('button-primary').addClass('button-secondary');
		
		jQuery('.grid .grid-item .wpeo-my-task').show();
		
		jQuery('.grid .grid-item').each(function(key, value ) {
			if( !jQuery( this ).hasClass( 'wpeo-my-task' ) ) array_elements.push(value);
		});
		
		jQuery(array_elements).fadeOut(function() {
			jQuery('.grid').masonry();
		});
	});
	
	/** All task */
	jQuery('.wpeo-project-wrap').on('click', '.wpeo-button-all-tasks', function() {		
		jQuery(this).removeClass('button-secondary').addClass('button-primary');
		jQuery('.wpeo-button-my-task').removeClass('button-primary').addClass('button-secondary');
		
		jQuery('.grid .grid-item').fadeIn(function() {
			jQuery('.grid').masonry();
			array_elements = [];
		});
	});
	
//	jQuery('.wpeo-project-wrap .wpeo-button-my-task').trigger('click');
	
	var array_elements_tags = [];
	
	/** Sort by tags */
	jQuery('.wpeo-project-wrap .wpeo-task-dashboard-tags').on('change', function(e) {
		var tag_slug = jQuery(this).val();
		
		if( 'all' === tag_slug ) {
			jQuery('.grid .grid-item').fadeIn(function() {
				jQuery('.grid').masonry();
				array_elements = [];
				array_elements_tags = [];
			});
		}
		else {
			array_elements_tags = [];
			
			jQuery('.grid .grid-item').each(function( key, value ) {
				if( !jQuery( this ).hasClass( tag_slug ) ) array_elements_tags.push(value);
			});
			
			jQuery(array_elements_tags).fadeOut(function() {
				jQuery('.grid').masonry();
			});
		}
		
		e.preventDefault();
	});
	
	/** @version 1.2
	 * 	When press the button archive
	 *  Task the server with ajax request to send me all archived task !
	 *  When we get the response, display it
	 *  
	 *  @version 1.0
	 *  Hide all task
	 *  Display archived task
	 */
	jQuery('.wpeo-project-wrap .wpeo-button-archive-task').click(function() {
		jQuery('.grid .grid-item').hide();
		jQuery('.grid .wpeo-task-archived').fadeIn(function() {
			jQuery('.grid').masonry();
		});
	});
	
	/** Masonry */
	jQuery('.grid').masonry({
		itemSelector: '.grid-item',
		percentPosition: true,
		columnWidth: '.grid-sizer',
	});
	
	/** Points */
	/** Enter on add point */
	jQuery(document).on('keydown', '.add-point', function(e) {
		if(e.which == 13) {
			jQuery(this).closest('.wpeo-task-point').find('.wpeo-tasks-add-new-point').click();
		}
	});
	
	/** Handle event in point */
//	jQuery(document).on('keydown', '.wpeo-task-point-sortable > li input[type="text"]', function(e) {
//		if(!jQuery(this).hasClass('add-point')) {
//			var element_li = jQuery(this).closest('.wpeo-task-point');
//			var prev_element_li = element_li.prev();
//			var next_element_li = element_li.next();
//			var cursor_position = get_cursor_position(jQuery(this)[0]);
//			var task_id = jQuery(this).closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
//			var nonce_add_point = jQuery(this).closest('.wpeo-project-task').find('.wpeo-tasks-add-new-point').data('nonce');
//			var length = jQuery(this).val().length;
//	
//			/** Enter */
//		
//			if(e.which == 13) {
//				var text_left = get_text_cursor( jQuery( this).val(), cursor_position, "left");
//				var text_right = get_text_cursor( jQuery( this ).val(), cursor_position, "right" );
//				
//				var data = {
//					action: "wpeo-add-point",
//					can_empty: 1,
//					task_id: task_id,
//					message: text_right,
//					_wpnonce: nonce_add_point,
//				}
//				
//				jQuery.post(ajaxurl, data, function(response) {
//					if(!display_error( task_id, response ) ) {	
//						element_li.find('input').val(text_left);
//						element_li.after(response.data.template);
//						set_order ( task_id );
//						jQuery('.point-' + response.data.point_id).find('input[type="text"]').focus();
//						jQuery('.grid').masonry();
//					}
//				});
//			}
//			/** Delete */
//				else if(e.which == 8) {			
//				// Delete lane
//				if( length == 0 ) {
//					delete_point( element_li.find('.wpeo-send-point-to-trash'), false );
//					// Select prev li
//					if(prev_element_li) {
//						prev_element_li.find('input[type="text"]').focus();
//						prev_element_li.find('input[type="text"]').putCursorAtEnd();
//					}
//				}
//			}
//			/** Down */
//			else if(e.which == 40 && cursor_position >= length) {
//				if(next_element_li) {
//					next_element_li.find('input[type="text"]').focus();
//				}
//			}
//			/** Up */
//			else if(e.which == 38 && cursor_position == 0) {
//				if(prev_element_li ){
//					prev_element_li.find('input[type="text"]').focus();
//				}
//			}
//		}
//	});

	
	/** Cancel function dynamic call */
	cancel = {
		cancel_delete_task: function(bloc_notification, task_id) {
			var data = {
				action: 'wpeo-cancel-delete-task',
				task_id: task_id,
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				var bloc = jQuery(response.data.template);
				jQuery('.grid').append( bloc ).masonry( 'appended', bloc, true );
				bloc_notification.remove();
			});
		},
		cancel_delete_point: function(bloc_notification, args) {
			var data = {
				action: 'wpeo-cancel-delete-point',
				point_id: args.point_id,
			}
			
			jQuery.post(ajaxurl, data, function(response) {
				refresh_task( args.task_id );
				bloc_notification.remove();
			});
		},
		cancel_archive_task: function(bloc_notification, task_id) {
			jQuery('.wpeo-project-task-' + task_id).find('.wpeo-send-to-unpacked').click();
			bloc_notification.remove();
		}
	};
	
	/** Cursor position */
	function get_cursor_position (oField) {

	  // Initialize
	  var iCaretPos = 0;

	  // IE Support
	  if (document.selection) {

	    // Set focus on the element
	    oField.focus ();

	    // To get cursor position, get empty selection range
	    var oSel = document.selection.createRange ();

	    // Move selection start to 0 position
	    oSel.moveStart ('character', -oField.value.length);

	    // The caret position is selection length
	    iCaretPos = oSel.text.length;
	  }

	  // Firefox support
	  else if (oField.selectionStart || oField.selectionStart == '0')
	    iCaretPos = oField.selectionStart;

	  // Return results
	  return (iCaretPos);
	}
	
	/** Get text cursor */
	function get_text_cursor( val, position, direction ) {
		var text = "";
		
		if(direction == "right")
			text = val.slice(position, val.length);
		else
			text = val.slice(0, position);
		
		return text;
	}
	
	function set_order ( task_id ) {
		var array_id = new Array();
		
		var task = jQuery( '.wpeo-project-task-' + task_id );
		jQuery.each( task.find( '.wpeo-task-point:first' ).find( 'textarea' ) , function( index, value ) {
			if( jQuery(this).data('id') != undefined ) {
				array_id[index] = jQuery(this).data('id');
			}
		});
		
		var nonce = task.find('.wpeo-task-point:first').data('nonce');

		var data = {
			action: "wpeo-set-order-point",
			array_id: array_id,
			task_id: task_id,
			_wpnonce: nonce,
		};

		jQuery.post(ajaxurl, data, function(response) {
			create_notification( 'info', 'info', response.data.notification_message, undefined, undefined );
		});
	}
	
	/** Delete point function */
	function delete_point( element, use_confirm ) {
		var point_id = element.data('id');
		var task_id = element.closest('.wpeo-project-task').find('.wpeo-point-task-id').val();
		var data_nonce = element.data('nonce');
		
		var data = {
			action: "wpeo-delete-point",
			task_id: task_id,
			point_id: point_id,
			_wpnonce: data_nonce,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if(!display_error( task_id, response ) ) {
				jQuery('.point-' + point_id).fadeOut(function() {
					if( use_confirm )
						refresh_task( task_id );
				});
				var args = {
					point_id: point_id,
					task_id: task_id,
				}
				
				if( use_confirm )
					create_notification( 'trash', 'info', wpeo_project_delete_comment_cancel_message, 'cancel_delete_point', args );
			}
		});	
	}
	
	/** Deselect all items selected */
	function clean_items_selected() {
		if( array_marker.length == 0) {
			return;
		}
		
		array_marker.forEach( function ( task_id ) {
			var block_task = jQuery( '.wpeo-project-task-' + task_id ).find( '.wpeo-project-task' );
			block_task.removeClass( 'wpeo-task-selected' );
			
			/** Change the box shadow of the task to see that we selected */
			block_task.css( 'box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-webkit-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-moz-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );
			block_task.css( '-o-box-shadow', '1px 1px 5px 0px rgba(0,0,0,.2)' );

			jQuery( this ).css( 'background', '#fff' );
			jQuery( this ).css( 'border', '4px solid rgba(0,0,0,0.4)' );

		});
		
		array_marker.length = 0;
	}
	
	/** Table sorter for the Timeline ! */
	jQuery('#wpeo-task-timeline').tablesorter();
});

function create_notification( dashicons, type, message, method, args ) {
	var data = {
		action: "wpeo-load-notification",
		type: type,
		message: message,
		method: method,
		dashicons: dashicons,
	}
	
	jQuery('.wpeo-container-notification').append('<div></div>');
	
	var my_div = jQuery('.wpeo-container-notification div:last');
	my_div.load(ajaxurl, data, function() {
		setTimeout(function() {
			my_div.fadeOut(200);
		}, 5000);
		
		if( method !== undefined) {
			jQuery('.wpeo-container-notification .wpeo-notification:last .wpeo-notification-cancel').click(function() {
				jQuery(this).closest('.wpeo-notification').toggleClass('fadeInLeft fadeOutLeft');
				cancel[method](jQuery(this).closest('.wpeo-notification'), args );
			});
		}
		
		jQuery('.wpeo-container-notification .wpeo-notification:last .dashicons-no').click(function() {
			jQuery(this).closest('.wpeo-notification').toggleClass('fadeInLeft fadeOutLeft');
		});
	});	
}

jQuery.fn.putCursorAtEnd = function() {

  return this.each(function() {

    jQuery(this).focus()

    // If this function exists...
    if (this.setSelectionRange) {
      // ... then use it (Doesn't work in IE)

      // Double the length because Opera is inconsistent about whether a carriage return is one character or two. Sigh.
      var len = jQuery(this).val().length * 2;

      this.setSelectionRange(len, len);
    
    } else {
    // ... otherwise replace the contents with itself
    // (Doesn't work in Google Chrome)

      $(this).val(jQuery(this).val());
      
    }

    // Scroll to the bottom, in case we're in a tall textarea
    // (Necessary for Firefox and Google Chrome)
    this.scrollTop = 999999;

  });

};

/**
 * Hey i need to refresh my task for update the content
 * Can u do this for me ?
 * @param int task_id
 * @return void 
 */
function refresh_task( task_id ) {
	var data = {
		action: "wpeo-refresh-task",
		task_id: task_id,
		_wpnonce: jQuery('.wpeo-project-task-' + task_id + ' .wpeo-project-task-nonce').val(),
	};
	
	/** Refresh task */
	jQuery.post(ajaxurl, data, function(response) {
		if(!display_error( task_id, response ) ) {
			jQuery('.wpeo-project-task-' + task_id).html(response.data.template);
			
			/** Sortable point */
			jQuery(".wpeo-project-task-" + task_id + " .wpeo-task-point-sortable").sortable({
				connectWith: ".wpeo-task-point-sortable",
				items: "> li:not(.ui-state-disabled)",
				dropOnEmpty: false,
				handle: '.dashicons-menu',
				update: function ( event, ui) {
					var task_id = jQuery(this).closest('.wpeo-task-point').data('id');
					set_order ( task_id );
				}
			});
			
			jQuery(".wpeo-project-wrap textarea").flexText();
		}
	});
}

function display_error( task_id, response ) {
	if( response.state === 'success' ) {
		return false;
	}
	
	create_notification('no', 'error', response.message, undefined, undefined );
	
	return true;
}

/** This variable get some function for use it like a callback */
var callback = {
	callback_user: function( args ) {
		refresh_task( args.post_id );
		create_notification('info', 'info', args.notification_message, undefined );
	},
	callback_tag: function( args ) {
		console.log( args );
		refresh_task( args.post_id );
		create_notification('info', 'info', args.notification_message, undefined );
	},
	after_load: function() {
		jQuery('.grid').masonry();
	}
};