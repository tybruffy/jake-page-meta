<?php

class JPM_Field extends JPM {
	public $allow_null = true;
	public $for_post   = false;
	public $classes    = "";
	public $attr       = array();
	public $wrap       = '<div class="form-row">%s</div>';

	function __construct($name, $data) {
		$this->plugin_info();
		$this->name  = $name;
		
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

		$this->value = $this->get_value();
	}

	public function get_html() {
		$html = "";
		if ($this->conditional_display()) {			
			$html = $this->wrap ? sprintf($this->wrap, $this->get_field()) : $this->get_field();
		}
		return $html;
	}

	public function get_field() {
		return sprintf(
			'<div class="form-block %s-block %s-block %s">
				%s
				<input type="%s" name="%s" id="%s" class="%s %s-input" value="%s" %s />
			</div>',
			$this->name,
			$this->type,
			$this->get_classes("block"),
			$this->get_label(),
			$this->type,
			$this->name,
			$this->name,
			$this->get_classes(),
			$this->type,
			$this->value,
			$this->get_attr_string($this->attr)
		);
	}

	public function get_label() {
		if (!isset($this->label)) {
			return "";
		}
		
		return sprintf(
			'<label for="%s" class="%s-label %s">%s</label>',
			$this->name,
			$this->name,
			$this->get_classes("label"),
			$this->label
		);		
	}

	public function get_classes($element = "field") {
		if (is_string($this->classes) && $element == "field") {
			$class = $this->classes;
		} elseif (is_array($this->classes) && isset($this->classes[$element])) {
			$class = $this->classes[$element];
		} else {
			$class = "";
		}
		return $class;
	}

	public function wrap($sprintf, $content) {
		return sprintf(
			$sprintf,
			$content
		);
	}
	
	public function wrap_each($sprintf, $content) {
		$output = array();
		
		foreach ($content as $field) {
			$output[] =$this->wrap($sprintf, $field);
		}

		return $output;
	}

	function get_attr_string($attrs) {
		$string = '';
		foreach ($attrs as $key => $value) {
			$string .= $key . '="' . $value . '" ';
		}
		return $string;
	}

	function get_value() {
		if (isset($this->value)) {
			return $this->value;
		} elseif ($this->for_post) {
			return self::get_post_meta(self::get_post_id(), $this->name);
		}
		return self::get_option($this->name);
	}

	function save($value) {
		if (!$this->allow_null && !$value) {
			return false;
		}
		$this->value = $value;
		self::set_option($this->name, $value);
	}	

	function maybe_selected($var, $value) {
		return ($var == $value)	? 'selected="selected"' : '';
	}
	
	function maybe_checked($var, $value) {
		return ($var == $value)	? 'checked="checked"' : '';
	}

	function conditional_display() {
		if (isset($this->condition) && is_string($this->condition)) {
			return call_user_func(array($this, $this->condition));
		}
		return true;
	}


}