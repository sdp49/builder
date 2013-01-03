<?php

class PL_Admin_Nav {
	// Class vars

	// Instance vars
	public $id;
	public $sections = array();

 	public function __construct( $id, $args = array() ) {
 		// Set nav id...
 		$this->id = $id;
 	}

 	public function add_section ( $id, $section_obj ) {
 		// Make sure the section we're trying to add is an either an instance of,
 		// or extends the standard section class...
 		if ( is_a($section_obj, 'PL_Admin_Section') ) {
 			$this->settings[$id] = $section_obj;
 		}
 	}

 	public function render () {
 		// To align with the corresponding CSS...
 		$html_id = $this->id . 'Nav';
 		
 		// Sort sections by priority...


 		// Loop through sections and call their render functions...
 		?>
 		  <ul id="<?php echo esc_attr( $html_id ); ?>" class="enabled">	
      	    <?php foreach ( $this->sections as $section ) { $section->render(); } ?>
      	  </ul>
 		<?php
 	}
}

class PL_Admin_Section {
	// Class vars

	// Instance vars
	public $panes = array();
	public $css_class;
	public $priority;

 	public function __construct( $id, $args = array() ) {
 		// Set section id...
 		$this->id = $id;

 		// Take care of the other args, matching instance vars to keys...
 		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

 	}

}

class PL_Admin_Pane {
	// Class Vars

	// Instance Vars
	public $id;
	public $priority;

}


?>