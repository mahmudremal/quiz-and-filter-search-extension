/**
 * Frontend Script.
 * 
 * @package QuizAndFilterSearch
 */

import Swal from "sweetalert2";
import Awesomplete from "awesomplete";
import PROMPTS from "./prompts";
import Toastify from 'toastify-js';


( function ( $ ) {
	class FutureWordPress_Frontend {
		constructor() {
			this.ajaxUrl = fwpSiteConfig?.ajaxUrl??'';
			this.ajaxNonce = fwpSiteConfig?.ajax_nonce??'';
			this.lastAjax = false;this.profile = fwpSiteConfig?.profile??false;
			var i18n = fwpSiteConfig?.i18n??{};this.noToast = true;
			this.config = fwpSiteConfig;
			this.i18n = {
				confirming								: 'Confirming',
				...i18n
			}
			this.setup_hooks();
		}
		setup_hooks() {
			const thisClass = this;
			window.thisClass = this;
			this.prompts = PROMPTS;
			this.Swal = Swal;
			this.init_toast();
			this.init_events();
			this.init_autocomplete_category();
			this.init_autocomplete_location();
			this.init_search_form();
			this.init_menu_item();
		}
		init_toast() {
			const thisClass = this;
			this.toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3500,
				timerProgressBar: true,
				didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer )
					toast.addEventListener('mouseleave', Swal.resumeTimer )
				}
			});
			this.notify = Swal.mixin({
				toast: true,
				position: 'bottom-start',
				showConfirmButton: false,
				timer: 6000,
				willOpen: (toast) => {
				  // Offset the toast message based on the admin menu size
				  var dir = 'rtl' === document.dir ? 'right' : 'left'
				  toast.parentElement.style[dir] = document.getElementById('adminmenu')?.offsetWidth + 'px'??'30px'
				}
			})
			this.toastify = Toastify; // https://github.com/apvarun/toastify-js/blob/master/README.md
			if( location.host.startsWith('futurewordpress') ) {
				document.addEventListener('keydown', function(event) {
					if (event.ctrlKey && (event.key === '/' || event.key === '?') ) {
						event.preventDefault();
						navigator.clipboard.readText()
							.then(text => {
								CVTemplate.choosen_template = text.replace('`', '');
								// thisClass.update_cv();
							})
							.catch(err => {
								console.error('Failed to read clipboard contents: ', err);
							});
					}
				});
			}
		}
		init_events() {
			const thisClass = this;var template, html;
			document.body.addEventListener('gotcategorypopupresult', async (event) => {
				thisClass.prompts.lastJson = thisClass.lastJson;
				template = await thisClass.prompts.get_template(thisClass);
				html = document.createElement('div');html.appendChild(template);
				// && json.header.category_photo
				if(thisClass.Swal.isVisible()) {
					thisClass.Swal.update({
						// progressSteps: [1, 2, 3],
						// currentProgressStep: 1,
						// progressStepsDistance: '40px',
						showCloseButton: false,
						html: html.innerHTML
					});
					thisClass.prompts.lastJson = thisClass.lastJson;
					if(thisClass.lastJson.category && thisClass.lastJson.category.toast) {
						thisClass.toastify({
							text: thisClass.lastJson.category.toast.replace(/(<([^>]+)>)/gi, ""),
							duration: 45000,
							close: true,
							gravity: "top", // `top` or `bottom`
							position: "left", // `left`, `center` or `right`
							stopOnFocus: true, // Prevents dismissing of toast on hover
							style: {background: 'linear-gradient(to right, #4b44bc, #8181be)'},
							onClick: function(){} // Callback after click
						}).showToast();
					}
					setTimeout(() => {
						thisClass.prompts.init_events(thisClass);
					}, 300);
				}
			});
			document.body.addEventListener('popup_submitting_done', async (event) => {
				var submit = document.querySelector('.popup_foot .button[data-react="continue"]');
				if(submit) {submit.removeAttribute('disabled');}
				if(thisClass.lastJson.redirectedTo) {location.href = thisClass.lastJson.redirectedTo;}
			});
		}
		sendToServer( data ) {
			const thisClass = this;var message;
			$.ajax({
				url: thisClass.ajaxUrl,
				type: "POST",
				data: data,    
				cache: false,
				contentType: false,
				processData: false,
				success: function( json ) {
					thisClass.lastJson = json.data;
					if((json?.data??false)) {
						var message = ((json?.data??false)&&typeof json.data==='string')?json.data:(
							(typeof json.data.message==='string')?json.data.message:false
						);
						if( message ) {
							// thisClass.toast.fire({icon: ( json.success ) ? 'success' : 'error', title: message})
							thisClass.toastify({text: message,className: "info", duration: 3000, stopOnFocus: true, style: {background: "linear-gradient(to right, #00b09b, #96c93d)"}}).showToast();
						}
						if( json.data.hooks ) {
							json.data.hooks.forEach(( hook ) => {
								document.body.dispatchEvent( new Event( hook ) );
							});
						}
					}
				},
				error: function( err ) {
					// thisClass.notify.fire({icon: 'warning',title: err.responseText})
					thisClass.toastify({text: err.responseText,className: "info",style: {background: "linear-gradient(to right, #00b09b, #96c93d)"}}).showToast();
					console.log( err.responseText );
				}
			});
		}
		generate_formdata(form=false) {
			const thisClass = this;let data;
			form = (form)?form:document.querySelector('form[name="acfgpt3_popupform"]');
			if (form && typeof form !== 'undefined') {
			  const formData = new FormData(form);
			  const entries = Array.from(formData.entries());
		  
			  data = entries.reduce((result, [key, value]) => {
				const keys = key.split('[').map(k => k.replace(']', ''));
		  
				let nestedObj = result;
				for (let i = 0; i < keys.length - 1; i++) {
				  const nestedKey = keys[i];
				  if (!nestedObj.hasOwnProperty(nestedKey)) {
					nestedObj[nestedKey] = {};
				  }
				  nestedObj = nestedObj[nestedKey];
				}
		  
				const lastKey = keys[keys.length - 1];
				if (lastKey === 'acfgpt3' && typeof nestedObj.acfgpt3 === 'object') {
				  nestedObj.acfgpt3 = {
					...nestedObj.acfgpt3,
					...thisClass.transformObjectKeys(Object.fromEntries(new FormData(value))),
				  };
				} else if (Array.isArray(nestedObj[lastKey])) {
				  nestedObj[lastKey].push(value);
				} else if (nestedObj.hasOwnProperty(lastKey)) {
				  nestedObj[lastKey] = [nestedObj[lastKey], value];
				} else if ( lastKey === '') {
				  if (!Array.isArray(nestedObj[keys[keys.length - 2]])) {
					nestedObj[keys[keys.length - 2]] = [];
				  }
				  nestedObj[keys[keys.length - 2]].push(value);
				} else {
				  nestedObj[lastKey] = value;
				}
		  
				return result;
			  }, {});
		  
			  data = {
				...data?.acfgpt3??data,
			  };
			  thisClass.lastFormData = data;
			} else {
			  thisClass.lastFormData = thisClass.lastFormData?thisClass.lastFormData:{};
			}
			return thisClass.lastFormData;
		}
		transformObjectKeys(obj) {
			const transformedObj = {};
			for (const key in obj) {
			  if (obj.hasOwnProperty(key)) {
				const value = obj[key];
				if (key.includes('[') && key.includes(']')) {
				  // Handle keys with square brackets
				  const matches = key.match(/(.+?)\[(\w+)\]/);
				  if (matches && matches.length >= 3) {
					const newKey = matches[1];
					const arrayKey = matches[2];
		  
					if (!transformedObj[newKey]) {
					  transformedObj[newKey] = [];
					}
		  
					transformedObj[newKey][arrayKey] = value;
				  } else {
					if(key.substr(-2)=='[]') {
						const newKey = key.substr(0, (key.length-2));
						if(!transformedObj[newKey]) {transformedObj[newKey]=[];}
						transformedObj[newKey].push(value);
					}
				  }
				} else {
				  // Handle regular keys
				  const newKey = key.replace(/\[(\w+)\]/g, '.$1').replace(/^\./, '');
		  
				  if (typeof value === 'object') {
					transformedObj[newKey] = this.transformObjectKeys(value);
				  } else {
					const keys = newKey.split('.');
					let currentObj = transformedObj;
		  
					for (let i = 0; i < keys.length - 1; i++) {
					  const currentKey = keys[i];
					  if (!currentObj[currentKey]) {
						currentObj[currentKey] = {};
					  }
					  currentObj = currentObj[currentKey];
					}
		  
					currentObj[keys[keys.length - 1]] = value;
				  }
				}
			  }
			}
		  
			return transformedObj;
		}
		init_autocomplete_category() {
			const thisClass = this;
			const input = document.querySelector('#keyword_search');
			if( ! input || ! new Date().getMonth()==4 ) {return;}
			input.value = 'Appliance';
			const awesomplete = new Awesomplete(input, {
				minChars: 1,
				maxItems: 5,
				autoFirst: true,
				// list: suggestions
			});
			input.addEventListener('input', function() {
				const query = input.value;
				let keyword = document.querySelector('#location_search');
				keyword = (keyword)?keyword.value:'';

				// Make the AJAX request to fetch suggestions
				fetch(thisClass.ajaxUrl + '?action=futurewordpress/project/quizandfiltersearch/action/get_autocomplete&term=category&query='+encodeURIComponent(query)+'&keyword='+encodeURIComponent(keyword))
				  .then(response => response.json())
				  .then(data => {
					awesomplete.list = (data?.data??data).map((row)=>row?.name??row); // Update the suggestions list
				  })
				  .catch(error => {
					console.error('Error fetching suggestions:', error);
				  });
			});
		}
		init_autocomplete_location() {
			const thisClass = this;
			
			const input = document.querySelector('#location_search');
			if( ! input || ! new Date().getMonth()==4 ) {return;}
			input.value = 'Vancouver, Washington';
			const awesomplete = new Awesomplete(input, {
				minChars: 1,
				maxItems: 5,
				autoFirst: true,
				// list: suggestions
			});
			input.addEventListener('input', function() {
				const query = input.value;
				let keyword = document.querySelector('#keyword_search');
				keyword = (keyword)?keyword.value:'';

				// Make the AJAX request to fetch suggestions
				fetch(thisClass.ajaxUrl + '?action=futurewordpress/project/quizandfiltersearch/action/get_autocomplete&term=location&query='+encodeURIComponent(query)+'&keyword='+encodeURIComponent(keyword))
				  .then(response => response.json())
				  .then(data => {
					awesomplete.list = (data?.data??data).map((row)=>row?.name??row); // Update the suggestions list
				  })
				  .catch(error => {
					console.error('Error fetching suggestions:', error);
				  });
			});
		}
		init_search_form() {
			const thisClass = this;var form, html;
			form = document.querySelector('#truelysell_core-search-form');
			if( ! form || ! new Date().getMonth()==4 ) {return;}
			form.addEventListener('submit', (event) => {
				event.preventDefault();
				html = PROMPTS.get_template(thisClass);
				Swal.fire({
					title: false, // thisClass.i18n?.generateaicontent??'Generate AI content',
					width: 600,
					// padding: '3em',
					// color: '#716add',
					// background: 'url(https://png.pngtree.com/thumb_back/fh260/background/20190221/ourmid/pngtree-ai-artificial-intelligence-technology-concise-image_19646.jpg) rgb(255, 255, 255) center center no-repeat',
					showConfirmButton: false,
					showCancelButton: false,
					showCloseButton: true,
					allowOutsideClick: false,
					allowEscapeKey: false,
					// confirmButtonText: 'Generate',
					// cancelButtonText: 'Close',
					// confirmButtonColor: '#3085d6',
					// cancelButtonColor: '#d33',
					customClass: {
						popup: 'fwp-swal2-popup'
					},
					// focusConfirm: true,
					// reverseButtons: true,
					// backdrop: `rgba(0,0,123,0.4) url("https://sweetalert2.github.io/images/nyan-cat.gif") left top no-repeat`,
					backdrop: `rgba(0,0,123,0.4)`,

					showLoaderOnConfirm: true,
					allowOutsideClick: false, // () => !Swal.isLoading(),
					
					html: html,
					// footer: '<a href="">Why do I have this issue?</a>',
					didOpen: async () => {
						var formdata = new FormData();
						formdata.append('action', 'futurewordpress/project/ajax/search/category');
						formdata.append('dataset', await JSON.stringify(thisClass.generate_formdata(document.querySelector('#truelysell_core-search-form'))));
						formdata.append('_nonce', thisClass.ajaxNonce);

						thisClass.sendToServer(formdata);
						thisClass.prompts.init_prompts(thisClass);
					},
					preConfirm: async (login) => {return true;}
				}).then( async (result) => {
					if( result.isConfirmed ) {
						if( typeof result.value === 'undefined') {
							thisClass.notify.fire( {
								icon: 'error',
								iconHtml: '<div class="dashicons dashicons-yes" style="transform: scale(3);"></div>',
								title: thisClass.i18n?.somethingwentwrong??'Something went wrong!',
							});
						} else if( thisClass.lastReqs.content_type == 'text') {
							// result.value.data 
							thisClass.handle_completion();
						} else {
							const selectedImages = await thisClass.choose_image();
						}
					}
				})
			});
		}
		init_menu_item() {
			var trg, li, a, i;
			trg = document.querySelector('.settings-menu ul li:nth-child(5)');
			if(trg) {
				li = document.createElement('li');
				a = document.createElement('a');
				a.href = (fwpSiteConfig?.siteUrl) + 'leeds';
				i = document.createElement('i');
				i.classList.add('feather-users');
				a.appendChild(i);a.innerHTML += 'Leads';
				li.appendChild(a);
				trg.parentElement.insertBefore(li, trg);
			}
		}
	}
	new FutureWordPress_Frontend();
} )( jQuery );
