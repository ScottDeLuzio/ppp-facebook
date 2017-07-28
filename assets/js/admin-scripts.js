jQuery(document).ready(function ($) {
	ppp_facebook_reset_values();
	if ( $('#title').val().length ) {
		ppp_facebook_update_color();
	}

	$('#ppp-facebook-link-to-post, #ppp-facebook-cancel-ext').click( function( e ) {
		$('#ppp-facebook-post-link').toggle();
		$('#ppp-facebook-ext-link').toggle();
		$('#ppp-facebook-ext-notice').toggle();
	});

	$('#ppp-facebook-ext-link').click( function() {
		$('select#ppp_facebook_link').val('0').trigger('chosen:updated');
	});

	// Setup Chosen menus
	$('.ppp-facebook-select-chosen').chosen({
		inherit_select_classes: true,
		placeholder_text_single: 'Select A Post',
		placeholder_text_multiple: 'Select Posts',
	});

	// Add placeholders for Chosen input fields
	$( '.chosen-choices' ).on( 'click', function () {
		$(this).children('li').children('input').attr( 'placeholder', 'Type to search' );
	});

	// Variables for setting up the typing timer
	var typingTimer;               // Timer identifier
	var doneTypingInterval = 342;  // Time in ms, Slow - 521ms, Moderate - 342ms, Fast - 300ms

	// Replace options with search results
	$('.ppp-facebook-select.chosen-container .chosen-search input, .ppp-facebook-select.chosen-container .search-field input').keyup(function(e) {

		var val = $(this).val(), container = $(this).closest( '.ppp-facebook-select-chosen' );
		var menu_id = container.attr('id').replace( '_chosen', '' );
		var lastKey = e.which;

		// Don't fire if short or is a modifier key (shift, ctrl, apple command key, or arrow keys)
		if(
			val.length <= 3 ||
			(
				e.which == 16 ||
				e.which == 13 ||
				e.which == 91 ||
				e.which == 17 ||
				e.which == 37 ||
				e.which == 38 ||
				e.which == 39 ||
				e.which == 40
			)
		) {
			return;
		}

		var data = {}

		clearTimeout(typingTimer);
		typingTimer = setTimeout(
			function(){
				$.ajax({
					type: 'GET',
					url: ajaxurl,
					data: {
						action: 'ppp_facebook_post_search',
						s: val,
					},
					dataType: "json",
					beforeSend: function(){
						$('ul.chosen-results').empty();
					},
					success: function( data ) {

						// Remove all options but those that are selected
						$('#' + menu_id + ' option:not(:selected)').remove();
						$.each( data, function( key, item ) {
							// Add any option that doesn't already exist
							if( ! $('#' + menu_id + ' option[value="' + item.id + '"]').length ) {
								$('#' + menu_id).prepend( '<option value="' + item.id + '">' + item.name + '</option>' );
							}
						});
						 // Update the options
						$('.ppp-facebook-select-chosen').trigger('chosen:updated');
						$('#' + menu_id).next().find('input').val(val);
					}
				}).fail(function (response) {
					if ( window.console && window.console.log ) {
						console.log( data );
					}
				}).done(function (response) {

				});
			},
			doneTypingInterval
		);
	});

	$('#ppp-facebook-ext-link-input').blur( function() {
		var value = $(this).val();
		ppp_facebook_reset_values();

		if(value.length > 0) {
			fbpostLengthYellow = fbpostLengthYellow - 22;
			fbpostLengthRed    = fbpostLengthRed - 22;
		} else {
			ppp_facebook_reset_values();
		}
		ppp_facebook_update_color();
	});

	$('#ppp_facebook_link').change( function() {
		var value = $(this).val();
		ppp_facebook_reset_values();

		if(value > 0) {
			fbpostLengthYellow = fbpostLengthYellow - 22;
			fbpostLengthRed    = fbpostLengthRed - 22;
		} else {
			ppp_facebook_reset_values();
		}
		ppp_facebook_update_color();
	});

	// This fixes the Chosen box being 0px wide when the thickbox is opened
	$( '#ppp-facebook-cancel-ext' ).click( function() {
		$( '.ppp-facebook-select-chosen' ).css( 'width', 'auto' );
	});

	$('#titlewrap #title').keyup(function(e) {
		ppp_facebook_update_color();
	});
});

function ppp_facebook_update_color() {
	var len = jQuery('#title').val().length;
	var lengthField = jQuery('#ppp-facebook-details');

	lengthField.text(len);

	pppSetLengthColors(len, lengthField);
}

function ppp_facebook_reset_values() {
	fbpostLengthYellow = 122;
	fbpostLengthRed    = 141;
}
