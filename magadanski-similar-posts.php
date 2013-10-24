<?php
/**
 * Plugin Name: Magadanski Similar Posts
 * Plugin URI: https://github.com/magadanskiuchen/Magadanski-Similar-Posts
 * Description: Shows similar posts ordered by the number of common categories.
 * Version: 1.0.4
 * Author: Georgi Popov a.k.a. Magadanski_Uchen
 * Author URI: http://magadanski.com/
 * License: GPL2
 */

define('SIMPOSTS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once(SIMPOSTS_DIR . 'Magadanski_Similar_Posts_Widget.php');

class Magadanski_Similar_Posts {
	private static $instance = null;
	
	private $args = array();
	private $similar_id = 0;
	
	private function __construct() {
		add_action('plugins_loaded', array(&$this, 'init'));
	}
	
	private function __clone() {}
	
	public static function get_instance() {
		if (!self::$instance instanceof self) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	private function set_similar_id($id) {
		$id = absint($id);
		
		if ($id) {
			$this->similar_id = $id;
		} else {
			$this->similar_id = get_the_ID();
		}
	}
	
	public function init() {
		load_plugin_textdomain('simposts', false, basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages');
		
		add_action('widgets_init', array(&$this, 'widgets_init'));
		
		add_shortcode('magadanski-similar-posts', array(&$this, 'shortcode'));
	}
	
	public function widgets_init() {
		register_widget('Magadanski_Similar_Posts_Widget');
	}
	
	public function get_defaults() {
		return array(
			'post_type' => 'post',
			'posts_per_page' => 5,
			'taxonomy' => 'category',
			'no_found_rows' => true
		);
	}
	
	public function get_similar_posts($args = array(), $similar_id = 0) {
		if ($similar_id) {
			$this->set_similar_id($similar_id);
		}
		
		$this->args = wp_parse_args($args, $this->get_defaults());
		
		$this->add_query_filters();
		$similar_posts = new WP_Query($this->args);
		$this->remove_query_filters();
		
		return $similar_posts;
	}
	
	private function add_query_filters() {
		add_filter('posts_request', array(&$this, 'posts_request'));
		add_filter('posts_join', array(&$this, 'posts_join'));
		add_filter('posts_where', array(&$this, 'posts_where'));
		add_filter('posts_groupby', array(&$this, 'posts_groupby'));
		add_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	private function remove_query_filters() {
		remove_filter('posts_request', array(&$this, 'posts_request'));
		remove_filter('posts_join', array(&$this, 'posts_join'));
		remove_filter('posts_where', array(&$this, 'posts_where'));
		remove_filter('posts_groupby', array(&$this, 'posts_groupby'));
		remove_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	public function posts_request($request) {
		global $wpdb;
		
		$request = preg_replace('/SELECT ([^(FROM)]+) FROM/', 'SELECT $1, COUNT(`' . $wpdb->posts . '`.`post_title`) AS `simposts_connections` FROM', $request);
		
		return $request;
	}
	
	public function posts_join($join) {
		global $wpdb;
		
		$join .= "
			INNER JOIN `$wpdb->term_relationships` AS `simposts_term_rel` ON (`simposts_term_rel`.`object_id` = `$wpdb->posts`.`ID`)
			INNER JOIN `$wpdb->term_taxonomy` AS `simposts_term_tax` ON (`simposts_term_tax`.`term_taxonomy_id` = `simposts_term_rel`.`term_taxonomy_id`) ";
		
		return $join;
	}
	
	public function posts_where($where) {
		global $wpdb;
		
		$where .= "
			AND `simposts_term_rel`.`term_taxonomy_id` IN (
				SELECT `term_rel`.`term_taxonomy_id`
				FROM `$wpdb->term_relationships` AS `term_rel`
				WHERE `term_rel`.`object_id` = {$this->similar_id}
			)
			AND `simposts_term_tax`.`taxonomy` = '{$this->args['taxonomy']}'
			AND `$wpdb->posts`.`ID` != {$this->similar_id} ";
		
		return $where;
	}
	
	public function posts_groupby($groupby) {
		global $wpdb;
		
		return " `$wpdb->posts`.`post_title` ";
	}
	
	public function posts_orderby($orderby) {
		return " `simposts_connections` DESC ";
	}
	
	public function shortcode($atts) {
		$output = '';
		
		extract(shortcode_atts(array(
			'id' => get_the_ID(),
			'post_type' => 'post',
			'taxonomy' => 'category',
			'limit' => 5,
		), $atts));
		
		$this->set_similar_id($id);
		
		$similar_posts = $this->get_similar_posts(array('post_type'=>$post_type, 'taxonomy'=>$taxonomy, 'posts_per_page'=>absint($limit), 'no_found_rows'=>true));
		
		if ($similar_posts->have_posts()) {
			ob_start();
			
			echo '<ul>';
			while ($similar_posts->have_posts()) {
				$similar_posts->the_post();
				$post_title = get_the_title();
				echo '<li><a href="' . get_permalink(get_the_ID()) . '" title="' . esc_attr($post_title) . '">' . $post_title . '</a></li>';
			}
			echo '</ul>';
			
			$output = ob_get_clean();
		}
		
		wp_reset_query();
		
		return $output;
	}
}

$magadanski_similar_posts = Magadanski_Similar_Posts::get_instance();

?>