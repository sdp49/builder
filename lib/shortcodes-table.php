<?php
/**
 * Construct a table to manage custom shortcode templates
 */
class PL_Shortcodes_List_Table extends WP_List_Table {

	private $post_type = 'pl_general_widget';
	private $base_page;
	private $per_page = 20;
	protected $_column_headers = array();
	private $shortcode_types = array();
	
	public function __construct( $args = array() ) {
		global $pagenow, $wpdb;
		
		// set screen to this post type
		$screen = get_current_screen();
		$screen->id = 'edit-'.$this->post_type;
		$page = $_REQUEST['page'];
		$this->base_page = $pagenow.'?page='.$page;
		
		$args = wp_parse_args($args, array(
				'plural' => 'posts',	// for nonce since we manage the records using wp-admin/edit.php
				'screen' => $screen,
		));
		
		parent::__construct($args);
		
		$this->items = array();
		$sc_attrs = PL_Shortcode_CPT::get_shortcodes();
		foreach($sc_attrs as $sc=>$attrs) {
			$this->shortcode_types[$sc] = $attrs['title'];
		} 
	}

	public function ajax_user_can() {
		return current_user_can( get_post_type_object( $this->post_type )->cap->edit_posts );
	}

	public function prepare_items() {
		global $wp_query, $per_page, $mode, $avail_post_stati;

		$q = $_GET;
		$q['post_type'] = $this->post_type;
		$avail_post_stati = wp_edit_posts_query($q);
		
		$total_items = $wp_query->found_posts;

		$post_type = $this->post_type;
		$per_page = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' );
 		$per_page = apply_filters( 'edit_posts_per_page', $per_page, $post_type );
		$total_pages = $wp_query->max_num_pages;

		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash';
		$this->_column_headers = array(
				array(
						'cb' 		=> '<input type="checkbox" />',
						'title'		=> 'Name',
						'type'		=> 'Shortcode Type',
						'shortcode'	=> 'Shortcode',
				),
				array(),
				array('title' => array('title', 'asc')),
		);
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page
		) );
	}
	
	public function sort_by_type($a, $b) {
		$cmp = strcmp($a['shortcode'], $b['shortcode']);
		if ($cmp != 0) return $cmp;
		return strcasecmp($a['title'], $b['title']);
	}

	public function sort_by_name($a, $b) {
		$cmp = strcasecmp($a['title'], $b['title']);
		if ($cmp != 0) return $cmp;
		return strcmp($a['shortcode'], $b['shortcode']);
	}

	public function has_items() {
		return have_posts();
	}

	public function no_items() {
		if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] )
			echo get_post_type_object( $this->post_type )->labels->not_found_in_trash;
		else
			echo get_post_type_object( $this->post_type )->labels->not_found;
	}

	public function get_views() {
		global $locked_post_status;

		$avail_post_stati = array('trash');
		$post_type = $this->post_type;
		$base_page = $this->base_page;

		if ( !empty($locked_post_status) )
			return array();

		$status_links = array();
		$num_posts = wp_count_posts( $post_type, 'readable' );
		$class = '';
		$allposts = '';

		$total_posts = array_sum( (array) $num_posts );

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='$base_page{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='$base_page&post_status=$status_name'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}

		return $status_links;
	}
	
	public function get_bulk_actions() {
		$actions = array();

		if ( $this->is_trash )
			$actions['untrash'] = __( 'Restore' );

		if ( $this->is_trash || !EMPTY_TRASH_DAYS )
			$actions['delete'] = __( 'Delete Permanently' );
		else
			$actions['trash'] = __( 'Move to Trash' );

		return $actions;
	}

	public function extra_tablenav( $which ) {
		global $cat;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() ) {
//			do_action( 'restrict_manage_posts', $this->post_type);
//			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
		}

		if ( $this->is_trash && current_user_can( get_post_type_object( $this->post_type )->cap->edit_posts ) ) {
			submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
		}
?>
		</div>
<?php
	}

	public function display_rows( $posts = array() ) {
		global $mode, $wp_query, $per_page;

		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		add_filter( 'the_title', 'esc_html' );

		foreach ( $posts as $post )
			$this->single_row( $post );
	}
	
	public function single_row( $a_post ) {
		global $post, $mode;
		static $alternate;
		
		$global_post = $post;
		$post = $a_post;
		
		setup_postdata( $post );
		
		$title = _draft_or_post_title();
		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );
		$post_meta = get_post_meta( $post->ID);
		$shortcode = empty($post_meta['shortcode']) ? '' : $post_meta['shortcode'][0];
		$shortcode_type = $shortcode && !empty($this->shortcode_types[$shortcode]) ? $this->shortcode_types[$shortcode] : 'Unknown Shortcode Type';
		$shortcode_str = '['.$shortcode." id='".$post->ID."']";
		
		$alternate = 'alternate' == $alternate ? '' : 'alternate';
		$classes = $alternate;
		
		?>
		<tr id="sc-shortcode-<?php echo $post->ID; ?>" class="<?php echo $classes;?> sc-shortcode-<?php echo $post_meta['shortcode']?>" valign="top">
		<?php
		
		list( $columns, $hidden ) = $this->get_column_info();
		
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
						<?php if ( $can_edit_post ) { ?>
						<label class="screen-reader-text" for="cb-select-<?php the_ID(); ?>"><?php printf( __( 'Select %s' ), $title ); ?></label>
						<input id="cb-select-<?php the_ID(); ?>" type="checkbox" name="post[]" value="<?php the_ID(); ?>" />
						<?php } ?>
					</th>
					<?php
					break;
	
				case 'title':
					?>
					<td <?php echo $attributes ?>><strong><?php echo $title?></strong>
					<?php
					$actions = array();
					if ( $can_edit_post && 'trash' != $post->post_status ) {
						$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
					}
					if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
						if ( 'trash' == $post->post_status )
							$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-'.$this->post_type.'_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
						elseif ( EMPTY_TRASH_DAYS )
							$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
						if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
							$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
					}
					echo $this->row_actions( $actions );
					?>
					</td>
					<?php
					break;
	
				case 'type':
					?>
					<td <?php echo $attributes ?>><?php echo $shortcode_type?></td>
					<?php
					break;
	
				case 'shortcode':
					?>
					<td <?php echo $attributes ?>><?php echo $shortcode_str;?></td>
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
