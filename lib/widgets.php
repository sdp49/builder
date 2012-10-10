<?php

/**
 *  Include all widgets from the widgets directory
 */

add_action( 'widgets_init', 'pl_register_widgets' );

function pl_register_widgets() {
	include PL_PARENT_DIR . "/lib/pl_widget.php";
	
	foreach (glob( PL_PARENT_DIR . "/lib/widgets/*widget.php") as $filename) {
		include_once $filename;
	}
}
