<?php
/**
 * Used to preview a shortcode using parameters provided in the url
 * instead of usng a shortcode post object
 */
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


function pl_template_drop_modernizr() {
	wp_dequeue_script('form');
}

function pl_template_add_modernizr() {
	wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');
	wp_enqueue_script( 'modernizr' );
}


$shortcode = (!empty($_GET['shortcode']) ? stripslashes($_GET['shortcode']) : '');
$argstr = (!empty($_GET['args']) ? stripslashes($_GET['args']) : '');
$args = json_decode($argstr, true);

if (!$shortcode || $args === false) {
	die;
}

$hash = md5($shortcode . $argstr);
$widget_cache = new PL_Cache("Embeddable_Widget_Preview");

if(0&& $widget_page = $widget_cache->get($hash) ) {
	echo $widget_page;
	return;
}

$args = array_merge(array('before_widget'=>'', 'after_widget'=>'', 'widget_css'=>''), $args);
$argstr = '';
foreach($args as $arg=>$val) {
	if ($val) {
		switch($arg) {
			case 'before_widget':
			case 'after_widget':
			case 'widget_css':
			case 'snippet_body':
				break;
			default:
				$argstr .= ' '.$arg.'="'.$val.'"';
		}
	}
}

if ($args['snippet_body']) {
	$shortcode = '['.$shortcode.$argstr.']'.$args['snippet_body'].'[/'.$shortcode.']';
}
else {
	$shortcode = '['.$shortcode.$argstr.']';
}

add_filter('show_admin_bar', '__return_false');
add_action('wp_enqueue_scripts', isset( $drop_modernizr ) ? 'pl_template_drop_modernizr': 'pl_template_add_modernizr' );

ob_start();

$html_class = '';

?><html style="margin-top: 0 !important; overflow: hidden;" <?php echo $html_class; ?>>
	<head>
		<style type="text/css">
			body {
				margin-top: 0px;
				overflow: hidden;
			}
			.pls_embedded_widget_wrapper {
				overflow: hidden;
			}
			#full-search .form-grp:first-child {
				margin-top: 0px;
			}
			.pls_embedded_widget_wrapper .pls_search_form_listings {
				margin-bottom: 0px;
			}
			p {
				margin-top: 0px;
			}
			<?php echo $args['widget_css']?>
		</style>
		<script type="text/javascript">
			var pl_general_widget = true;
		</script>
		<?php wp_head(); ?>
	</head>
	<body>
	
		<script type="text/javascript">
			jQuery(document).ready(function() {
				try {
					if (jQuery('.shortcode-link', window.parent.document).length ) {
						jQuery('.shortcode-link', window.parent.document).html('<strong>Shortcode:</strong><?php echo str_replace( "'", "\'", $shortcode ); ?>');
					}
				} catch( exception ) {}  
			});
		</script>
		<div class="pls_embedded_widget_wrapper">
			<?php
			echo $args['before_widget'];
			echo do_shortcode( $shortcode );
			echo $args['after_widget'];
			echo $shortcode;
			?>
		<div>
		
		<?php wp_footer();?>
</body>
</html>
<?php

$widget_page = ob_get_clean();
$widget_cache->save( $widget_page );

echo $widget_page;