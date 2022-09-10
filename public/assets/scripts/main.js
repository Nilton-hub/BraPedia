import * as Notify from './helpers/notification.js';

const body = document.querySelector('body'),
    btnToggleMenu = document.querySelector('#btn-toggle-menu'),
    mainsidebar = document.querySelector('#main-sidebar-menu'),
    searchContainer = document.querySelector('.search-container'),
    btnSearch = document.getElementById('btn-search'),
    formSearch = document.querySelector('form.form-search'),
    baseUrl = 'http://localhost';

// SEARCH
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

// TOOGLE SIDEBAR MENU
const toggleMenu = etv => {
    mainsidebar.classList.toggle('active');
    sidebarNotification.classList.remove('active');
};
btnToggleMenu.addEventListener('click', toggleMenu);

// TOOGLE SIDEBAR NOTIFICATIONS
const sidebarNotification = document.querySelector('div.notification-sidebar');
const sidebarNotificationInner = document.querySelector('div.notification-sidebar button');
const buttonNotification = document.getElementById('notification-button');

const notificationToggle = e => {
    sidebarNotification.classList.toggle('active');
    mainsidebar.classList.remove('active');
};
buttonNotification.addEventListener('click', notificationToggle);
sidebarNotificationInner.addEventListener('click', notificationToggle);

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
Notify.notify();

/*
// COMO ADICIONAR UM NOVO COMPONENTE DE NOTIFICAÇÃO
const item_A = notificationTpl({
    username: 'Marina',
    msg: 'Muito bom!',
    photo: 'http://localhost/uploads/profile/1662282486-eu-pb.jpg',
    // url: `${baseUrl}/artigo/9#container-of-comment-25` // comment
    url: `${baseUrl}/artigo/9#comment-response-67` // response
}, 'Comentou no seu artigo');

document.querySelector("#main-sidebar-menu").append(item_A);
*/
