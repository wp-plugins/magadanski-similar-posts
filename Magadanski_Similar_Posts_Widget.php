<?php
class Magadanski_Similar_Posts_Widget extends WP_Widget {
	private function get_post_types() {
		$disallowed_post_types = apply_filters('simposts_disallowed_post_types', array('attachment', 'revision', 'nav_menu_item'));
		$post_types = get_post_types(array(), 'objects');
		
		foreach ($disallowed_post_types as $dpt) {
			if (isset($post_types[$dpt])) unset($post_types[$dpt]);
		}
		
		return $post_types;
	}
	
	private function get_taxonomies($post_type = false) {
		global $wp_taxonomies;
		
		if ($post_type) {
			$tax_post_types = (array)$post_type;
		} else {
			$tax_post_types = array();
			$tax_post_types_objects = $this->get_post_types();
			
			foreach ($tax_post_types_objects as $post_type => $post_type_obj) {
				$tax_post_types[] = $post_type;
			}
		}
		
		$taxonomies = array();
		foreach ($wp_taxonomies as $tax => $tax_object) {
			if (!$tax_object->public) continue;
			if (in_array($tax, apply_filters('simposts_disallowed_taxonomies', array('post_format')))) continue;
			
			foreach ($tax_object->object_type as $object_type) {
				if (in_array($object_type, $tax_post_types)) {
					$taxonomies[$tax] = $tax_object;
					break;
				}
			}
		}
		
		return $taxonomies;
	}
	
	public function __construct() {
		parent::__construct(
			'magadanski_similar_posts_widget',
			__('Similar Posts', 'simposts'),
			array(
				'description' => __('A list of similar posts', 'simposts'),
			)
		);
	}
	
	public function widget($args, $instance) {
		if (is_single()) {
			$magadanski_similar_posts = Magadanski_Similar_Posts::get_instance();
			
			$similar_posts_entries = $magadanski_similar_posts->get_similar_posts(array('posts_per_page'=>$instance['limit'], 'taxonomy'=>$instance['taxonomy']));
			
			if ($similar_posts_entries->have_posts()) {
				$title = apply_filters('widget_title', $instance['title']);
				
				echo $args['before_widget'];
				
				if (!empty($title)) {
					echo $args['before_title'] . $title . $args['after_title'];
				}
				
				echo '<ul>';
				while ($similar_posts_entries->have_posts()) {
					$similar_posts_entries->the_post();
					$post_title = get_the_title();
					echo '<li><a href="' . get_permalink(get_the_ID()) . '" title="' . esc_attr($post_title) . '">' . $post_title . '</a></li>';
				}
				echo '</ul>';
				
				echo $args['after_widget'];
			}
			
			wp_reset_query();
		}
	}
	
	public function form($instance) {
		$magadanski_similar_posts = Magadanski_Similar_Posts::get_instance();
		$defaults = $magadanski_similar_posts->get_defaults();
		
		$title = isset($instance['title']) ? $instance['title'] : '';
		$limit = isset($instance['limit']) ? absint($instance['limit']) : $defaults['posts_per_page'];
		
		$all_post_types = $this->get_post_types();
		$current_post_type = isset($instance['post_type']) ? $instance['post_type'] : '';
		
		$all_taxonomies = $this->get_taxonomies($current_post_type);
		$current_taxonomy = isset($instance['taxonomy']) ? $instance['taxonomy'] : $all_taxonomies;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simposts'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'simposts'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_type') ?>"><?php _e('Post Type:', 'simposts'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('post_type') ?>" name="<?php echo $this->get_field_name('post_type') ?>">
				<?php
				foreach ($all_post_types as $post_type => $post_type_object) {
					$selected = $current_post_type == $post_type ? 'selected="selected"' : '';
					echo '<option value="' . esc_attr($post_type) . '" ' . $selected . '>' . $post_type_object->labels->name . '</option>';
				}
				?>
			</select>
		</p>
		
		<?php
		if (!empty($all_taxonomies)) {
			?>
			<p>
				<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy:', 'simposts'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('taxonomy') ?>" name="<?php echo $this->get_field_name('taxonomy') ?>">
					<?php
					foreach ($all_taxonomies as $tax => $tax_object) {
						$selected = $current_taxonomy == $tax ? 'selected="selected"' : '';
						echo '<option value="' . esc_attr($tax) . '" ' . $selected . '>' . $tax_object->labels->name . '</option>';
					}
					?>
				</select>
			</p>
			<?php
		}
	}
	
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = isset($new_instance['limit']) ? absint($new_instance['limit']) : $old_instance['limit'];
		
		if (isset($new_instance['post_type']) && array_key_exists($new_instance['post_type'], $this->get_post_types())) {
			$instance['post_type'] = $new_instance['post_type'];
		}
		
		if (isset($new_instance['taxonomy']) && array_key_exists($new_instance['taxonomy'], $this->get_taxonomies($instance['post_type']))) {
			$instance['taxonomy'] = $new_instance['taxonomy'];
		}
		
		return $instance;
	}
}
?>