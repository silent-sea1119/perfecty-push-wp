	'use strict';

	function checkFeatures() {
		return ('serviceWorker' in navigator) && ('PushManager' in window);
	}
	
	function registerServiceWorker(path, siteUrl, vapidPublicKey64, nonce) {
			navigator.serviceWorker.register(path + '/service-worker-loader.js.php', { scope: '/' }).then(() =>{
				return navigator.serviceWorker.ready
			}).then(async (registration) => {
				// we get the push subscription
				const subscription = await registration.pushManager.getSubscription();
				if (subscription) {
					return subscription;
				}
				const vapidPublicKey = urlBase64ToUint8Array(vapidPublicKey64);
				return registration.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: vapidPublicKey
				});
			}).then((subscription) => {
				// we send the registration details to the server
				path = siteUrl + "/wp-json/perfecty-push/v1/register?_wpnonce=" + nonce
				const payload = {
					subscription: subscription
				}

				fetch(path, {
					method: 'post',
					headers: {
						'Content-type': 'application/json'
					},
					body: JSON.stringify(payload)
				})
				.then(resp => resp.json())
				.then(data => {
					if (data && data.result && data.result !== true){
						return Promise.reject("Unable to send the registration details")
					}
				})
				.catch(err => {
					console.log("Error when sending the registration details", err)
				})
			}).catch(err => {
				console.log('Unable to register the service worker', err)
			})
	}

	async function askForPermission() {
		let permission = window.Notification.permission
		if (permission !== 'denied') {
			permission = await window.Notification.requestPermission();
		}
		return permission;
	}
	
	async function drawDialogControl(options) {
		const dialogControl =
		'<div class="perfecty-push-dialog-container" id="perfecty-push-dialog-container">' +
		'  <div class="perfecty-push-dialog-box">' +
		'    <div class="perfecty-push-dialog-title">' + options.title + '</div>' +
		'    <div>' + 
		'      <button id="perfecty-push-dialog-cancel" type="button" class="secondary">' + options.cancel + '</button>' +
		'      <button id="perfecty-push-dialog-subscribe" type="button" class="primary">' + options.submit + '</button> ' +
		'    </div>' +
		'  </div>' +
		'</div>';
		document.body.insertAdjacentHTML('beforeend', dialogControl);
	}

	function showDialogControl() {
		const control = document.getElementById('perfecty-push-dialog-container');
		control.style.display = "block";
	}

	function hideDialogControl() {
		const control = document.getElementById('perfecty-push-dialog-container');
		control.style.display = "none";
	}

	async function drawSettingsControl(options) {
		const svg = 'data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJiZWxsIiBjbGFzcz0ic3ZnLWlubGluZS0tZmEgZmEtYmVsbCBmYS13LTE0IiByb2xlPSJpbWciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDQ0OCA1MTIiPjxwYXRoIGZpbGw9ImN1cnJlbnRDb2xvciIgZD0iTTIyNCA1MTJjMzUuMzIgMCA2My45Ny0yOC42NSA2My45Ny02NEgxNjAuMDNjMCAzNS4zNSAyOC42NSA2NCA2My45NyA2NHptMjE1LjM5LTE0OS43MWMtMTkuMzItMjAuNzYtNTUuNDctNTEuOTktNTUuNDctMTU0LjI5IDAtNzcuNy01NC40OC0xMzkuOS0xMjcuOTQtMTU1LjE2VjMyYzAtMTcuNjctMTQuMzItMzItMzEuOTgtMzJzLTMxLjk4IDE0LjMzLTMxLjk4IDMydjIwLjg0QzExOC41NiA2OC4xIDY0LjA4IDEzMC4zIDY0LjA4IDIwOGMwIDEwMi4zLTM2LjE1IDEzMy41My01NS40NyAxNTQuMjktNiA2LjQ1LTguNjYgMTQuMTYtOC42MSAyMS43MS4xMSAxNi40IDEyLjk4IDMyIDMyLjEgMzJoMzgzLjhjMTkuMTIgMCAzMi0xNS42IDMyLjEtMzIgLjA1LTcuNTUtMi42MS0xNS4yNy04LjYxLTIxLjcxeiI+PC9wYXRoPjwvc3ZnPg==';
		const settingsControl =
		'<div class="perfecty-push-settings-container">' +
		'	 <div id="perfecty-push-settings-open">' +
		'    <img src="' + svg + '" alt="Settings" width="30"/>' +
		'  </div>' +
		'  <div id="perfecty-push-settings-form">' +
		' 	 <div>' + options.title + '</div>' +
		'    <input type="checkbox" id="perfecty-push-settings-subscribed">' + options.subscribed + '</input>' +
		'  </div>' +
		'</div>';
		document.body.insertAdjacentHTML('beforeend', settingsControl);
	}

	function showSettingsFormControl() {
		const control = document.getElementById('perfecty-push-settings-form');
		control.style.display = "block";
	}

	function hideSettingsFormControl() {
		const control = document.getElementById('perfecty-push-settings-form');
		control.style.display = "none";
	}

	function toggleSettingsFormControl() {
		const control = document.getElementById('perfecty-push-settings-form');
		const isDisplayed = control.style.display == 'block';

		control.style.display = isDisplayed ? 'none' : 'block';
		if (control.style.display == 'block') {
			listenToOutsideClick(control);
		}
	}

	function listenToOutsideClick (formControl) {
		// from jquery: https://github.com/jquery/jquery/blob/master/src/css/hiddenVisibleSelectors.js
		const isVisible = elem => !!elem && !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length );

		const documentClickListener = (event) => {
			if (!formControl.contains(event.target) && isVisible(formControl)) {
				toggleSettingsFormControl();
				removeClickListener();				
			}
		}
		const removeClickListener = () => {
			document.removeEventListener('click', documentClickListener);
		}

		document.addEventListener('click', documentClickListener);
	}

	// taken from https://github.com/mozilla/serviceworker-cookbook/blob/e912ace6e9566183e06a35ef28516af7bd1c53b2/tools.js
	function urlBase64ToUint8Array(base64String) {
		var padding = '='.repeat((4 - base64String.length % 4) % 4);
		var base64 = (base64String + padding)
			.replace(/\-/g, '+')
			.replace(/_/g, '/');
	 
		var rawData = window.atob(base64);
		var outputArray = new Uint8Array(rawData.length);
	 
		for (var i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	}
	
	async function perfectyStart(options) {
		if (checkFeatures()) {
			// Draw dialog
			drawDialogControl(options.dialogControl);
			drawSettingsControl(options.settingsControl);

	
			// Notification permission
			let permission = Notification.permission;
			let askedForNotifications = localStorage.getItem("perfecty_asked_notifications") === "yes";
			if (permission === 'default' && !askedForNotifications) {
				showDialogControl();
			}

			document.getElementById('perfecty-push-dialog-subscribe').onclick = async () => {
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideDialogControl();
				permission = await askForPermission();
				if (permission === 'granted'){
					// We only register the service worker and the push manager
					// when the user has granted permissions
					registerServiceWorker(options.path, options.siteUrl, options.vapidPublicKey, options.nonce);
				} else {
					console.log('Notification permission not granted')
				}
			};

			document.getElementById('perfecty-push-dialog-cancel').onclick = async () => {
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideDialogControl();
			}

			document.getElementById('perfecty-push-settings-open').onclick = async (e) => {
				e.stopPropagation();
				toggleSettingsFormControl();
			}

			document.getElementById('perfecty-push-settings-subscribed').onchange = async (event) => {
				const checked = event.target.checked;
				if (checked == true && permission === 'default') {
					showDialogControl();
					// Subscribe
				} else {
					// Unsubscribe
				}
			}
		} else {
			console.log('Browser doesn\'t support notifications');
		}
	}

	window.onload = () => {
		// defined outside in the html body
		const options = window.PerfectyPushOptions;
		perfectyStart(options);
	};