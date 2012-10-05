<?php

/**
 * Entity functions to be used for shortcodes/widgets/frames
 *
 */

class PL_Component_Entity {

	public static function featured_listings_entity( $atts ) {
		$atts = wp_parse_args($atts, array('limit' => 5, 'featured_id' => 'custom'));
		ob_start();
		echo pls_get_listings( $atts );
		return ob_get_clean();
	}
	
	public static function search_listings_entity( $atts ) {
		ob_start();
		?>
			  	<script type="text/javascript">
				  	if (typeof bootloader !== 'object') {
						var bootloader;
					}
				  jQuery(document).ready(function( $ ) {
		
				  	if (typeof bootloader !== 'object') {
				  		bootloader = new SearchLoader();
				  		bootloader.add_param({list: {context: "shortcode"}});
				  	} else {
				  		bootloader.add_param({list: {context: "shortcode"}});
				  	}
				  });
				</script>
		
		
			  	<?php
			    PLS_Partials_Get_Listings_Ajax::load(array('context' => 'shortcode'));
			  return ob_get_clean();  
	}
	
	public static function search_map( $atts ) {
		ob_start();
	?>
	 <script type="text/javascript">
    	jQuery(document).ready(function( $ ) {
    		
    		var map = new Map (); 
    		// var filter = new Filters ();
    		var listings = new Listings ({
    			map: map
    			// filter: filter,
    		});
            
            var status = new Status_Window ({map: map, listings:listings});
            
            map.init({
                // type: 'lifestyle',
                // type: 'lifestyle_polygon',
                // type: 'neighborhood',
                type: 'listings',
                // lifestyle: lifestyle,
                listings: listings,
                // lifestyle_polygon: lifestyle_polygon,
                status_window: status
            });

    		listings.init();
    		
    	});
    </script>

	<?php
	    echo PLS_Map::listings( null, array('width' => 600, 'height' => 400) );
	  	return ob_get_clean();  
	}
}