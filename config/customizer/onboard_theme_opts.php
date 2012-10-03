<?php

global $PL_CUSTOMIZER_ONBOARD_OPTS;
$PL_CUSTOMIZER_ONBOARD_OPTS = array( 

        array(
            'name' => 'Title & Logo',
            'type' => 'heading'
        ),

        array(
            'name' => 'Site Title',
            'desc' => 'Site title in header.',
            'id' => 'pls-site-title',
            'type' => 'text'
        ),

        array(
            'name' => 'Site Subtitle',
            'desc' => 'Site subtitle in header.',
            'id' => 'pls-site-subtitle',
            'type' => 'text'
        ),

        array(
            'name' => 'Site Logo',
            'desc' => 'Upload your logo here. It will appear in the header and will override the title you\'ve provided above.',
            'id' => 'pls-site-logo',
            'type' => 'upload'
        ),

        array(
            'name' => 'Contact Info',
            'type' => 'heading'
        ),

        array(
            'name' => 'Your First and Last Name',
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

        array(
            'name' => 'Your Headshot',
            'desc' => 'Add your headshot that you want to display on the site.',
            'id' => 'pls-user-image',
            'type' => 'upload'
        ),

        array(
            'name' => 'Company Info',
            'type' => 'heading'
        ),

        array(
            'name' => 'Company Name',
            'desc' => 'Add your company\'s name you want to display on the site.',
            'id' => 'pls-company-name',
            'type' => 'text'
        ),

        array(
            'name' => 'Company Phone',
            'desc' => 'Add your company\'s phone number you want to display on the site.',
            'id' => 'pls-company-phone',
            'type' => 'text'
        ),

        array(
            'name' => 'Company Email',
            'desc' => 'Add your company\'s email address you want to display on the site.',
            'id' => 'pls-company-email',
            'type' => 'text'
        ),

        array(
            'name' => 'Company Street Address',
            'desc' => 'Add your company\'s street address you want to display on the site.',
            'id' => 'pls-company-street',
            'type' => 'text'
        ),

        array(
            'name' => 'Company City / Locality',
            'desc' => 'Add your company\'s city/locality location you want to display on the site.',
            'id' => 'pls-company-locality',
            'type' => 'text'
        ),

        array(
            'name' => 'Company State / Region',
            'desc' => 'Add your company\'s state/region location you want to display on the site.',
            'id' => 'pls-company-region',
            'type' => 'text'
        ),

        array(
            'name' => 'Company Postal Code',
            'desc' => 'Add your company\'s postal code you want to display on the site.',
            'id' => 'pls-company-postal',
            'type' => 'text'
        ),

        array(
            'name' => 'Company Description',
            'desc' => 'Add your company\'s description you want to display on the site.',
            'id' => 'pls-company-description',
            'type' => 'textarea'
        ),

        array(
            'name' => 'Styles & Coloring',
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

        array(
            'name' => 'H1 Title',
            'desc' => 'Change main site title\'s size, font-family, styling, and color.',
            'id' => 'h1_title',
            'selector' => 'header h1 a',
            'type' => 'typography'
        ),

        array(
            'name' => 'H2 Subtitle',
            'desc' => 'Change the site subtitle\'s size, font-family, styling, and color.',
            'id' => 'h2_subtitle',
            'selector' => 'header h2',
            'type' => 'typography'
        ),

        array(
            'name' => 'Analytics',
            'type' => 'heading'
        ),

        array(
            'name' => 'Google Analytics Tracking Code',
            'desc' => 'Add your google analytics tracking ID code here. It looks something like this: UA-XXXXXXX-X',
            'id' => 'pls-google-analytics',
            'type' => 'text'
        )

        );
?>