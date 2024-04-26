
/**
 * setCookie for consistent cookie handling
 *
 */
function setCookie(name, value, days) {
  let expires = "";
  let date = new Date();
  // is the siteroot set for this template
  let paths = Joomla.getOptions(["system.paths"], "No good");
  let root = paths.root;
  let path = "; path=" + root;

  const samesite = "; samesite=None; secure=true";
  let baseFull = paths.baseFull; // "http:\/\/localhost\/j4ops\/"

  if (days) {
    date.setTime(date.getTime()+(days*24*60*60*1000));
  } else {
    date.setTime(date.getTime());
  }
  expires = "; expires="+date.toGMTString();
  if (typeof root === undefined) {
    path = "; path=/";
  }
  document.cookie = name + "=" + value + expires + path + samesite;
}

/**
 * getCookie - return cookie by name
 */
function getCookie(name) {
  let nameEQ = name + "=";
  let ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i += 1) {
    let c = ca[i];
    while (c.charAt(0) === " ") {
      c = c.substring(1,c.length);
    }
    if (c.indexOf(nameEQ) === 0) {
      return c.substring(nameEQ.length,c.length);
    }
  }
  return null;
}

/**
 * eraseCookie by name
 */
function eraseCookie(name) {
  setCookie(name,'',0);
}

/**
 * Joomla menu toggle - hide or show the Joomla menu
 */

let toggle = document.getElementById('toggle-joomla-menu');

if(toggle) {
  toggle.addEventListener('click', function() {
    let wrapper = document.getElementById('sidebar-wrapper');
    let style = getComputedStyle(wrapper);
    if (style.display === 'none') {
      wrapper.classList.remove('d-none');
    } else {
      wrapper.classList.add('d-none');
    }
  });
}

/**
 * Set the page content by clicking a page item in the index
 */
//let contents = document.getElementsByClassName("content-link");

let getPage = function(event) {
  event.preventDefault();
  // this contains the full url of the link
  let url = new URL(this);
  let paramsString = url.search;
  let searchParams = new URLSearchParams(paramsString);
  let manual = searchParams.get('manual');
  let heading = searchParams.get('heading');
  let filename = searchParams.get('filename');
  setPanelContent(manual, heading, filename);
  // add the highlight class for the selected index item
  this.parentElement.classList.add("article-active");
  setlinks();
};

/**
 * Set the page content by clicking a link in the page
 */
setlinks();

function setlinks() {
    let links = document.querySelectorAll('a[href*="filename="]');
    for (let i = 0; i < links.length; i += 1) {
        links[i].addEventListener('click', getPage, false);
    }
}

/**
 * Fetch the selected page from source.
 */
async function setPanelContent(manual, heading, filename) {
  let document_title = document.getElementById('document-title');
  if (!document_title) {
    return;
  }

  // remove the highlight class from the selected index item
  let index_items = document.getElementsByClassName('article-active');
  [].forEach.call(index_items, function(el) {
    el.classList.remove("article-active");
  });

  let document_panel = document.getElementById('document-panel');
  document_panel.innerHTML = `<div class="text-center">
    <div class="spinner-border m-5" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>`;
  let toc_panel = document.getElementById('toc-panel');

  // get token from javascript loaded in the page
  const token = Joomla.getOptions('csrf.token', '');
  let url = 'index.php?option=com_jdocmanual&task=content.fillpanel';
  let data = new URLSearchParams();

  let new_cookie = heading + '--' + filename;
  setCookie('jdm' + manual, new_cookie, 10);

  data.append('manual', manual);
  data.append('heading', heading);
  data.append('filename', filename);
  data.append(token, 1);
  const options = {
    body: data,
    method: 'POST'
  };
  let response = await fetch(url, options);
  if (!response.ok) {
    document_panel.innerHTML = response.status;
    throw new Error (Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS'));
  } else {
    let result = await response.text();
    let obj = JSON.parse(result);
    toc_panel.innerHTML = obj[0];
    document_panel.innerHTML = obj[1];
    document_title.innerHTML = obj[2];
    setlinks();
    menuHighlight(heading, filename);
  }
}

// the default index location - set on page load
let indexLocation = 'oncanvas';

function setIndexLocation () {
  const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
  // if the width is less than 576 move the index off canvas
  let offcanvasId = document.getElementById('offcanvasMenu');
  let oncanvasId = document.getElementById('oncanvasMenu');
  let jdocmanulId = document.getElementById('jdocmanual-wrapper');
  if (vw < 576) {
    if (indexLocation == 'oncanvas') {
      offcanvasId.appendChild(jdocmanulId);
      indexLocation = 'offcanvas';
    }
  } else {
    if (indexLocation == 'offcanvas') {
      oncanvasId.appendChild(jdocmanulId);
      indexLocation = 'oncanvas';
    }
  }
}

function menuHighlight(heading, filename) {
  let link = document.querySelector('a[href*="heading=' + heading + '&filename=' + filename + '"]');
  link.parentElement.classList.add("article-active");
  // Expand the nearest <details> tag.
  el = link.closest("details");
  el.setAttribute('open', '');
}

/**
 * After page load set the active menu and open its accordion panel.
 */
document.addEventListener('DOMContentLoaded', function(event) {
  // Get the heading and filename from the cookies.
  let jdmcur = getCookie('jdmcur');
  let manual = 'jdm' + jdmcur.split('-')[0];
  let handf = getCookie(manual).split('--');
  menuHighlight(handf[0], handf[1]);
  setIndexLocation();
});

window.addEventListener('resize', setIndexLocation);