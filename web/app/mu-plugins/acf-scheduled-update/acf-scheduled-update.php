<?php
/**
 * Plugin Name: Farlo UI | ACF Scheduled Update
 * Description: A Farlo UI plugin to schedule updates to ACF fields.
 * Version: 0.9
 * Author: Farlo
 * Author URI: farlo.co.uk
 */

 defined('ABSPATH') || exit;

 if (class_exists('ACF')) {
	// Add ACF Options page as a sub-item under 'Tools'
	function register_acf_options_page() {
		if( function_exists('acf_add_options_sub_page') ) {
			acf_add_options_sub_page(array(
				'page_title' => 'ACF Updater',
				'menu_title' => 'ACF Updater',
				'parent_slug' => 'tools.php',
				'capability' => 'manage_options',
			));
		}
	}
	add_action('acf/init', 'register_acf_options_page');

	// Add a hook to update fields at the specified time
	add_action('init', 'schedule_acf_field_updates');
	function schedule_acf_field_updates() {
		$updates = get_field('update_item', 'option');

		if ($updates) {
			foreach ($updates as $update) {
				$update_time = $update['update_time'];
				$sub_field = $update['update_sub_field'];
				$sub_field_ID = $update['sub_field_ID'];
				$update_timestring = strtotime($update_time);
				$update_field = $update['update_field'];
				$field_id = $update['ID_field'];
				$current_time = current_time('timestamp');
				$page_ID = $update['page_id'];
				// Compare the update time with the current time
				if ($current_time >= $update_timestring) {
					// Update the ACF field value based on the field type
					if ($sub_field && $sub_field_ID) {
						$newvalues = array($field_id => $update_field);
						switch ($update['field_type']) {
						case 'text':
							update_field($sub_field_ID, $newvalues, $page_ID ?: 'option');
							break;
						case 'textarea':
							update_field($sub_field_ID, $newvalues, $page_ID ?: 'option');
							break;
						case 'link':
							// Assuming the link field is stored as an array
							update_field($sub_field_ID, $newvalues, $page_ID ?: 'option');
							break;
						case 'true/false':
							update_field($sub_field_ID, $newvalues, $page_ID ?: 'option');
							break;
						case 'WYSIWYG Editor':
							update_field($sub_field_ID, $newvalues, $page_ID ?: 'option');
							break;
						}
					} else {
						switch ($update['field_type']) {
						case 'text':
							update_field($field_id, $update_field, $page_ID ?: 'option');
							break;
						case 'textarea':
							update_field($field_id, $update_field, $page_ID ?: 'option');
							break;
						case 'link':
							// Assuming the link field is stored as an array
							update_field($field_id, $update_field, $page_ID ?: 'option');
							break;
						case 'true/false':
							update_field($field_id, $update_field, $page_ID ?: 'option');
							break;
						case 'WYSIWYG Editor':
							update_field($field_id, $update_field, $page_ID ?: 'option');
							break;
						}
					}
				}
			}
		}
	}

	add_action('acf/include_fields', function() {
		if (!function_exists('acf_add_local_field_group')) {
			return;
		}

		acf_add_local_field_group(array(
			'key' => 'group_6462049577bd7',
			'title' => 'ACF Field Update',
			'fields' => array(
				array(
					'key' => 'field_64620495cc62f',
					'label' => 'Update Item',
					'name' => 'update_item',
					'aria-label' => '',
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'block',
					'pagination' => 0,
					'min' => 0,
					'max' => 0,
					'collapsed' => '',
					'button_label' => 'Add Row',
					'rows_per_page' => 20,
					'sub_fields' => array(
						array(
							'key' => 'field_646204b1cc630',
							'label' => 'Field ID',
							'name' => 'ID_field',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64621cccac5ad',
							'label' => 'Update Sub Field',
							'name' => 'update_sub_field',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64621e3cf246a',
							'label' => 'Sub Field ID',
							'name' => 'sub_field_ID',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_64621cccac5ad',
										'operator' => '==',
										'value' => '1',
									),
								),
							),
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_646204cacc631',
							'label' => 'Field Type',
							'name' => 'field_type',
							'aria-label' => '',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'text' => 'Text',
								'textarea' => 'Text Area',
								'link' => 'Link',
								'true/false' => 'True/False',
								'WYSIWYG Editor' => 'WYSIWYG Editor',
							),
							'default_value' => false,
							'return_format' => 'value',
							'multiple' => 0,
							'allow_null' => 0,
							'ui' => 0,
							'ajax' => 0,
							'placeholder' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64620510cc632',
							'label' => 'Update Field Value',
							'name' => 'update_field',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_646204cacc631',
										'operator' => '==',
										'value' => 'text',
									),
								),
							),
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_6462053ccc633',
							'label' => 'Update Field Value',
							'name' => 'update_field',
							'aria-label' => '',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_646204cacc631',
										'operator' => '==',
										'value' => 'textarea',
									),
								),
							),
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'rows' => '',
							'placeholder' => '',
							'new_lines' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64620560cc634',
							'label' => 'Update Field Value',
							'name' => 'update_field',
							'aria-label' => '',
							'type' => 'link',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_646204cacc631',
										'operator' => '==',
										'value' => 'link',
									),
								),
							),
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'array',
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64620579cc635',
							'label' => 'Update Field Value',
							'name' => 'update_field',
							'aria-label' => '',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_646204cacc631',
										'operator' => '==',
										'value' => 'true/false',
									),
								),
							),
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
							'ui' => 1,
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64620592cc636',
							'label' => 'Update Field Value',
							'name' => 'update_field',
							'aria-label' => '',
							'type' => 'wysiwyg',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_646204cacc631',
										'operator' => '==',
										'value' => 'WYSIWYG Editor',
									),
								),
							),
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'tabs' => 'all',
							'toolbar' => 'full',
							'media_upload' => 1,
							'delay' => 0,
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_646205b7cc637',
							'label' => 'Update Time',
							'name' => 'update_time',
							'aria-label' => '',
							'type' => 'date_time_picker',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'display_format' => 'd/m/Y g:i a',
							'return_format' => 'Y-m-d H:i:s',
							'first_day' => 1,
							'parent_repeater' => 'field_64620495cc62f',
						),
						array(
							'key' => 'field_64623d4549fd9',
							'label' => 'Page ID',
							'name' => 'page_id',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => 'Leave blank if you\'re targeting a field on Site Settings',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'maxlength' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'parent_repeater' => 'field_64620495cc62f',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options-acf-updater',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		) );
	} );
}
