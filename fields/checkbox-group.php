<?php

class JPM_Checkbox_Group extends JPM_Field {
	public $subfields = array();
	public $to_save   = array();
	public $wrap      = false;

	function __construct($field_name, $data) {
 		parent::__construct($field_name, $data);

		$this->choices = $data["choices"];
	}

	public function get_field() {
		$this->create_sub_fields();

		$html = "";
		foreach ($this->subfields as $field) {
			$html .= $field->get_html();
		}
		return $html;
	}

	public function create_sub_fields() {
		foreach ($this->choices as $sub_field_name => $meta) {
			$meta["group"]                    = $this->name;
			$meta["value"]                    = $this->get_sub_value($sub_field_name);
			$this->subfields[$sub_field_name] = new JPM_Checkbox($sub_field_name, $meta);
		}
	}

	public function get_sub_value($sub_field_name) {
		if (isset($this->value[$sub_field_name])) {
			return $this->value[$sub_field_name];
		} else {
			return false;
		}
	}

	public function save($data_array) {
		$data_array = !$data_array ? array() : $data_array;

		foreach ($this->choices as $name => $field) {
			if (in_array($name, $data_array)) {
				$this->to_save[$name] = true;
			} else {
				$this->to_save[$name] = false;
			}
		}

		parent::save($this->to_save);
	}

}