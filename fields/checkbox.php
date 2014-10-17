<?php

class JPM_Checkbox extends JPM_Field {
	public $wrap = false;

	function __construct($name, $data) {
 		parent::__construct($name, $data);
	}

	public function get_field() {
		if (isset($this->enclosed) && $this->enclosed === false) {
			$html = $this->get_unenclosed_html();
		} else {
			$html = $this->get_normal_html();
		}
		return $html;
	}

	public function get_normal_html() {
		return sprintf(
			'<div class="form-block %s-block %s-block">
				<label for="%s" class="%s-label">
				<input type="%s" name="%s" %s id="%s" class="%s %s-input" %s %s />
				%s</label>
			</div>',
			$this->name,
			$this->type,
			$this->name,
			$this->name,
			$this->type,
			isset($this->group) ? $this->group . "[]" : $this->name,
			isset($this->group) ? 'value="' . $this->name . '"' : '',
			$this->name,
			$this->get_classes(),
			$this->type,
			$this->maybe_checked($this->value, true),
			$this->get_attr_string($this->attr),
			$this->label
		);
	}

	public function get_unenclosed_html() {
		return sprintf(
			'<div class="form-block %s-block %s-block">
				%s
				<input type="%s" name="%s" %s id="%s" class="%s %s-input" %s %s />
			</div>',
			$this->name,
			$this->type,
			$this->get_label(),
			$this->type,
			isset($this->group) ? $this->group . "[]" : $this->name,
			isset($this->group) ? 'value="' . $this->name . '"' : '',
			$this->name,
			$this->get_classes(),
			$this->type,
			$this->maybe_checked($this->value, true),
			$this->get_attr_string($this->attr)
		);
	}


}