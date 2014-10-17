<?php

class JPM_Meta extends JPM {
	public $metadata = array();
	public static $default_title = "";

	public function __construct() {
		$this->add_sitewide_meta();
		$this->add_post_meta();
	}

	public function generate() {
		$str = "";
		foreach ($this->metadata as $key => $value) {
			$str .= $this->create_meta_tag($key, $value) . "\r\n";
		}
		return $str;
	}

	public function add_sitewide_meta() {
		$this->metadata["og:locale"]    = get_locale();
		$this->metadata["og:site_name"] = get_bloginfo("name");
	}

	public function add_post_meta() {
		global $post;
		$this->metadata["og:url"]          = get_permalink($post->ID);
		$this->metadata["og:title"]        = self::get_post_title($post->ID);
		$this->metadata["og:type"]         = $this->get_og_type($post->post_type);
		$this->metadata["og:description"]  = $this->get_post_desc($post->post_type);
	}	

	public function create_meta_tag($key, $value) {
		return sprintf(
			'<meta property="%s" content="%s" />',
			$key,
			$value
		);
	}

/* ==========================================================================
	Data Getters
	========================================================================= */

	public static function get_post_title() {
		global $post;
		$title = self::get_post_meta($post->ID, "meta-title", true);
		
		if (!$title) {
			$title = self::$default_title;
		}

		return $title;
	}

	public function get_post_desc() {
		global $post;
		$desc = self::get_post_meta("meta-desc", $post->ID);
		
		if (!$desc) {
			$desc = $this->create_desc_text();
		}
		return $desc;
	}	

/* ==========================================================================
	Helper Funcs
	========================================================================= */

	public function get_og_type($post_type) {
		$type = self::get_option("og-type-$post_type");
		return $type ? $type : "website";
	}

	private function create_desc_text() {
		global $post;
		
		if ( !$desc = trim( $post->post_excerpt ) ) {
			if (!empty($post->post_content)) {
				$desc = $this->cutoff_text($post->post_content, 150);
			} else {
				$desc = get_bloginfo("description");
				$desc = ($desc !== "Just another WordPress site") ? $desc : "";
			}
		}
		
		return strip_tags($desc);
	}

	private function cutoff_text( $text, $length ) {
		$words = preg_split("/[\n\r\t ]+/", $text, $length + 1, PREG_SPLIT_NO_EMPTY);

		if ( count( $words ) > $length ) {
			array_pop( $words );
			$text = implode( ' ', $words );
		} else {
			$text = implode( ' ', $words );
		}
		return $text;
	}

/* ==========================================================================
	Setters
	========================================================================= */

	public static function set_title($title, $seperator) {
		self::$default_title = $title;
		return self::get_post_title();
	}

}