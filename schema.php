<?php

class JPM_Schema extends JPM {
	public $html         = "";
	public $fields       = array();
	public $field_groups = array();
	public $updated      = false;
	public $error        = false;
	public $notice       = "";

	function __construct($schema) {
		$this->plugin_info();
		
		require_once(self::$plugin_path . "fields/_field.php");
		self::require_dir("/fields");
		
		if (is_string($schema)) {
			$schema = $this->get_schema( $schema );
		}

		$schema = self::apply_filters("schema", $schema);

		$this->create_field_groups($schema);
	}

/* ==========================================================================
	Field Interaction
	========================================================================= */

	public function create_field_groups($groups) {
		foreach ($groups as $group_name => $fields) {
			$group = array();

			foreach ($fields as $field_name => $field_meta) {
				$field                     = $this->create_field($field_name, $field_meta);
				$group[]                   = $field;
				$this->fields[$field_name] = $field;
			}

			$this->field_groups[$group_name] = $group;
		}
		return $this->field_groups;
	}

	public function get_schema( $schema ) {
		return include(self::$plugin_path . "schema/" . $schema . ".php");
	}

	public function create_field($field_name, $field_meta) {
		$type  = $this->get_field_class($field_meta["type"]);
		$Class = class_exists($type) ? $type : "JPM_Field";

		return new $Class($field_name, $field_meta);
	}

	public function get_field_class($type) {
		$type = str_replace("-", " ", $type);
		$type = ucwords($type);
		$type = str_replace(" ", "_", $type);
		$type = "JPM_" . $type;
		return $type;
	}

	public function render_group( $group_name ) {
		echo $this->generate_html( $this->field_groups[$group_name] );
	}

	public function generate_html( $fields ) {
		$html = "";
		foreach ($fields as $name => $field) {
			$html .= $field->get_html();
		}
		return $html;
	}

/* ==========================================================================
	Template
	========================================================================= */

	public function get_template( $name, $subfolder = "templates/", $data = array() ) {
		foreach ($data as $key => $value) {
			$$key = $value;
		}
		ob_start();
		include(self::$plugin_path . $subfolder . $name . ".php");
		return ob_get_clean();
	}

	public function the_template( $name, $subfolder = "templates/", $data = array() ) {
		echo $this->get_template( $name, $subfolder, $data );
	}

	public function set_vars( $data = array() ) {
	}
}