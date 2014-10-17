<?php

return array(
	
	"page-meta" => array(
		"meta-title" => array(
			"type"        => "text",
			"for_post"    => true,
			"label"       => "Title",
			"allow_null"  => true,
			"classes"     => array(
				"block" => "full-width char-count-block",
				"field" => "full-width soft-char-limit",
				"label" => "block-label",
			),
			"attr"        => array(
				"data-char-limit"     => 60,
				"data-char-limit-msg" => "Google typically only displays the first 50-60 characters of a page title.",
			),
		),
		"meta-desc" => array(
			"type"        => "text",
			"for_post"    => true,
			"label"       => "Description",
			"allow_null"  => true,
			"classes"     => array(
				"block" => "full-width char-count-block",
				"field" => "full-width soft-char-limit",
				"label" => "block-label",
			),
			"attr"        => array(
				"data-char-limit" => 156,
				"data-char-limit-msg" => "Google typically only displays the first 156 characters of a page description.",
			),
		),		
	),


);