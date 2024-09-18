/**
 * Process the Manual build by language selector
 */

let updateHTML = function(event) {
	event.preventDefault();
	if(confirm('On first use this action may take several minutes. On rebuild it should be quick.')) {
		// If there is an alert open - find and close it.
		if (false) {
			this.dispatchEvent(new CustomEvent('joomla.alert.close'));
			this.style.animationName = 'joomla-alert-fade-out';
		}
		let url = '?option=com_jdocmanual&task=sources.buildhtml&manual=' + this.id + '&language=' + this.value;
		location = url;
	}
	return false;
}
  
let links = document.querySelectorAll('.buildhtml');
for (let i = 0; i < links.length; i += 1) {
	links[i].addEventListener('change', updateHTML, false);
}


