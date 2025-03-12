import 'media-chrome'
import 'media-chrome/menu'


const cookieConsent = localStorage.getItem('oak-marketing-cookies-consent') === 'true'
function setCookieConsent() {
	localStorage.setItem('oak-marketing-cookies-consent', 'true')
}


const containers = document.querySelectorAll('[data-oak-marketing-cookies-consent]')

function showContent() {
	document
		.querySelectorAll('[data-oak-marketing-cookies-consent] iframe')
		.forEach(iframe => {
			iframe.src = iframe.dataset.src
		})

	document
		.querySelectorAll('[data-oak-marketing-cookies-consent-banner]')
		.forEach(banner => banner.remove())
}


if (cookieConsent) {
	showContent()
}
else {
	for (const container of containers) {
		container
			.querySelector('[data-oak-marketing-cookies-consent-button]')
			.addEventListener('click', () => {
				setCookieConsent()
				showContent()
			})
	}
}
