<?php

class JPM_Settings extends JPM {
	public $updated     = false;
	public $error       = false;
	public $notice      = "";
	public $schema_name = "";

	function __construct($schema) {
		$this->schema_name = $schema;
		$this->plugin_info();		
		$this->schema = new JPM_Schema($schema);
	}

	function display($template_name) {
		$settings = $this;
		$schema   = $this->schema;
		$this->schema->the_template($template_name, "templates/", array("settings" => $this));
	}

/* ==========================================================================
	Processing
	========================================================================= */

	public function update($update_array) {
		if (isset($update_array["color_update"]) && $update_array["color_update"]) {
			$this->compile($update_array["compiled_css"]);
			$this->notice =  "Your browser may prevent you from seeing changes made to the color scheme immediately. Please hard refresh/clear your cache if you do not see your changes.";
		}	

		$this->save_fields($update_array);

		$this->updated = true;
	}

	public function save_fields($update_array) {
		foreach ($this->schema->fields as $name => $field) {
			if ( !isset($update_array[$name]) ) {
				$update_array[$name] = false;
			}
			$field->save($update_array[$name]);
		}
	}

/* ==========================================================================
	HTML Interaction
	========================================================================= */

	public function update_message() {
		if (!$this->updated) {
			return;
		}

		if ($this->error) {
			echo $this->notice("Error Saving.", "error", $this->error);
		} else {
			echo $this->notice("Settings saved.", "updated", $this->notice);
		}
	}

}