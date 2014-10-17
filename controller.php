<?php

class JPM_Controller extends JPM {

	function __construct() {
		$this->plugin_info();
	}

	public function create_options_page() {
		add_options_page('Simple Page Meta Options', 'Page Meta', 'manage_options', self::SLUG, array($this, "output_options"));
	}

	public function output_options() {
		$this->settings = new JPM_Settings("settings-schema");
		$this->maybe_process();
		$this->settings->display("settings-template");
	}

	private function maybe_process() {
		if (empty($_POST)) {
			return false;
		}

		check_admin_referer(self::SLUG);
		
		$this->settings->update($_POST);
	}

	function enqueue_options_scripts($hook) {
		if ( self::is_public_edit_page() || self::is_settings_page() ) {
			wp_enqueue_style( 'jpm_css', self::$plugin_url . '/assets/css/plugin.css' );

			wp_register_script("jpm_js", self::$plugin_url . "/assets/js/plugin.js", array( "jquery", "wp-color-picker"));
			wp_localize_script("jpm_js", "plugin_root", self::$plugin_url);
			wp_enqueue_script("jpm_js");			
		}
	}

/* ==========================================================================
	Custom Stuff -- Move this to other files?
	========================================================================= */

/* ==========================================================================
	Settings
	========================================================================= */

	public function add_post_type_settings($schema) {
		$types = get_post_types(array(
			"public"   => true,
		), "objects");		

		unset($types["attachment"]);

		foreach ($types as $name => $data) {
			$schema["og-type-defaults"]["og-type-$name"] = array(
				"label"   => $data->label,
				"type"    => "text",
				"classes" => array(
					"label" => "label-column",
				),				
			);
		}

		return $schema;
	}


/* ==========================================================================
	Meta Boxes
	========================================================================= */

	public function display_meta_box() {
		if (self::is_public_edit_page()) {
			add_meta_box( "page-meta", "Page Meta", array($this, "meta_cb") );
		}
	}

	public function save_meta_box() {
		global $post_id;
		if (!self::is_public_edit_page()
		|| !self::should_save(self::SLUG."-meta", self::SLUG) 
		|| !self::user_can_save() 
		|| self::is_autosaving() ) {
			return;
		}

		$title = sanitize_text_field( $_POST['meta-title'] );
		$desc  = sanitize_text_field( $_POST['meta-desc'] );
		self::save_post_meta( $post_id, "meta-title", $title );
		self::save_post_meta( $post_id, "meta-desc", $desc );
	}	

	public function meta_cb() {
		$schema = new JPM_Schema("meta-schema");
		$schema->the_template("meta-template");
	}

	public function output_meta_tags() {
		$meta = new JPM_Meta();
		echo $meta->generate();	
	}


}



