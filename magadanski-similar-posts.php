<?php
/**
 * Plugin Name: Magadanski Similar Posts
 * Plugin URI: https://github.com/magadanskiuchen/Magadanski-Similar-Posts
 * Description: Shows similar posts ordered by the number of common categories.
 * Version: 1.0.5
 * Author: Georgi Popov a.k.a. Magadanski_Uchen
 * Author URI: http://magadanski.com/
 * License: GPL2
 */

define('MSP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once(MSP_DIR . 'Magadanski_Similar_Posts_Widget.php');

/**
 * Plugin Singleton Class
 * 
 * @since 1.0
 */
class Magadanski_Similar_Posts {
	/**
	 * Singleton instance holder
	 * 
	 * @since 1.0
	 * @access private
	 * @var Magadanski_Similar_Posts
	 */
	private static $instance = null;
	
	/**
	 * Query arguments store
	 * 
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	private $args = array();
	
	/**
	 * Holder for current/query post ID
	 * 
	 * @since 1.0
	 * @access private
	 * @var int
	 */
	private $similar_id = 0;
	
	/**
	 * Plugin constructor
	 * 
	 * There should be only a single instance of the class. Therefor the constructor method is private.
	 * Use Magadanski_Similar_Posts::get_instance() to obtain access to the class' only instance.
	 * @since 1.0
	 * @access private
	 * @see Magadanski_Similar_Posts::get_instance()
	 * @return Magadanski_Similar_Posts
	 */
	private function __construct() {
		add_action('plugins_loaded', array(&$this, 'init'));
	}
	
	/**
	 * Class cloning is forbidden in order to keep just a single instance.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function __clone() {}
	
	/**
	 * Provides access to the class' instance
	 * 
	 * @since 1.0
	 * @access public
	 * @return Magadanski_Similar_Posts
	 */
	public static function get_instance() {
		if (!self::$instance instanceof self) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Setter for the $similar_id property
	 * 
	 * @since 1.0
	 * @access private
	 * @param int $id the ID to be stored
	 * @return void
	 */
	private function set_similar_id($id) {
		$id = absint($id);
		
		if ($id) {
			$this->similar_id = $id;
		} else {
			$this->similar_id = get_the_ID();
		}
	}
	
	/**
	 * Plugin initialization. Called on the plugins_loaded hook
	 * 
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain('msp', false, basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages');
		
		add_action('widgets_init', array(&$this, 'widgets_init'));
		
		add_shortcode('magadanski-similar-posts', array(&$this, 'shortcode'));
	}
	
	/**
	 * Plugin widget initialization. Called on the widgets_init hook.
	 * 
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function widgets_init() {
		register_widget('Magadanski_Similar_Posts_Widget');
	}
	
	/**
	 * Plugin default query parameters
	 * 
	 * @since 1.0
	 * @access public
	 * @return array The default options for the plugin when making a query
	 */
	public function get_defaults() {
		return array(
			'post_type' => 'post',
			'posts_per_page' => 5,
			'taxonomy' => 'category',
			'no_found_rows' => true
		);
	}
	
	/**
	 * Main plugin functionality method.
	 * 
	 * Easily called from other plugins and/or themes. Allows you to hook to the plugin's functionality.
	 * @since 1.0
	 * @access public
	 * @param array $args Query parameters that will later on be passed to WP_Query. The parameters can be compatible. Terms from the `taxonomy` parameter will be used to determine similarity
	 * @param int $similar_id = 0 The ID of the post you'd like to get similar for. By default this will use the current post's ID.
	 * @return WP_Query Object containing similar posts.
	 */
	public function get_similar_posts($args = array(), $similar_id = 0) {
		if ($similar_id) {
			$this->set_similar_id($similar_id);
		}
		
		$this->args = wp_parse_args($args, $this->get_defaults());
		
		// attach necessary filters
		$this->add_query_filters();
		$similar_posts = new WP_Query($this->args);
		
		// remove filters not to affect other queries
		$this->remove_query_filters();
		
		return $similar_posts;
	}
	
	/**
	 * Method to add custom query filters.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function add_query_filters() {
		add_filter('posts_request', array(&$this, 'posts_request'));
		add_filter('posts_join', array(&$this, 'posts_join'));
		add_filter('posts_where', array(&$this, 'posts_where'));
		add_filter('posts_groupby', array(&$this, 'posts_groupby'));
		add_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	/**
	 * Method to remove custom query filters.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function remove_query_filters() {
		remove_filter('posts_request', array(&$this, 'posts_request'));
		remove_filter('posts_join', array(&$this, 'posts_join'));
		remove_filter('posts_where', array(&$this, 'posts_where'));
		remove_filter('posts_groupby', array(&$this, 'posts_groupby'));
		remove_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	/**
	 * Posts request modification filder
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_request($request) {
		global $wpdb;
		
		// use regular expression to modify "SELECT" statement, as no filter is available for that
		$request = preg_replace('/SELECT ([^(FROM)]+) FROM/', 'SELECT $1, COUNT(`' . $wpdb->posts . '`.`post_title`) AS `msp_connections` FROM', $request);
		
		return $request;
	}
	
	/**
	 * Query join filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_join($join) {
		global $wpdb;
		
		$join .= "
			INNER JOIN `$wpdb->term_relationships` AS `msp_term_rel` ON (`msp_term_rel`.`object_id` = `$wpdb->posts`.`ID`)
			INNER JOIN `$wpdb->term_taxonomy` AS `msp_term_tax` ON (`msp_term_tax`.`term_taxonomy_id` = `msp_term_rel`.`term_taxonomy_id`) ";
		
		return $join;
	}
	
	/**
	 * Query where filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_where($where) {
		global $wpdb;
		
		$where .= "
			AND `msp_term_rel`.`term_taxonomy_id` IN (
				SELECT `term_rel`.`term_taxonomy_id`
				FROM `$wpdb->term_relationships` AS `term_rel`
				WHERE `term_rel`.`object_id` = {$this->similar_id}
			)
			AND `msp_term_tax`.`taxonomy` = '{$this->args['taxonomy']}'
			AND `$wpdb->posts`.`ID` != {$this->similar_id} ";
		
		return $where;
	}
	
	/**
	 * Query groupby filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_groupby($groupby) {
		global $wpdb;
		
		return " `$wpdb->posts`.`post_title` ";
	}
	
	/**
	 * Query orderby filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_orderby($orderby) {
		return " `msp_connections` DESC ";
	}
	
	/**
	 * Plugin shortcode method
	 * 
	 * @since 1.0.3
	 * @access public
	 * @return string
	 */
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