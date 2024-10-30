(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	
	 $(function() {

	 	var _kl_plugin = {

	 		init: function() {
				_kl_plugin.bindRefreshChecklistBtn();
				_kl_plugin.bindClearCacheDataBtn();
			},

			bindRefreshChecklistBtn() {
				$('.kuicklist_checklists').on('click', _kl_plugin.klRefreshChecklist.bind(null, 'click'));

			},

			bindClearCacheDataBtn() {
				$('.clearAllCahceData').on('click', _kl_plugin.klClearCacheData.bind(null, 'click'));

			},

			klRefreshChecklist: function () {
				var data = {
					'action': 'refresh_checklists',
				};

				$('.kuicklist_refresh_icon').html('Fetching Data...');

				jQuery.post(ajaxurl, data, function(response) {
					$(".kuicklist_checklists_dd" ).replaceWith(response);

					// bind the click event after refresh
					_kl_plugin.bindRefreshChecklistBtn();

				});
			},

			klClearCacheData: function () {
				var data = {
					'action': 'kuicklist_clear_all_cache_data',
				};

				$('.clearData').hide();
				$('.clearingCacheData').show();

				jQuery.post(ajaxurl, data, function(response) {
					console.log(response);
					$('.clearData').show();
					$('.clearingCacheData').hide();
				});
			}

		};

		window.onload = _kl_plugin.init();
	 })

})( jQuery );
