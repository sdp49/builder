<?php 

PL_Global_Filters::init();
class PL_Global_Filters {
	
	public static function init() {
		
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

	private static function generate_global_filter_key_from_value ($value) {
		$value = str_replace(' ', '_', $value);
		$value = str_replace('.', '', $value);
		$value = str_replace('-', '', $value);
		$value = strtolower($value);
		return $value;
	}


}