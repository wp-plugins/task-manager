<!-- The list of tag -->
<?php 
if( !empty( $array_tag ) ) :
	?><ul class="wpeo-ul-tags wpeo-adding-tags wpeo-connected-sortable"><?php
	foreach( $array_tag as $tag ) :
		if( !in_array( $tag->slug, is_array( $array_tag_in ) ? $array_tag_in : array() ) ):
			?><li class="wpeo-item-tag" data-slug="<?php echo $tag->slug; ?>" ><?php echo $tag->name; ?></li><?php 
		endif;
	endforeach;
	?></ul><?php 
endif; 
?>
