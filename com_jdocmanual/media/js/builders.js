/**
 * Process the Manual build by language selector
 */

let updateHTML = function (event) {
    event.preventDefault();
    if (confirm('On first use this action may take several minutes. On rebuild it should be quick.')) {
        // Set an alert message using the system message container.
        const elem = document.getElementById('system-message-container');
        elem.innerHTML = '<div class="alert alert-info text-center">Please Wait!</div>';
        let url = '?option=com_jdocmanual&task=sources.buildhtml&manual=' + this.id + '&language=' + this.value;
        location = url;
    }
    return false;
}

let links = document.querySelectorAll('.buildhtml');
for (let i = 0; i < links.length; i += 1) {
    links[i].addEventListener('change', updateHTML, false);
}

let gitPull = function (event) {
    event.preventDefault();
    let url = '?option=com_jdocmanual&task=sources.gitpull&manual=' + this.id + '&language=' + this.value;
    location = url;
}

let plinks = document.querySelectorAll('.gitpull');
for (let i = 0; i < plinks.length; i += 1) {
    plinks[i].addEventListener('change', gitPull, false);
}
