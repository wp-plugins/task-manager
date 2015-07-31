<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<ul class="wpeo-main-tags">
	<li>
		<!-- Variables used by JS -->
		<input type=hidden class="wpeo-post-id" value="<?php echo $id; ?>" />
		<input type=hidden class="wpeo-callback-js" value="<?php echo $callback_js; ?>" />
		
			<ul class="wpeo-ul-tags wpeo-current-tags wpeo-connected-sortable">
				<?php 
				if( !empty( $array_tag_in ) ):
					foreach( $array_tag_in as $tag ) :
						?><li data-slug="<?php echo $tag; ?>"><?php echo $tag; ?></li><?php  
					endforeach;
				endif;
				?>
				
				<li class="wpeo-force-alignright wpeo-state-disabled">
					<a href="#" data-id="<?php echo $id; ?>" data-nonce="<?php echo wp_create_nonce( 'eo_nonce_view_tag' ); ?>" class="wpeo-tag-add dashicons <?php echo $dashicons; ?>"></a>
				</li>
			</ul>
	</li>
	
	<li>
		<div class="wpeo-no-display wpeo-tag-bloc wpeo-tag-point-<?php echo $id; ?>">
			
		</div>
	</li>
</ul>