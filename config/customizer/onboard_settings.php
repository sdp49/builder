<?php

global $PL_CUSTOMIZER_ONBOARD_SECTIONS;
$PL_CUSTOMIZER_ONBOARD_SECTIONS = array(
                                  'Placester Logo' => 1,
								  'Theme Selection' => 10,
								  'Title & Slogan' => 20,
								  'Colors & Style' => 30,
								  'MLS Integration' => 40,
                                  'Upload Logo' => 50,
								  'Post a Listing' => 60,
								  'Write a Blog Post' => 70,
                                  // 'Invite Your Friends' => 80,
								  'Analytics & Webmasters' => 90,
                                  'Save & Continue' => 1000
							   );

global $PL_CUSTOMIZER_ONBOARD_OPTS;
$PL_CUSTOMIZER_ONBOARD_OPTS = array( 

        array(
            'name' => 'Placester Logo',
            'id' => 'logo',
            'type' => 'heading',
            'class' => 'no-pane'
        ),    

        array(
            'name' => 'Theme Selection',
            'desc' => 'Select the Right Look-and-Feel',
            'id' => 'theme',
            'type' => 'heading'
        ),

        array(
            'name' => 'theme-select',
            'type' => 'custom'
        ),

        array(
            'name' => 'Title & Slogan',
            'desc' => 'Let Your Customers Know Who You Are',
            'id' => 'title',
            'type' => 'heading'
        ),

        array(
            'name' => 'Site Title',
            'desc' => 'Site title in header.',
            'id' => 'pls-site-title',
            'type' => 'text'
        ),

        array(
            'name' => 'Tagline',
            'desc' => 'Site subtitle in header.',
            'id' => 'pls-site-subtitle',
            'type' => 'text'
        ),

        // array(
        //     'name' => 'Contact Info',
        //     'type' => 'info'
        // ),

        array(
            'name' => 'First and Last Name',
            'desc' => 'Add the name you want to display on the site.',
            'id' => 'pls-user-name',
            'type' => 'text'
        ),

        array(
            'name' => 'Your Email Address',
            'desc' => 'Add the email address you want to display on the site.',
            'id' => 'pls-user-email',
            'type' => 'text'
        ),

        array(
            'name' => 'Your Phone Number',
            'desc' => 'Add the phone you want to display on the site.',
            'id' => 'pls-user-phone',
            'type' => 'text'
        ),

        array(
            'name' => 'Your Bio',
            'desc' => 'Add your bio that you want to display on the site.',
            'id' => 'pls-user-description',
            'type' => 'textarea'
        ),

        // array(
        //     'name' => 'Your Headshot',
        //     'desc' => 'Add your headshot that you want to display on the site.',
        //     'id' => 'pls-user-image',
        //     'type' => 'upload'
        // ),

        array(
            'name' => 'Colors & Style',
            'desc' => 'Customize Your Site Even More',
            'id' => 'colors',
            'type' => 'heading'
        ),

        // array(
        //     'name' => 'Site Background',
        //     'desc' => 'Change the site\'s background.',
        //     'id' => 'site_background',
        //     'selector' => 'body',
        //     'type' => 'background'
        // ),

        // array(
        //     'name' => 'Inner Background',
        //     'desc' => 'Change the site\'s inner background.',
        //     'id' => 'inner_background',
        //     'selector' => '.inner',
        //     'type' => 'background'
        // ),

        // array(
        //     'name' => 'H1 Title',
        //     'desc' => 'Change main site title\'s size, font-family, styling, and color.',
        //     'id' => 'h1_title',
        //     'selector' => 'header h1 a',
        //     'type' => 'typography'
        // ),

        // array(
        //     'name' => 'H2 Subtitle',
        //     'desc' => 'Change the site subtitle\'s size, font-family, styling, and color.',
        //     'id' => 'h2_subtitle',
        //     'selector' => 'header h2',
        //     'type' => 'typography'
        // ),       

        array(
            'name' => 'Upload Logo',
            'desc' => 'Display Your Brand',
            'id' => 'brand',
            'type' => 'heading'
        ),

        array(
            'name' => 'Site Logo',
            'desc' => 'Upload your logo here. It will appear in the header and will override the title you\'ve provided above.',
            'id' => 'pls-site-logo',
            'type' => 'upload'
        ),

        array(
            'name' => 'MLS Integration',
            'desc' => 'Integrate Your Listing Data',
            'id' => 'mls',
            'type' => 'heading'
        ),

        array(
            'name' => 'integration',
            'type' => 'custom'
        ), 

        array(
            'name' => 'Post a Listing',
            'desc' => 'Create Your Own Custom Listings to Display',
            'id' => 'listing',
            'type' => 'heading'
        ),

        array(
            'name' => 'post-listing',
            'type' => 'custom'
        ),   

        array(
            'name' => 'Write a Blog Post',
            'desc' => 'Start Creating Content for Visitors',
            'id' => 'post',
            'type' => 'heading'
        ),

        array(
            'name' => 'blog-post',
            'type' => 'custom'
        ),   

        array(
            'name' => 'Analytics & Webmasters',
            'desc' => 'Authorize Placester with Google',
            'id' => 'analytics',
            'type' => 'heading'
        ),

        array(
            'name' => 'Google Analytics Tracking Code',
            'desc' => 'Add your google analytics tracking ID code here. It looks something like this: UA-XXXXXXX-X',
            'id' => 'pls-google-analytics',
            'type' => 'text'
        ),

        array(
            'name' => 'Save & Continue',
            'id' => 'confirm',
            'type' => 'heading',
            'class' => 'no-pane'
        )

    );

?>