<?php

/*
Plugin Name: Simple Page Meta
Plugin URI: http://www.thejakegroup.com/wordpress
Description: A custom theme developed by The Jake Group for your Wordpress Admin Site.
Author: Tyler Bruffy
Version: 1.0
Author URI: hhttp://www.thejakegroup.com/wordpress/
*/

class JPM {
	const DB_VERSION    = "1.0.0";
	const ABBREV        = "JPM";
	const PREFIX        = "jpm_";
	const SLUG          = "jpm_options";

	public static $plugin_url;
	public static $plugin_path;

	function __construct() {
		$this->include_files();
		
		$this->control = new JPM_Controller();	
		
		$this->add_hooks();
		$this->add_filters();
	}

/* ==========================================================================
	Activation
	========================================================================= */

	public function activate() {

	}

/* ==========================================================================
	Deactivation
	========================================================================= */

	public static function deactivate() {

	}

/* ==========================================================================
	Uninstall
	========================================================================= */

	public static function uninstall() {

	}

/* ==========================================================================
	Hooks
	========================================================================= */

	public function add_hooks() {
		register_activation_hook(__FILE__,    array($this, "activate") );
		register_deactivation_hook(__FILE__,  array("JPM", "deactivate") );
		register_uninstall_hook(__FILE__,     array("JPM", "uninstall") );

		add_action("admin_menu",              array($this->control, "create_options_page"));
		add_action("admin_enqueue_scripts",   array($this->control, "enqueue_options_scripts"));
	
		add_action("add_meta_boxes",          array($this->control, "display_meta_box") );
		add_action("save_post",               array($this->control, "save_meta_box") );

		add_action("wp_head",                 array($this->control, "output_meta_tags") );
	}

/* ==========================================================================
	Filters
	========================================================================= */

	public function add_filters() {
		add_filter("wp_title",              array("JPM_Meta", "set_title"), 100, 2 );

		add_filter("JPM/schema",            array($this->control, "add_post_type_settings"), 10, 1 );
	}

/* ==========================================================================
	Includes
	========================================================================= */

	public function include_files() {
		require_once("controller.php");
		require_once("settings_controller.php");
		require_once("schema.php");
		require_once("meta.php");
	}

/* ==========================================================================
	General
	========================================================================= */

	public static function plugin_info() {
		self::$plugin_path = plugin_dir_path(__FILE__);
		self::$plugin_url  = plugins_url('', __FILE__);
	}

	public static function apply_filters($name, $var) {
		return apply_filters(self::ABBREV . "/$name", $var);
		return $var;
	}

	public static function notice($message, $class, $extra = "") {
		return sprintf(
			'<div id="setting-error-settings_updated" class="%s settings-error">
				<p><strong>%s</strong> %s</p>
			</div>',
			$class,
			$message,
			$extra
		);
	}

/* ==========================================================================
	Database Interaction
	========================================================================= */

	protected function get_option( $option ) {
		return get_option( self::PREFIX.$option );
	}

	protected function set_option( $option, $value ) {
		$value = $value ? $value : 0;
		return update_option( self::PREFIX . $option, $value );
	}

	protected function set_default( $option, $value ) {
		if (!self::get_option($option)) {
			return update_option( self::PREFIX . $option, $value );
		}
		return;
	}

	protected function delete_option( $option ) {
		return delete_option( self::PREFIX.$option );
	}

	public static function save_post_meta( $post, $name, $value ) {
		return update_post_meta($post, self::PREFIX.$name, $value);
	}

	public static function get_post_meta( $post, $name, $single = true ) {
		return get_post_meta($post, self::PREFIX.$name, $single);
	}

/* ==========================================================================
	File System Interaction
	========================================================================= */

	public static function upload_dir($index = "url") {
		$dir_array = wp_upload_dir();
		return $dir_array[$index] . "/" . self::ABBREV . "/";
	}

	public static function require_dir( $path ) {
		foreach (glob(self::$plugin_path . $path . "/*.php") as $filename) {
			require_once($filename);
		}
	}

	public static function activate_file_system() {
		$url = wp_nonce_url('options-general.php?page='.self::SLUG, self::SLUG);
		
		if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
			return "There was an error with your FTP/Filesystem credentials, please try again"; 
		}

		if ( !WP_Filesystem($creds) ) {
			request_filesystem_credentials($url, '', true, false, null);
			return "There was an error with your FTP/Filesystem credentials, please try again";
		}

		global $wp_filesystem;
		return $wp_filesystem;
	}

/* ==========================================================================
	Conditional Checks
	========================================================================= */

	public static function is_settings_page() {
		global $plugin_page;
		return isset($plugin_page) && $plugin_page === self::SLUG;
	}

	public static function is_edit_page($new_edit = null){
		global $pagenow;

		if ($new_edit == "edit")
			return in_array( $pagenow, array( 'post.php',  ) );
		elseif($new_edit == "new") //check for new post page
			return in_array( $pagenow, array( 'post-new.php' ) );
		else //check for either new or edit
			return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}

	public static function is_public_edit_page() {
		global $typenow;
		return self::is_edit_page() && self::is_public_post_type($typenow);
	}

	public static function is_public_post_type($type) {
		$data = get_post_type_object($type);
		if (!is_object($data)) {
			return false;
		}
		return $data->public;
	}

/* ==========================================================================
	Conditional Checks -- Saving
	========================================================================= */

	public function is_autosaving() {
		return (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE);
	}

	public function user_can_save() {
		global $post_id;
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return false;
			}
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) ) {
				return false;
			}
		}
		return true;
	}

	public static function should_save($nonce_name, $context) {
		if ( !isset( $_POST[$nonce_name] )
		|| !wp_verify_nonce( $_POST[$nonce_name], $context ) ) {
			return false;
		}

		return true;
	}

/* ==========================================================================
	Getters
	========================================================================= */

	public static function get_post_id() {
		global $post;
		return $post->ID;
	}

}


new JPM();
