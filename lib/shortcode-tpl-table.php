<?php
/**
 * Construct a table to manage custom shortcode templates
 */
class PL_Shortcode_Tpl_Table extends WP_List_Table {

	private $base_page;
	private $per_page = 20;
	protected $_column_headers = array();
	private static $_items = array();
	private static $_status = array('in_use'=>0, 'inactive'=>0, 'built_in'=>0);

	public function __construct( $args = array() ) {
		global $pagenow;

		parent::__construct($args);

		$page = $_REQUEST['page'];
		$this->base_page = $pagenow.'?page='.$page;

		$this->_column_headers = array(
			array(
					//'cb' 		=> '<input type="checkbox" />',
					'title'		=> 'Name',
					'shortcode'	=> 'For Shortcode',
					'id'		=> 'Template ID',
			),
			array(),
			array(
					'title' => array('title', ''),
					'shortcode' => array('shortcode', ''),
			),
		);
		$this->items = array();

		// make sure the sort indicator is set
		if (empty($_GET['orderby'])) {
			$_GET['orderby'] = 'title';
			$_GET['order'] = 'asc';
		}
	}

	/**
	 * Static function to fetch and hold list of templates
	 * @return array:
	 */
	private static function _fetch_items() {
		if (empty(self::$_items)) {
			$sc_attr = PL_Shortcode_CPT::get_shortcode_attrs();
			$shortcodes = PL_Shortcode_CPT::get_shortcode_list();
			foreach($shortcodes as $shortcode=>$inst) {
				$sc_tpls = PL_Shortcode_CPT::template_list($shortcode);
				$shortcode_name = $sc_attr[$shortcode]['title'];
				foreach($sc_tpls as $sc_tpl) {
					$status = $sc_tpl['type']=='custom' ? (PL_Shortcode_CPT::template_in_use($sc_tpl['id']) ? 'in_use' : 'inactive') : 'built_in';
					self::$_status[$status]++;
					self::$_items[] = array_merge($sc_tpl, array('shortcode'=>$shortcode, 'shortcode_name'=>$shortcode_name, 'status'=>$status));
				}
			}
		}
		return self::$_items;
	}

	public function prepare_items() {

		$search = (!empty($_REQUEST['s']) ? esc_attr($_REQUEST['s']) : '');
		$status = (!empty($_REQUEST['status']) ? esc_attr($_REQUEST['status']) : '');
		$orderby = empty($_GET['orderby']) ? 'title' : $_GET['orderby'];
		$order = empty($_GET['order']) ? 'asc' : $_GET['order'];
		$order = $order=='asc' ? 'asc' : 'desc';

		// get data
		$this->items = $this->_fetch_items();
		foreach($this->items as $key=>$item) {
			if (($status && $item['status']!=$status)
				|| ($search && strpos($item['title'], $search)===false)) {
				unset($this->items[$key]);
			}
		}

		// sort
		if ($orderby == 'title') {
			uasort($this->items, array($this, $order=='asc' ? 'sort_by_title_asc' : 'sort_by_title_desc'));
		}
		else {
			uasort($this->items, array($this, $order=='asc' ? 'sort_by_type_asc' : 'sort_by_type_desc'));
		}

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

	public static function sort_by_title_asc($a, $b) {
		return strcasecmp($a['title'], $b['title']);
	}

	public static function sort_by_title_desc($a, $b) {
		return -strcasecmp($a['title'], $b['title']);
	}

	public static function sort_by_type_asc($a, $b) {
		$cmp = strcasecmp($a['shortcode_name'], $b['shortcode_name']);
		if ($cmp != 0) return $cmp;
		return strcasecmp($a['title'], $b['title']);
	}

	public static function sort_by_type_desc($a, $b) {
		$cmp = strcasecmp($a['shortcode_name'], $b['shortcode_name']);
		if ($cmp != 0) return -$cmp;
		return -strcasecmp($a['title'], $b['title']);
	}

	public function no_items() {
		return "No shortcode templates found.";
	}

	public function get_views() {
		$status_links = array();

		$count = count($this::$_items);
		$class = empty($_REQUEST['s']) && empty($_REQUEST['status']) ? ' class="current"' : '';
		$status_links['all'] = '<a href="'.$this->base_page.'"'.$class.'>'.sprintf(_nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'templates'), number_format_i18n($count)).'</a>';

		if ($this::$_status['in_use']) {
			$class = !empty($_REQUEST['status']) && $_REQUEST['status']=='in_use' ? ' class="current"' : '';
			$status_links['in_use'] = '<a href="'.$this->base_page.'&status=in_use"'.$class.'>'.sprintf(_nx('In Use <span class="count">(%s)</span>', 'In Use <span class="count">(%s)</span>', $this::$_status['in_use'], 'templates'), number_format_i18n($this::$_status['in_use'])).'</a>';
		}

		if ($this::$_status['inactive']) {
			$class = empty($class) && !empty($_REQUEST['status']) && $_REQUEST['status']=='inactive' ? ' class="current"' : '';
			$status_links['inactive'] = '<a href="'.$this->base_page.'&status=inactive"'.$class.'>'.sprintf(_nx('Not In Use <span class="count">(%s)</span>', 'Not In Use <span class="count">(%s)</span>', $this::$_status['inactive'], 'templates'), number_format_i18n($this::$_status['inactive'])).'</a>';
		}

		if ($this::$_status['built_in']) {
			$class = empty($class) && !empty($_REQUEST['status']) && $_REQUEST['status']=='builtin' ? ' class="current"' : '';
			$status_links['built_in'] = '<a href="'.$this->base_page.'&status=built_in"'.$class.'>'.sprintf(_nx('Built In <span class="count">(%s)</span>', 'Built In <span class="count">(%s)</span>', $this::$_status['built_in'], 'templates'), number_format_i18n($this::$_status['built_in'])).'</a>';
		}

		return $status_links;
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
						if ($template['status'] == 'in_use') {
							$actions['delete'] =  __( 'In Use' );
						}
						else {
							$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . $delete_link . $template['id'] . "'>" . __( 'Delete Permanently' ) . "</a>";
						}
					}
					else {
						$actions['edit'] = __('Non-editable built-in template.');
					}
					echo $this->row_actions( $actions, true );
					?>
					</td>
					<?php
					break;

				case 'shortcode':
					?>
					<td <?php echo $attributes ?>><strong><?php echo $template['shortcode_name']?></strong><br/><?php echo '['.$template['shortcode'].']'?></td>
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
