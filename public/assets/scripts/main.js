import notificationTpl from './components/notification-tpl.js';

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
async function getChannels() {
    const request = await fetch(`${baseUrl}/notify`);
    return await request.json();
}

var conn = new ab.Session('ws://localhost:8080',
    function() {
        //article_Assincronismo_com_9_3
        getChannels().then(response => {
                response.map(channel => {
                    conn.subscribe(channel, (topic, data) => {
                        data = JSON.parse(data);
                        let notifyTpl;
                        switch (topic.split('_')[0]) {
                            case 'article':
                                notifyTpl = notificationTpl({
                                    username: data.username,
                                    msg: data.msg,
                                    photo: `${baseUrl}/uploads/profile/${data.photo}`, // 1662282486-eu-pb.jpg
                                    url: `${baseUrl}/artigo/9#${data.comment_id}` // comment
                                }, 'Comentou no seu artigo');
                                break;
                            case 'comment':
                                console.log(`${data.username} respondeu seu comentário: \"${data.msg}\"`);
                                // ${data.username} respondeu seu comentário: "Bla blá blá"
                                break;
                            case 'commentRepply':
                                console.log(`${data.username} respondeu seu comentário: \"${data.msg}\"`);
                                // ${data.username} respondeu seu comentário: "Bla blá blá"
                                break;
                        }
                        document.querySelector("#main-sidebar-menu").append(notifyTpl);
                    });
                });
            });
        /*conn.subscribe('kittensCategory', function(topic, data) {
            console.log('New article published to category "' + topic + '" : ' + data.title);
        });*/
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);

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
