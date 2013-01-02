<?php

class PL_Admin_Nav {
	// Class vars

	// Instance vars
	public $id;
	public $sections = array();

 	public function __construct( $args = array() ) {

 	}

}

class PL_Admin_Section {
	// Class vars

	// Instance vars
	public $panes = array();
	public $css_class;
	public $priority;

 	public function __construct( $args = array() ) {

 	}

}

class PL_Admin_Pane {
	// Class Vars

	// Instance Vars
	public $id;
	public $priority;
	
}


?>