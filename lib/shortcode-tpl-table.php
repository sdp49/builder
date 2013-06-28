<?php
/**
 * Construct a table to manage custom shortcode templates
 */
class PL_Shortcode_Tpl_Table extends WP_List_Table {

	private $per_page = 20;
	protected $_column_headers = array();
	
	public function __construct( $args = array() ) {
		parent::__construct($args);
		
		$this->_column_headers = array(
				array(
//						'cb' 		=> '<input type="checkbox" />',
						'title'		=> 'Name',
						'shortcode'	=> 'For Shortcode',
						'id'		=> 'Template ID',
				),
				array(),
				array(),
			);
		$this->items = array();
	}

	public function prepare_items() {

		// get data
		$shortcodes = PL_Shortcode_CPT::get_shortcode_list();
		foreach($shortcodes as $shortcode=>$inst) {
			$sc_tpls = PL_Shortcode_CPT::template_list($shortcode);
			foreach($sc_tpls as $sc_tpl) {
				$this->items[] = array_merge($sc_tpl, array('shortcode'=>$shortcode));
			}
		}
		
		// sort
		uasort($this->items, array($this, 'sort_by_type'));

		// get page counts
		$total_items = count($this->items);
		$total_pages = ceil($total_items / $this->per_page);
		
		$this->set_pagination_args(array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page' => $this->per_page
			));
		
		// paginate the results
		$page = $this->get_pagenum();
		$page--;
		if ($page >= 0 && $page < $total_pages) {
			$this->items = array_slice($this->items, $page*$this->per_page, $this->per_page);
		}
	}
	
	public function sort_by_type($a, $b) {
		$cmp = strcmp($a['shortcode'], $b['shortcode']);
		if ($cmp != 0) return $cmp;
		return strcasecmp($a['title'], $b['title']);
	}

	public function no_items() {
		return "No shortcode templates found.";
	}

	public function get_bulk_actions() {
		$actions = array();
		return $actions;
	}

	public function display_rows( $templates = array() ) {
		global $per_page;

		if (empty($templates)) {
			$templates = $this->items;
		}
		
		add_filter( 'the_title', 'esc_html' );

		// Create array of post IDs.
		$post_ids = array();

		foreach ( $templates as $id=>$template ) {
			$this->single_row( $id, $template );
		}
	}

	public function single_row( $id, $template ) {

		?>
		<tr id="sc-template-<?php echo $id; ?>" class="sc-template-<?php echo $template['type']?>" valign="top">
		<?php
		
		list( $columns, $hidden ) = $this->get_column_info();
		
		$edit_link = admin_url('admin.php?page=placester_shortcodes_template_edit&action=edit&id=');
		$delete_link = admin_url('admin.php?page=placester_shortcodes_template_edit&action=delete&id=');
		
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) ) {
				$style = ' style="display:none;"';
			}

			$attributes = "$class$style";

			switch ( $column_name ) {

				case 'cb':
					?>
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="cb-select-<?php $id; ?>"><?php printf( __( 'Select %s' ), $template['title'] ); ?></label>
						<input id="cb-select-<?php $id; ?>" type="checkbox" name="post[]" value="<?php $id; ?>" />
					</th>
					<?php
					break;
	
				case 'title':
					?>
					<td <?php echo $attributes ?>><strong><?php echo $template['title']?></strong>
					<?php
					$actions = array();
					if ($template['type'] == 'custom') {
						$actions['edit'] = '<a href="' . $edit_link . $template['id'] . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
						if (PL_Shortcode_CPT::template_in_use($template['id'])) {
							$actions['delete'] =  __( 'In Use' );
						}
						else {
							$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . $delete_link . $template['id'] . "'>" . __( 'Delete Permanently' ) . "</a>";
						}
					}
					else {
						echo ' (built-in)';
						$actions['edit'] = __('Non-editable built-in template.');
					}
					echo $this->row_actions( $actions );
					?>
					</td>
					<?php
					break;
	
				case 'shortcode':
					?>
					<td <?php echo $attributes ?>><?php echo $template['shortcode']?></td>
					<?php
					break;
	
				case 'id':
					?>
					<td <?php echo $attributes ?>><?php echo $template['id']?></td>
					<?php
					break;
	
				default:
					break;
			}

		}
		?>
		</tr>
		<?php
	}
}
