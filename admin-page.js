jQuery(function($) {
	console.log(wp_localized_vars);

	/*
	 * Select/Upload image(s) event
	 */
	$('body').on('click', '.js-dfi-group .upload_image_button', function(e) {
		e.preventDefault();

		var button = $(this),
			custom_uploader = wp
				.media({
					title: 'Insert image',
					library: {
						// uncomment the next line if you want to attach image to the current post
						// uploadedTo : wp.media.view.settings.post.id,
						type: 'image'
					},
					button: {
						text: 'Use this image' // button label text
					},
					multiple: false // for multiple image selection set to true
				})
				.on('select', function() {
					// it also has "open" and "close" events
					var attachment = custom_uploader
						.state()
						.get('selection')
						.first()
						.toJSON();

					$(button)
						.removeClass('button')
						.html(
							'<img class="true_pre_image" src="' +
								attachment.url +
								'" style="max-width:40px;display:block;" />'
						)
						.val(attachment.id);

					/* if you sen multiple to true, here is some code for getting the image IDs
						var attachments = frame.state().get('selection'),
						    attachment_ids = new Array(),
						    i = 0;
						attachments.each(function(attachment) {
			 				attachment_ids[i] = attachment['id'];
							console.log( attachment );
							i++;
						});
					*/
				})
				.open();
	});

	/*
	 * Remove image event
	 */
	$('body').on('click', '.js-dfi-row .remove_image_button', function() {
		if ($('.js-dfi-row').length < 2) {
			addRow();
		}

		$(this)
			.parent()
			// .find('.misha_upload_image_button')
			// .val('')
			// .addClass('button')
			// .html('Upload image');
			.remove();

		return false;
	});

	$('#dfi-add-row').on('click', addRow);

	$('#dfi-save').on('click', function() {
		var values = {};

		$('.js-dfi-row').map(function(i, el) {
			var type = $(el)
				.find('.type-selector')
				.val();

			var attachment_id = $(el)
				.find('.upload_image_button')
				.val();

			if (type && attachment_id) {
				values[type] = attachment_id;
			}
		});

		// ajax save option
		jQuery.post(
			wp_localized_vars.ajaxurl,
			{
				action: 'update_options',
				options: values
			},
			function(response) {
				// show Notice
				var $wpbodyContent = $('#wpbody-content');

				$wpbodyContent.find('.notice').remove();

				setTimeout(function() {
					$wpbodyContent.prepend(
						'<div class="updated notice"><p>' + response.msg + '</p></div>'
					);
				}, 300);
			}
		);
	});

	function addRow() {
		var $lastRow = $('.js-dfi-row').last();
		var $clone = $lastRow.clone();
		var $type_selector = $($clone).find('.type-selector');

		// select first option
		$type_selector.val($type_selector.find('option:first').val());

		// reset button to original state
		$clone
			.find('.upload_image_button')
			.text('Upload image')
			.val('');

		$clone.insertAfter($lastRow);
	}
});
