jQuery( document ).ready( function( ){       // The click event for the gallery manage button

	// Uploading files
	var file_frame;

	jQuery( '.wpeo-project-wrap' ).on( "click", ".wpeo-upload-media", function( e ) {
		e.preventDefault();

	    // If the media frame already exists, reopen it.
	    if ( file_frame ) {
	      // Set the post ID to what we want
	      file_frame.uploader.uploader.param( 'post_id', jQuery( this ).data( 'id' ) );
	      // Open frame
	      file_frame.open();
	      return;
	    }
	    else {
	      // Set the wp.media post id so the uploader grabs the ID we want when initialised
	      wp.media.model.settings.post.id = jQuery( this ).data( 'id' );
	    }

	    // Create the media frame.
	    file_frame = wp.media.frames.file_frame = wp.media({
	    	title: jQuery( this ).data( 'uploader_title' ),
	    	button: {
	    		text: jQuery( this ).data( 'uploader_button_text' ),
	    	},
	    	multiple: true  // Set to true to allow multiple files to be selected
	    });

	    // When an image is selected, run a callback.
	    file_frame.on( 'select', function() {
	    	var selection = file_frame.state().get('selection');
	    	var files_to_associate = new Array;
	    	selection.map( function( attachment ) {
	    		attachment = attachment.toJSON();
	    		files_to_associate.push( attachment.id );
	    	});

	    	/** Alex associate files ajax */
	    	var data = {
	    		action: "wpeofiles-associate-files",
	    		files_to_associate: files_to_associate,
	    		element_id: file_frame.uploader.uploader.param( 'post_id' ),
	    	};
	    	jQuery.post( ajaxurl, data, function( response ) {
	    		jQuery( '.wpeo-project-task-' + file_frame.uploader.uploader.param( 'post_id') ).find( '.wpeomtm-attached-file-container' ).append( response.data.template );
	    		jQuery( '.wpeo-project-task-' + file_frame.uploader.uploader.param( 'post_id' ) ).find( '.wpeomtm-no-result-msg' ).hide();
	    		
	    		if(typeof create_notification == 'function') {
	    			create_notification( 'info', 'info', response['message'], undefined, undefined );
	    		}
	    	});
	    	
	    	/** For render */
//	    	var data = {
//	    		action: "wpeo-template-file",
//	    		files_to_associate: files_to_associate,
//	    		element_id: file_frame.uploader.uploader.param( 'post_id' ),
//	    	};
//	    	jQuery.post( ajaxurl, data, function( response ) {
//	    		
//	    	});
	    });
	    
	    
	    // Finally, open the modal on click
	    file_frame.open();

	});
	

	/**	Trigger event on delete file button	*/
	jQuery( document ).on( "click", ".wpeofiles-action-button-container .dashicons-trash", function( event ){
		event.preventDefault();

		if ( confirm( wpeofile_confirm_file_dissociation ) ) {
			var current_file_line = jQuery( this ).closest( "li" );
			var current_file = current_file_line.attr( "class" ).replace( "wpeofiles-associated-item-", "" );

			var data = {
				action: "wpeofiles-delete-association-file",
				file_id: current_file,
				element_id: jQuery(this).closest('.wpeo-project-task').find(".wpeo-files-associated-element-id" ).val(),
			};
			jQuery.post( ajaxurl, data, function( response ){
				if ( response[ 'status' ] ) {
					current_file_line.remove();
					
					if(typeof create_notification == 'function') {
		    			create_notification( 'info', 'info', response['message'], undefined, undefined );
		    		}
				}
			}, 'json');
		}

	});
} );