<?php

class JPM_Color extends JPM_Field {

	function __construct($name, $data) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		parent::__construct($name, $data);
	}

	public function get_field() {
		return sprintf(
			'<div class="form-block %s-block %s-block">
				%s
				<input type="text" name="%s" id="%s" class="%s %s-input" value="%s" %s />
			</div>',
			$this->name,
			$this->type,
			$this->get_label(),
			$this->name,
			$this->name,
			$this->get_classes(),
			$this->type,
			$this->value,
			$this->get_attr_string($this->attr)
		);
	}

}