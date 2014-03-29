<?php
/**
 * Return valid post types
 * 
 * The result would be the built-in post types, except for attachment, revision and nav_menu_items (should result in just post and page).
 * Additionally this will include any custom post types.
 * A `msp_disallowed_post_types` filter is available to exclude other post types or allow one of the blocked by default.
 * @since 1.0
 * @return mixed An array of post-type objects
 */
function msp_get_post_types() {
	// by default do not include
	$disallowed_post_types = apply_filters('msp_disallowed_post_types', array('attachment', 'revision', 'nav_menu_item'));
	$post_types = get_post_types(array(), 'objects');
	
	foreach ($disallowed_post_types as $dpt) {
		if (isset($post_types[$dpt])) unset($post_types[$dpt]);
	}
	
	return $post_types;
}

/**
 * Return valid taxonomies
 * 
 * If the `$post_type` parameter is used the taxonomies will be filtered for this post type only.
 * The `post_format` taxonomy will be skipped.
 * The `msp_disallowed_taxonomies` filter is available to block any other taxonomies.
 * @since 1.0
 * @param string $post_type The key for the post type to retrieve taxonomies for.
 * @return mixed An array of taxonomies for the requested post-type
 */
function msp_get_taxonomies($post_type = false) {
	global $wp_taxonomies;
	
	if ($post_type) {
		$tax_post_types = (array)$post_type;
	} else {
		// if no $post_type is provided -- get all post types
		$tax_post_types = array();
		$tax_post_types_objects = msp_get_post_types();
		
		foreach ($tax_post_types_objects as $post_type => $post_type_obj) {
			$tax_post_types[] = $post_type;
		}
	}
	
	$taxonomies = array();
	foreach ($wp_taxonomies as $tax => $tax_object) {
		// skip private taxonimies
		if (!$tax_object->public) continue;
		
		// skip blocked taxonomies
		if (in_array($tax, apply_filters('msp_disallowed_taxonomies', array('post_format')))) continue;
		
		foreach ($tax_object->object_type as $object_type) {
			if (in_array($object_type, $tax_post_types)) {
				$taxonomies[$tax] = $tax_object;
				break;
			}
		}
	}
	
	return $taxonomies;
}

?>