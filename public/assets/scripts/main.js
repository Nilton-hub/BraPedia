import * as Notify from './helpers/notification.js';
import notification from "./components/notification.js";
import { login } from "./helpers/functions.js";

export const body = document.querySelector('body'),
    btnToggleMenu = document.querySelector('#btn-toggle-menu'),
    mainsidebar = document.querySelector('#main-sidebar-menu'),
    searchContainer = document.querySelector('.search-container'),
    btnSearch = document.getElementById('btn-search'),
    formSearch = document.querySelector('form.form-search'),
    notficationsCountElement = document.querySelector('#main-nav-menu .notification-count'),
    baseUrl = 'http://brapedia.infinityfreeapp.com/public';

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

// SEARCH SUBMIT
formSearch.addEventListener('submit', (e) => {
    e.preventDefault();
    const search = formSearch.search.value;
    if (search.length === 0) {
        return;
    }
    window.location.href = `${baseUrl}/search/${search}`;
});

// NOTIFICATIONS
const loadNotifications = async () => {
    try {
        const req = await fetch(`${baseUrl}/notifications`);
        return await req.json();
    } catch (error) {
        return error;
    }
};

loadNotifications().then(data => {
    if (login()) {
        let totNewsNotifies = 0;
        data.forEach(d => {
            if (!Number(d.view)) {
                totNewsNotifies++;
            }
            const notify = notification(d, d.content);
            const notificationContaier = document.querySelector('div.notification-container');
            if (notificationContaier)
                notificationContaier.append(notify);
        });
        if (totNewsNotifies > 0 && totNewsNotifies <= 9) {
            notficationsCountElement.innerHTML = totNewsNotifies;
        }
        if (totNewsNotifies > 9) {
            notficationsCountElement.innerHTML = '+9';
        }
    }
});

// WEBSOCKET CONNECTION BY NOTIFICATIONS
Notify.notify();

// TOOGLE SIDEBAR MENU
const toggleMenu = etv => {
    mainsidebar.classList.toggle('active');
    if (sidebarNotification !== null)
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
    if (sidebarNotification.classList.contains('active')) {
        notficationsCountElement.innerHTML = '';
        fetch(`${baseUrl}/clear-notifications`, {
            method: 'POST'
        });
    }
};

if (buttonNotification) {
    buttonNotification.addEventListener('click', notificationToggle);
    sidebarNotificationInner.addEventListener('click', notificationToggle);
}

// FUNCTIONS
//message
function closeAlert() {
    wrapper.innerHTML = '';
}

const alertPlaceholder = document.getElementById('liveAlertPlaceholder');

const wrapper = document.createElement('div');
export const message = (message, type) => {
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('');
    alertPlaceholder.append(wrapper);
}
