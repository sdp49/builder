/*
 * Onboarding Wizard global object -- contains each stage's data + state information
 */

var wizard_global = {
  	states: {
  		welcome: {
  			header: 'Welcome!',
  			content: 'Great!  You\'re making all the right moves.  We\'re going to take you into the main admin panel now so you can further customize your web site.<br />'
  					  + '<br />You can always return to this customization wizard by clicking Appearance in the main menu, then clicking "Customize."',
  			link: 'Let\'s Get Started',
  			left: '39%',
  			top: '36%',
  			next_state: 'theme'
  		},
  		theme: {
  			header: '1. Theme Selection',
  			content: '',
  			link_text: 'Select a Theme',
  			left: '75px',
  			top: '50px',
  			next_state: 'title'
  		},
  		title: {
  			header: '2. Slogan & Title',
  			content: 'Add a Title',
  			link_text: '',
  			left: '75px',
  			top: '100px',
  			next_state: 'colors'
  		},
  		colors: {
  			header: '3. Colors & Style',
  			content: '',
  			link_text: 'Customize your Theme',
  			left: '75px',
  			top: '150px',
  			next_state: 'brand'
  		},
  		brand: {
  			header: '4. Upload Logo',
  			content: '',
  			link_text: 'Upload my Logo',
  			left: '75px',
  			top: '200px',
  			next_state: 'mls'
  		},
  		mls:  {
  			header: '5. MLS Integration',
  			content: '',
  			link_text: 'Integrate with your MLS',
  			left: '75px',
  			top: '250px',
  			next_state: 'listing'
  		},
  		listing: {
  			header: '6. Post a Listing',
  			content: '',
  			link_text: 'Post my First Listing',
  			left: '75px',
  			top: '300px',
  			next_state: 'post'
  		},
  		post: {
  			header: '7. Make a Blog Post',
  			content: '',
  			link_text: 'Make a Post',
  			left: '75px',
  			top: '350px',
  			next_state: 'analytics'
  		},
  		analytics: {
  			header: '8. Analytics',
  			content: '',
  			link_text: 'Integrate with Google',
  			left: '75px',
  			top: '400px',
  			next_state: 'confirm'
  		},
  		confirm: {
  			header: 'Save your Changes',
  			content: 'Alright, all done for now -- you can view these customization options in the future by visiting Appearance -> Customize from the admin panel',
  			link_text: 'View my Site',
  			left: '75px',
  			top: '450px',
  			next_state: ''
  		}
  	},
  	initial_state: 'welcome', 
  	active_state: 'welcome', // Set to initial value...
    previewLoaded: function () {
      if ( window.location.href.indexOf('theme=changed') == -1 ) {
        // Kick things off by loading the initial state...
        jQuery('#full-overlay').prepend('<div id="welcome-overlay"></div>');

        wiz = this;
        jQuery('#welcome-overlay').fadeIn(500, function () {
          loadState(wiz.initial_state);
        });
      }
      else {
        this.active_state = 'theme';
        moveToNextState();
        loadState(this.active_state);
      }    
    }
  }


/*
 * Onboarding global functions
 */

function loadState (state) {
  var tooltip = jQuery('#tooltip');

  // Retrieve associated state object...
  var stateObj = wizard_global.states[state];
  
  // Populate tooltip w/given state's copy... (no need to do this for initial state, rendered in response)
  if ( state != wizard_global.initial_state ) {
    tooltip.find('h4').text(stateObj.header);
    tooltip.find('p.desc').html(stateObj.content);
    tooltip.find('.link a').text(stateObj.link_text);
  }
  
  // Position tooltip + make sure it is visible...
  tooltip.css('top', stateObj.top);
  tooltip.css('left', stateObj.left);
  tooltip.show();
}

function moveToNextState () {
  var currStateObj = wizard_global.states[wizard_global.active_state];
  wizard_global.active_state = currStateObj.next_state;
}

function openStatePane () {
  console.log('in openStatePane...');

  // Just mimic related menu-item click...
  jQuery('#' + wizard_global.active_state).trigger('click');

  // Set active state to the next state + hide the tooltip...
  moveToNextState();
  jQuery('#tooltip').hide();
}

/*
 * Onboarding Wizard actions + flow
 */

jQuery(document).ready(function($) {
  // Append "next/skip" to existing panes...
  $('.control-container').prepend('<a class="wizard-next" href="#">Move to Next Step</a>');

  // Main tooltip element...
  var tooltip = $('#tooltip');

	// Bind main action of clicking tooltip link...
	$('#tooltip .link a').on('click', function (event) {
		event.preventDefault();

		// Initial state's link has been clicked...
		if ( wizard_global.active_state == wizard_global.initial_state ) {
      // Get rid of welcome overlay & hide tooltip
      tooltip.hide();
      $('#welcome-overlay').remove();

      // Insert menu overlay (to prevent clicking other menu items directly...)
      if ( $('#menu-overlay').length == 0 ) {
        $('#menu-nav').prepend('<div id="menu-overlay"></div>');
        $('#menu-overlay').on('click', function () { 
          // If a pane is not already open (i.e., tooltip IS visible), move open active state's pane...
          if ( tooltip.css('display') != 'none' ) {
            openStatePane();
          }
          else {
            tooltip.show();
          }
        });
      }

      // Tack on tooltip display elements needed going forward...
      tooltip.addClass('arrow');
      tooltip.find('a.close').show();

      //  Bring the tooltip back into focus with the next state loaded...
			moveToNextState();
      loadState(wizard_global.active_state);
		}
		else {
      // console.log('Here!');
		  openStatePane();	
		}
	});

  // Handle close tooltip close...
  $('#tooltip a.close').on('click', function (event) {
    event.preventDefault();
    tooltip.hide();
  });

  // Load the next state...
  $('a.wizard-next').on('click', function (event) {
    // Mimic hide-pane functionality...
    $('#logo').trigger('click');

    // Load the activate state (which was bumped to next when the last pane appeared)...
    loadState(wizard_global.active_state);
  });

  // Detect any submission/input button clicks from inside the pane...
  // $('.control-container input[type=button]').on('click', function (event) {

  // });

});