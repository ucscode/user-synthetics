/**
 * User Synthetics by Uchenna Ajah
 * License: MIT
 * Since 2023
 */
"use strict";
	
(new class {
	
	bootstrapEnabled = null;
	
	constructor() {
		this.bootstrapEnabled = (typeof bootstrap !== 'undefined') && (typeof bootstrap.Tooltip !== 'undefined');
	}

	react($) {
		$(function() {
			this.toolTip();
			this.autoSelect();
			this.otherCheat();
		}.bind(this));
	}
	
	toolTip() {
		if( !this.bootstrapEnabled ) return console.log('No Bootstrap');
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
		});
	}
	
	autoSelect() {
		$('select[value]').each(function() {
			let value = this.getAttribute('value');
			if(value !== '') this.value = value;
		});
	}
	
	otherCheat() {
		if(Uss['@RE-POST'] === false) {
			if (window.history.replaceState) {
				window.history.replaceState(null, null, window.location.href);
			};
		};
	}
	
}).react(window.jQuery);