const body = document.querySelector('body'),
    btnToggleMenu = document.querySelector('#btn-toggle-menu'),
    mainsidebar = document.querySelector('#main-sidebar-menu'),
    searchContainer = document.querySelector('.search-container'),
    btnSearch = document.getElementById('btn-search'),
    formSearch = document.querySelector('form.form-search'),
    baseUrl = 'http://localhost';

searchContainer.style.display = 'flex';
const showSearchContainer = evt => {
    searchContainer.classList.toggle('search-show');
    const toggleSearchContainer = document.getElementById('toggle-search-container');
    const inputSearch = document.querySelector('input#input-article-search');
    let isDisabled = inputSearch.getAttributeNode('disabled');
    if (isDisabled) {
        inputSearch.removeAttribute('disabled');
        inputSearch.focus();
    } else {
        inputSearch.setAttribute('disabled', '');
    }
    toggleSearchContainer.classList.toggle('icon-x');
    toggleSearchContainer.classList.toggle('icon-search');
}
btnSearch.addEventListener('click', showSearchContainer);

window.addEventListener('click', evt => {
    if (evt.srcElement !== searchContainer && evt.srcElement !== btnSearch && evt.target !== searchContainer && evt.target !== btnSearch) {
        // console.log(evt.srcElement);
    }
});

// SEARCH SUBMIT
formSearch.addEventListener('submit', (e) => {
    e.preventDefault();
    const search = formSearch.search.value;
    if (search.length === 0) {
        return;
    }
    window.location.href = `${baseUrl}/search/${search}`;
});

const toggleMenu = etv => {
    mainsidebar.classList.toggle('active');
    if (mainsidebar.classList.contains('active')) {

    } else {

    }
};
btnToggleMenu.addEventListener('click', toggleMenu);

// FUNCTIONS
//submit forms
function sendRequest(url, init) {
    let req = fetch(url, init)
        .then(response => {
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(error => {
            console.error(error);
        });
    return req;
}

//message
const alertPlaceholder = document.getElementById('liveAlertPlaceholder');

const wrapper = document.createElement('div');
const message = (message, type) => {
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" onclick="closeAlert()" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('');
    alertPlaceholder.append(wrapper);
}

function closeAlert() {
    wrapper.innerHTML = '';
}

// WEBSOCKET CONNECTION BY NOTIFICATIONS
var conn = new ab.Session('ws://localhost:8080',
    function() {
        conn.subscribe('kittensCategory', function(topic, data) {
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New article published to category "' + topic + '" : ' + data.title);
        });
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);
