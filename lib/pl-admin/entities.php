<?php

/* 
 * Base Class for all admin panel components (see classes below...) 
 */

abstract class PL_Admin_Component {
	// Class Vars

	// Instance Vars
	public $id;

	public $title;
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

 	/* MUST be implemented by any child class... */
 	abstract public function render ();

 	/* MUST be implemented by any child class... */
 	abstract public function render_content ();

 	/* Generic Component-object comparison function by priority (for uasort) */
 	protected function cmp_priority ( $a, $b ) {
		$ap = $a->priority;
		$bp = $b->priority;

		if ( $ap == $bp ) {
			return 0;
		}
		return ( $ap > $bp ) ? 1 : -1;
	}
}

/* 
 * Concrete implementations of the "Component" class above -- together they form the
 * basis for the admin panel's class/object model.
 *
 * NOTE: There is a "Has-A" relationaship betwween these -- A Nav contains Sections,
 * which in turn contain cards.
 */

class PL_Admin_Nav extends PL_Admin_Component {
	// Class Vars

	// Instance Vars
	public $sections = array();

 	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function add_section ( $section_obj ) {
 		// Make sure the section we're trying to add is an either an instance of,
 		// or extends the standard section class...
 		if ( is_a($section_obj, 'PL_Admin_Section') ) {
 			$this->sections[$section_obj->id] = $section_obj;
 		}
 	}

 	public function render () {
 		// To align with the corresponding CSS...
 		$html_id = $this->id . 'Nav';
 		
 		// Sort sections by priority...
 		uasort( $this->sections, array( $this, 'cmp_priority' ) );

 		// Loop through sections and call their render functions...
 		?>
 		  <ul id="<?php echo esc_attr( $html_id ); ?>" class="enabled">	
      	    <?php foreach ( $this->sections as $section ) { $section->render(); } ?>
      	  </ul>
 		<?php
 	}

 	public function render_content () {
 		?>
 		  <?php foreach ( $this->sections as $section ) { $section->render_content(); } ?>
 		<?php
 	}

}

class PL_Admin_Section extends PL_Admin_Component {
	// Class vars

	// Instance vars
	public $cards = array();
	public $content_uri = '';

 	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function add_card ( $card_obj ) {
 		// Make sure the card we're trying to add is an either an instance of,
 		// or extends the standard card class...
 		if ( is_a($card_obj, 'PL_Admin_Card') ) {
 			$this->cards[$card_obj->id] = $card_obj;
 		}
 	}

 	public function render () {
 		?>
 		  <li>
 		  	<a class="<?php echo esc_attr( $this->css_class ); ?>" href="<?php echo esc_url( $this->content_uri ) ?>"><?php echo esc_html( $this->title ); ?></a>
 		  </li>
 		<?php
 	}

 	/* Render the container of the cards (the pane) + the cards themselves */
 	public function render_content () {
 		// No point in rendering if no cards are present...
 		$numCards = count($this->cards);
 		if ( $numCards <= 0) { return; }

 		// Sort cards by priority...
 		uasort( $this->cards, array( $this, 'cmp_priority' ) );

 		// Get array of card objects + isolate first card and set the appropriate property...
 		$cardObjArr = array_values($this->cards);
 		list($firstCard) = $cardObjArr;

 		?>
 		  <div id="card-group-<?php echo $this->id; ?>" class="pls-inner-top card-group" style="display:none">
 		    <div class="container-fluid card-nav">
 		  	  <div class="row-fluid">
	            <div class="span12">
	              <div class="pls-head">
	                <h1><span class="curr-card-num">1</span>/<?php echo $numCards; ?>: <?php echo $firstCard->title; ?></h1>
	                <div class="pls-right">
	                  <?php for ( $i = 0; $i < $numCards; $i++ ): ?>
	                  	<?php $cardObjArr[$i]->cardNum = ($i + 1); // This is a good time to set this... ?>
	                    <a href="<?php echo esc_attr( 'card-' . $cardObjArr[$i]->id ); ?>" class="bullet <?php echo ( $i == 0 ? 'on' : 'off' ); ?>"></a>
	              	  <?php endfor; ?>
	                  <a href="#" class="button button-light-grey pls-close"><span></span></a>        
	                </div><!--pls-pagination-->      
	              </div><!--pls-head-->
	            </div>
	          </div>
 		    </div>

 		    <div class="container-fluid card-body">
 		  	  <?php foreach ( $this->cards as $card ): ?>
 		      	<?php $card->render(); ?>
 		      <?php endforeach; ?>
 		    </div>  
 		  </div>
 		<?php
 	}

}

class PL_Admin_Card extends PL_Admin_Component {
	// Class Vars

	// Instance Vars
	public $cardNum;
	public $paneCustom;

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	// Standard wrapper for all custom card content...
	public function render () {
		// Display the first card in the group (hide the others for now).
		$class = ( $this->cardNum == 1 ? 'active' : '' );

		// Custom attributes...
		$customAttr = ( empty($this->paneCustom) ? '' : "pane=\"{$this->paneCustom}\"" );
		$customAttr .= ( " card-num=\"{$this->cardNum}\"" );

		?>
		  <div id="<?php echo esc_attr( 'card-' . $this->id ); ?>" class="container-fluid <?php echo $class; ?>" <?php echo $customAttr; ?>>
		  	<?php $this->render_content(); ?>
		  </div>
		<?php
	}

	public function render_content () {
		// Defined by implementing classes...
	}
}


?>