<?php

class JPM_Hidden extends JPM_Field {
	public $wrap = false;

	function __construct($name, $data) {
 		parent::__construct($name, $data);
	}

	public function get_field() {
		return sprintf(
			'<input type="%s" name="%s" value="%s" %s />',
			$this->type,
			$this->name,
			$this->value,
			$this->get_attr_string($this->attr)
		);
	}

}