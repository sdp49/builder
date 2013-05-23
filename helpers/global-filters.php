<?php 

PL_Global_Filters::init();
class PL_Global_Filters {
	
	public static function init() {
		add_action( 'wp_ajax_user_save_global_filters', array(__CLASS__, 'set_global_filters') );
		add_action( 'wp_ajax_user_remove_all_global_filters', array(__CLASS__, 'remove_all_global_filters') );
	}

	public static function merge_global_filters ($args) {
		
		// comes back as an associative array. 
		//false if empty.
		$global_filters = self::get_global_filters();

	    if (is_array($global_filters)) {
	  		foreach ($global_filters as $attribute => $value) {
	  			// Special handling for property type, comes in as property_type-{type} since it differs on listing_type
	  			if (strpos($attribute, 'property_type') !== false ) {
	  				$args['property_type'] = is_array($value) ? implode('', $value) : $value;
	  			} else if ( is_array($value) ) {
	  				//this whole thing basically traverses down the arrays for global filters
	  				
	  				foreach ($value as $k => $v) {
	  				  // Check to see if this value is already set

	  				  if ( empty($args[$attribute][$k]) && !is_array($v) ) {
	  				  	// sometimes $value is an array, but we actually want to implode it. 
	  				  	// Like non_import and other boolean fields.
	  				  	if (is_int($k) & count($k) > 1) {
	  				  		$args[$attribute] = self::handle_boolean_values($v);
	  				  	} else {
	  				  		$args[$attribute][$k] = self::handle_boolean_values($v);
	  				  	}
	  					
		  			  } elseif ( empty($args[$attribute][$k]) && is_array($v) ) {
		  			  	if (is_int($k) & count($k) > 1) {
	  				  		$args[$attribute][$k] = self::handle_boolean_values(implode('',$v));
	  				  	} else {
			  			  	$args[$attribute][$k] = self::handle_boolean_values($v);
	  				  	}
		  			  }
	  				}
	  			} 
	  			else {
					$args[$attribute] = self::handle_boolean_values($value);
	  			}
	  		}
	    }
	    // pls_dump($args);
	    return $args;
	}

	function report_filters () {
		$response = PL_WordPress::set(array_merge(self::get_global_filters(), array('url' => site_url() ) ) );
		return $response;
	}


	//updates boolean values so they are
	//properly respected by rails.
	private static function handle_boolean_values ($value) {
		if ($value === 'true') {
			return 1;
		} elseif ($value === 'false') {
			return 0;
		} else {
			return $value;
		}
	}

	function display_global_filters () {
		$filters = self::get_global_filters();
		// pls_dump($filters);
		$html = '';
		if (!empty($filters)) {
			foreach ($filters as $key => $filter) {
				if (is_array($filter)) {
					foreach ($filter as $subkey => $item) {
						if (!is_array($item)) {
							if ($item == 'in') { continue; }
							$label = is_int($subkey) ? $key : $key . '-' . $subkey;
							$value = $item;
							$name = $key . '['.$subkey.']=';
							ob_start();
							?>
								<span id="active_filter_item">
									<a href="#"  id="remove_filter"></a>
									<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
									<input test="true" type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
								</span>
							<?php
							$html .= ob_get_clean();
						} else {
							foreach ($item as $k => $value) {
								if ($value == 'in') { continue; }
								$label = is_int($subkey) ? $key : $key . '-' . $subkey;
								$value = $value;
								$name = $key . '['.$subkey.'][]=';
								ob_start();
								?>
									<span id="active_filter_item">
										<a href="#"  id="remove_filter"></a>
										<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
										<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
									</span>
								<?php
								$html .= ob_get_clean();
							}
						}
					}
				}
			}
		}
		echo $html;
	}

	public static function filter_options () {
		$option_name = 'pl_my_listings_filters';
		$options = get_option($option_name);
		if (isset($_POST['filter']) && isset($_POST['value']) && $options) {
			$options[$_POST['filter']] = $_POST['value'];
			update_option($option_name, $options);
		} elseif (isset($_POST['filter']) && isset($_POST['value']) && !$options) {
			$options = array($_POST['filter'] => $_POST['value']);
			add_option($option_name, $options);
		}
		echo json_encode($options);
		die();
	}

	public static function get_listing_attributes() {
		$options = array();

		$attributes = PL_Config::PL_API_LISTINGS('get', 'args');

		$form_types = PL_Config::PL_API_CUST_ATTR('get');
		$form_types = $form_types['args']['attr_type']['options'];

		if (isset($attributes['custom']) && is_array($attributes['custom'])) {
			$custom_attributes = call_user_func( array($attributes['custom']['bound']['class'], $attributes['custom']['bound']['method'] ) );
							
			foreach ($custom_attributes as $key => $option) {
				$attributes[$option['cat']][] = array('label' => $option['name'], 'type' => $form_types[$option['attr_type']] );
			} 

			unset($attributes['custom']);

			// pls_dump('custom_attributes',$response);
		}
		// pls_dump($attributes);
		foreach ($attributes as $key => $attribute) {
			if ( isset($attribute['label']) ) {
				$options['basic'][$key] = $attribute['label'];
			} else {
				foreach ($attribute as $k => $v) {
					if (isset( $v['label'])) {
						if (is_int($k)) {
							$options[$key][self::generate_global_filter_key_from_value($v['label'])] = $v['label'];
						} else {
							$options[$key][$k] = $v['label'];
						}
						
					}
				}
			}
		}
		// pls_dump($attributes);
		// pls_dump($options);
		$option_html = '';
		foreach ($options as $group => $value) {
			ob_start();
			?>
			<optgroup label="<?php echo ucwords($group) ?>">
				<?php foreach ($value as $value => $label): ?>
					<option value="<?php echo $value ?>"><?php echo $label ?></option>
				<?php endforeach ?>
			</optgroup>
			<?php
			$option_html .= ob_get_clean();
		}

		$option_html = '<select id="selected_global_filter">' . $option_html . '</select>';
		echo $option_html;
	}

	/*
	 * Functionality for Global Filters
	 */

	public static function remove_all_global_filters() {
		$response = PL_Option_Helper::set_global_filters(array('filters' => array()));
		if ($response) {
			echo json_encode(array('result' => true, 'message' => 'You successfully removed all global search filters'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'Change not saved or no change detected. Please try again.'));
		}
		die();
	}

	public static function get_global_filters() {
		$response = PL_Option_Helper::get_global_filters();
		return $response;
	}

	public static function set_global_filters ($args = array()) {
		if (empty($args) ) {
			unset($_POST['action']);
			$args = $_POST;
		}
		
		$global_search_filters = PL_Validate::request($args, PL_Config::PL_API_LISTINGS('get', 'args'));
		foreach ($global_search_filters as $key => $filter) {
			foreach ($filter as $subkey => $subfilter) {
				if (!is_array($subfilter) && (count($filter) > 1) ) {
					$global_search_filters[$key . '_match'] = 'in';
				} elseif (count($subfilter) > 1) {
					$global_search_filters[$key][$subkey . '_match'] = 'in';
				}
			}
		}
		$response = PL_Option_Helper::set_global_filters(array('filters' => $global_search_filters));
		if ($response) {
			echo json_encode(array('result' => true, 'message' => 'You successfully updated the global search filters'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'Change not saved or no change detected. Please try again.'));
		}
		echo json_encode(self::report_filters());
		die();
	}

	private static function generate_global_filter_key_from_value ($value) {
		$value = str_replace(' ', '_', $value);
		$value = str_replace('.', '', $value);
		$value = str_replace('-', '', $value);
		$value = strtolower($value);
		return $value;
	}


}