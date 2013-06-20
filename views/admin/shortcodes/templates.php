<?php
global $shortcode_subpages;

if(!class_exists('PL_SC_Templates_List_Table')){
	require_once( PL_LIB_DIR . 'templates-table.php' );
}


$wp_list_table = new PL_SC_Templates_List_Table();
$wp_list_table->prepare_items();


PL_Router::load_builder_view('header.php');
?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Templates'); ?>
 
	<div id="pl_shortcode_all">
	 
		<?php
		if ( isset( $_REQUEST['locked'] ) || isset( $_REQUEST['updated'] ) || isset( $_REQUEST['deleted'] ) || isset( $_REQUEST['trashed'] ) || isset( $_REQUEST['untrashed'] ) ) {
			$messages = array();
		}
		?>
	 
		<?php $wp_list_table->views(); ?>
	 
		<form id="posts-filter" action="edit.php" method="get">
	 
		<?php $wp_list_table->display(); ?>
	 
		</form>
	</div>
 
	<br class="clear" />
</div>
<?php
PL_Router::load_builder_view('footer.php');
