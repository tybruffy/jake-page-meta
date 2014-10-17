<?php

class JPM_Media extends JPM_Field {

	function __construct($name, $data) {
		wp_enqueue_media();
		parent::__construct($name, $data);
		$this->image = $this->get_existsing_media();
	}

	public function get_field() {
		return sprintf(
			'<div class="media-block">
				%s
				<div class="media-upload-container %s" data-field="#%s-field" data-display="#%s-display">
					<input class="button media-upload-button" type="button" value="Upload Image" />
					<input class="button media-remove-button" type="button" value="Remove Image" />
					<input id="%s-field" type="hidden" name="%s" value="%s" /> 
					<div class="media-display" id="%s-display">%s</div>
				</div>
			</div>',
			$this->get_label(),
			$this->image ? "has-img" : "no-img",
			$this->name,
			$this->name,
			$this->name,
			$this->name,
			$this->value,
			$this->name,
			$this->image
		);	
	}

	public function get_existsing_media() {
		if (!$this->value) {
			return '';
		}

		return wp_get_attachment_image($this->value, "full");
	}

}