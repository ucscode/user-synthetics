/**
 * User Synthetics JavaScript
 *
 * Author: Uchenna Ajah <Ucscode>
 * Version: 1.0.0
 * License: MIT
 * Last Updated: 02-May-2023
 *
 * Copyright (c) 2023
 */

"use strict";
	
new class {
	
	constructor($) {
		
		$(function() {
			
			this.toolTip();
			this.bootboxCheat();
			this.toastrCheat();
			this.otherCheat();
			
			this.delegateClicks();
			this.delegateChanges();
			this.delegateSubmits();
			
			this.automateActions();
		
		}.bind(this));
		
	}
	
	toolTip() {
		//! Initialize Bootstrap Tooltip
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
		});
	}
	
	bootboxCheat() {
		//! Console Cheat: Display Bootstrap Modal Box
		if( (typeof uss['@alert'] === 'string') && uss['@alert'].trim() != '' ) {
			bootbox.alert({
				title: uss.platform,
				message: uss['@alert']
			});
		};
	}
	
	toastrCheat() {
		/*!
			Console Cheat: Display Toastr Notification
			------------------------------------------
			Backend Sample:
			~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			uss::console( '@toastr.error[name]', "Error Text" );
			uss::console( '@toastr.error[name2]', "Error Text 2" );
			uss::console( '@toastr.success[name]', "Success Text" );
		*/
		let timeout = 0;
		Object.keys(uss).forEach(function(key) {
			let match = key.match(/^@toastr\.(error|info|warning|success)(?:\[([a-z0-9_\-]*)\])$/i);
			if( !match ) return;
			let type = match[1];
			setTimeout(function() {
				toastr[ type ]( uss[key], null, {
					progressBar: true,
					newestOnTop: false
				});
			}, timeout);
			timeout += 1500;
		});
	}
	
	otherCheat() {
		// Prevent form from re-submitting _POST request
		if( uss['@RE-POST'] === false ) {
			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			};
		};
	}
	
	//! Recursive Delegation Method;
	
	delegate( event, parent, closure ) {
		let root = $(parent).get(0);
		let self = this;
		let recycle = function(node, e) {
			if( !node ) return;
			for( let x in closure ) {
				if( node.matches(x) ) {
					let func = closure[x].bind(self)
					return func( node, e );
				};
			};
			if( node != root ) recycle( node.parentElement, e );
		}
		root.addEventListener(event, (e) => recycle( e.target, e ), false);
	}
	
	//! Click Events;
	
	delegateClicks() {
		
		//! Document Delegation
		
		this.delegate( 'click', 'body', {
			
			/*!
				~~~~~~~~~~ [ confirm an anchor before redirecting ] ~~~~~~~~
				
				<a href='' data-uss-confirm='The confirmation message'> 
					Click here to redirect 
				</a>
			*/
			
			"a[data-uss-confirm]": function(anchor, e) {
				e.preventDefault();
				let message = anchor.dataset.ussConfirm;
				if( !message || message.trim() == '' ) message = 'You are about to leave this page';
				bootbox.confirm({
					message: message,
					size: 'small',
					className: 'text-center animate__animated animate__faster animate__bounceIn',
					centerVertical: true,
					closeButton: false,
					callback: function(choice) {
						if( !choice ) return;
						let target = anchor.target;
						if( (!target || target == "_self") && !anchor.dataset.ussFeatures ) {
							window.location.href = anchor.href;
						} else {
							/*
								windowFeature Sample that works on Chrome:
								------------------------------------------
								resizable=yes, scrollbars=yes, titlebar=yes, width=300, height=300, top=10, left=10
							*/
							window.open( anchor.href, target, anchor.dataset.ussFeatures );
						}
					},
					onShow: function(e) {
						$(e.currentTarget).removeClass('fade');
					}
				});
			},
			
	
			//! Copy a text on button click 
			
			"[data-uss-copy]": function( node ) {
				let el = $(node.dataset.ussCopy).get(0), text;
				if( !el ) return toastr.warning( "Failed to copy content" );
				// copy the text;
				if( ['input', 'select', 'textarea'].includes( el.nodeName.toLowerCase() ) ) text = el.value.trim();
				else text = el.innerText.trim();
				// add to clipboard
				navigator.clipboard.writeText( text ).then(
					() => toastr.info( node.dataset.ussMessage || 'Copied to clipboard' ),
					() => toastr.warning( 'Not copied to clipboard' )
				);
			}
		
		});
	
		
	}
	
	delegateChanges() {
		/*! 
			Preview An Image!
			-----------------
			
			<img src='' id='the-image-element />
			
			<input type='file' accept='image/*' data-uss-image-preview='#the-image-element' />
			
		*/
		
		$('body').on('change', "input[type='file'][data-uss-image-preview]", function() {
			try {
				let img = this.dataset.ussImagePreview;
				let image = $(img).filter(function(key, el) {
					return (el.nodeName == 'IMG');
				});
				if( !image.length ) return console.error( `The image preview element cannot be found` );
				let reader = new FileReader();
				reader.addEventListener('load', function() {
					image.attr( 'src', this.result );
				});
				let file = this.files[0];
				let type = file.type.split('/')[0];
				if( type == 'image' ) reader.readAsDataURL( file );
				else toastr.error( 'Invalid Image file', null, {
					progressBar: true,
					showMethod: 'slideDown',
				});
			} catch(e) {
				console.log( e );
			};
		});
	}
	
	delegateSubmits() {
		
		/*!
			=================== confirm a form before submitting ===================
			
			<form method='POST' data-uss-confirm='The confirmation message'>
				...
			</form>
			
		*/
		
		$('body').on('submit', "form[data-uss-confirm]", function(e) {
			e.preventDefault();
			let form = this;
			let message = this.dataset.ussConfirm;
			if( !message || message.trim() == '' ) message = 'Please confirm this process to continue';
			bootbox.confirm({
				message: `<div class='px-4'>${message}</div>`,
				className: 'text-center animate__animated animate__faster animate__bounceIn',
				size: 'small',
				callback: function(yea) {
					if( !yea ) return;
					let node = e.originalEvent.submitter;
					if( node.hasAttribute('name') ) {
						let nodeName = node.tagName.toLowerCase();
						let permit = [
							( nodeName == 'input' && node.type == 'submit' ),
							( nodeName == 'button' && ['submit', '', undefined].includes( node.type ) )
						];
						if( permit.includes(true) ) {
							let input = document.createElement('input');
							input.type = 'hidden';
							input.name = node.name;
							input.value = node.value;
							if( input.value == undefined ) input.value = '';
							form.appendChild( input );
						}
					};
					form.submit();
				},
				onShow: function(e) {
					$(e.currentTarget).removeClass('fade');
				},
				closeButton: false,
				centerVertical: true
			});
		});
	
	}
	
	automateActions() {
		
		/*!
			=========== Auto select an option from the <select/> element ===========
			Make select work similar to input
		*/
		
		$("select[value]").each(function() {
			let value = this.getAttribute('value');
			if( value == '' ) return;
			this.value = value;
		});
	
	}
	
	//! DeepClone an object;
	
	deepClone( obj ) {
		let clone = Array.isArray(obj) ? [] : {};
		for( let x in obj ) {
			let value = obj[x];
			// check if object is iteratable;
			if( typeof value == 'object' && value !== null && typeof value[Symbol.iterator] == 'function' ) {
				clone[x] = this.deepClone( value );
			} else clone[x] = value;
		};
		return clone;
	}
	
	
}(window.jQuery);