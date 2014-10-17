jQuery(document).ready(function($) {

/* ==========================================================================
	Color Field
	========================================================================= */

	$(".wp-color-field").wpColorPicker({
		defaultColor: false,
		change: function(event, ui) {
			var $input = $('<input type="hidden" name="color_update" id="color_update" value="true">');
			$(this).closest("form").append($input);
		},
	});

/* ==========================================================================
	Upload Media
	========================================================================= */

	$('.media-upload-button').click(function(event) {
		event.preventDefault();

		$container = $(this).closest(".media-upload-container");

		if (typeof uploader !== "undefined") {
			uploader.open();
			return;
		}

		uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		uploader.on('select', function() {
			var field      = $container.data("field");
			var display    = $container.data("display");
			var attachment = uploader.state().get('selection').first().toJSON();

			$(display).html('<img src="' + attachment.url + '" />')
			$(field).val(attachment.id);

			$container.removeClass("no-img").addClass("has-img");
		});

		uploader.open();
	});

/* ==========================================================================
	Remove Media
	========================================================================= */

	$('.media-remove-button').click(function(event) {
		event.preventDefault();

		$container = $(this).closest(".media-upload-container");

		var field   = $container.data("field");
		var display = $container.data("display");

		$(display).html('');
		$(field).val(0);

		$container.removeClass("has-img").addClass("no-img");
	});

/* ==========================================================================
	Character Limits
	========================================================================= */

	$(".char-count-block")
		.append('<span class="char-count"><span class="count-progress"></span></span>')
		.append('<span class="char-limit-msg"></span>');

	$(".soft-char-limit").on("propertychange keyup input paste", function() {
		var $cntr    = $(this).closest(".form-block");
		var $bar     = $cntr.find(".count-progress");
		var max      = $(this).data("char-limit");
		var msg      = $(this).data("char-limit-msg");
		var count    = $(this).val().length;
		var $msgcntr = $cntr.find(".char-limit-msg");
		var percent  = ((count/max) * 100);

		if (count > max) {
			$cntr.addClass("exceeded");
			$msgcntr.text(msg);
		} else {
			$cntr.removeClass("exceeded");
			$msgcntr.text("");
		}

		$bar.css("width", percent + "%");		
	})

	$(".soft-char-limit").trigger("keyup");


});