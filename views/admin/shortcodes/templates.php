<?php
global $shortcode_subpages;

if(!class_exists('PL_Shortcode_Tpl_Table')){
	require_once( PL_LIB_DIR . 'shortcode-tpl-table.php' );
}


$wp_list_table = new PL_Shortcode_Tpl_Table();
$wp_list_table->prepare_items();


PL_Router::load_builder_view('header.php');

$search = (!empty($_REQUEST['s']) ? esc_attr($_REQUEST['s']) : '');

?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Templates'); ?>

	<div id="pl_template_all">

		<p>
		These are the templates currently available for use with Placester shortcodes.
		Each template is associated with a specific shortcode and can be used to style and customize the output
		generated by the shortcode.
		</p>
		<p>You can select one of these templates for use with a shortcode by setting the shortcode's
		<code>context</code> attribute to the template's ID. For example:<br/>
		<code>[search_form context=twentyeleven]</code>
		</p>

		<?php if ($search):?>
		<h2>
			<?php printf( ' <span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', $search )?>
		</h2>
		<?php endif?>

		<?php $wp_list_table->views(); ?>

		<form id="posts-filter" action="<?php echo admin_url("admin.php")?>" method="get">

		<?php $wp_list_table->search_box( 'Search Shortcode Templates', 'pl_sc_tpl' ); ?>

		<input type="hidden" name="page" class="post_page" value="placester_shortcodes_templates" />
		<input type="hidden" name="post_status" class="post_status_page" value="<?php echo !empty($_REQUEST['post_status']) ? esc_attr($_REQUEST['post_status']) : 'all'; ?>" />

		<?php $wp_list_table->display(); ?>

		</form>
	</div>

	<br class="clear" />
</div>
<?php
PL_Router::load_builder_view('footer.php');
