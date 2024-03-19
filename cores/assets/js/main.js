/**
 * User Synthetics by Uchenna Ajah
 * License: MIT
 * Since 2023
 */
"use strict";
	
new class {
	
	bootstrapEnabled = null;
	
	constructor($) {
		this.bootstrapEnabled = (typeof bootstrap !== 'undefined') && (typeof bootstrap.Tooltip !== 'undefined');
		this.#extendJQuery($);
		this.#reactiveComponents($, this);
	}

	#extendJQuery($) {

		$.extend({

			validateObject: function(objectInterface, originalObject) {

				for (let key in objectInterface) {

					let propertyInterface = objectInterface[key];

					if (typeof propertyInterface === 'string') {
						propertyInterface = { 
							type: propertyInterface, 
							required: true 
						};
					}

					if (propertyInterface.required && !originalObject.hasOwnProperty(key)) {
						if(propertyInterface.hasOwnProperty('default')) {
							originalObject[key] = propertyInterface.default;
						} else {
							throw new Error(`Property '${key}' is required`);
						}
					}

					if (originalObject.hasOwnProperty(key) && typeof originalObject[key] !== propertyInterface.type) {
						throw new Error(`Property '${key}' must be of type ${propertyInterface.type}, ${typeof originalObject[key]} given instead`);
					}
				}

				return originalObject;
			},

		});

	}

	#reactiveComponents($, _self) {

		const reaction = {

			toolTip: function() {
				
				if( !_self.bootstrapEnabled ) return console.log('No Bootstrap');

				var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

				tooltipTriggerList.map(function (tooltipTriggerEl) {
					return new bootstrap.Tooltip(tooltipTriggerEl);
				});

			},

			autoSelect: function() {
				$('select[value]').each(function() {
					let value = this.getAttribute('value');
					if(value !== '') this.value = value;
				});
			}

		}

		$(function() {
			for(let key in reaction) {
				reaction[key]();
			}
		});
	}
	
}(window.jQuery);