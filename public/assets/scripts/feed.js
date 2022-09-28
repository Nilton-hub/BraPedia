import * as Main from './main.js';
import {baseUrl} from "./main.js";
import { sendNotification, strRemume } from "./helpers/functions.js";

const topLink = document.querySelector('[href="#main-header"]'),
    bottomReference = document.querySelector('#bottom-reference'),
    btnsToggleComments = document.querySelectorAll('p.btn-toggle-comments'),
// comment and response options
    btnResponseActions = document.querySelectorAll('.btn-response-actions'),
    responseActions = document.querySelector('.response-actions');

// BTN PAGE TOP
let scrollIterator = 0;
topLink.style.opacity = '0';
topLink.style.transition = 'all 0.3s';
window.addEventListener('scroll', (e) => {
    if (bottomReference.getClientRects()[0].top <= -250) {
        topLink.style.opacity = '1';
    } else {
        topLink.style.opacity = '0';
    }
    scrollIterator++;
});

document.querySelectorAll('h4.article-card-title a')
    .forEach(element => {
        element.innerHTML = strRemume(element.innerText);
    });

// BTN TOGGLE COMENTS
btnsToggleComments.forEach(value => {
    const paragraphText = ['Exibir todos os comentários...', 'Ocultar todos os comentários'];
    value.innerHTML = paragraphText[0];
    const id = value.getAttribute('id');
    const toggleComments = () => {
        let currentCommentsContainer = document.querySelector(`#${id} ~ div[id^="comments-container-"]`);
        currentCommentsContainer.classList.toggle('comments-show');
        if (value.innerHTML === paragraphText[0]) {
            value.innerHTML = paragraphText[1];
        } else {
            value.innerHTML = paragraphText[0];
        }
    }

    value.addEventListener('click', toggleComments);
})

const closeCommentResponseAction = (elementActions) => {
    window.addEventListener('click', e => {
        const element = e.target;
        if (
            !element.classList.contains('no-close-comment-opts') &&
            !element.classList.contains('btn-response-actions') &&
            !element.classList.contains('btn-comment-actions')
        ) {
            if (!elementActions.classList.contains('d-none')) {
                elementActions.classList.add('d-none');
            }
        }
    });
}

// COMMENT ACTIONS
const toggleComments = (id, commentid) => {
    const commentActions = document.querySelector(`#comments-container-${id} .comment-actions-${commentid}`);
    closeCommentResponseAction(commentActions);
    commentActions.classList.toggle('d-none');
}

const buttonsCommentOptions = document.querySelectorAll('[id^="container-of-comment-"] div.btn-comment-actions');
buttonsCommentOptions.forEach(button => {
    button.addEventListener('click', evt => {
        const articleid = button.getAttribute('data-articleid');
        const commentid = button.getAttribute('data-commentid');
        toggleComments(articleid, commentid);
    });
});

// COMMENT SUBMIT
const formsArtcilecomment = document.querySelectorAll('.form-article-commemnt');
formsArtcilecomment.forEach((element) => {
    const commentAction = (e) => {
        e.preventDefault();
        const formData = new FormData(element);
        const commentText = element.comment.value;
        fetch(`${element.getAttribute('action')}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.commentTpl) {
                    document.getElementById(`comments-container-${element.article_id.value}`).innerHTML += data.commentTpl;
                    element.childNodes[3].childNodes[5].value = '';
                }
                if (data.channel) {
                    const notificationData = {};
                    notificationData.url = `${baseUrl}/artigo/${element.article_id.value}#container-of-comment-${notificationData.comment_id}`;
                    notificationData.photo = data.photo;
                    notificationData.username = element.name.value;
                    notificationData.msg = commentText;
                    notificationData.comment_id = data.comment_id;
                    notificationData.element_id = element.article_id.value;
                    notificationData.id = data.id;
                    sendNotification(data.channel, notificationData);
                }
            })
            .catch(err => { console.log(err) });
    }
    element.addEventListener('submit', commentAction);
});

// TO EDIT COMMENT
function commentActionEdit(id) {
    let comment = document.getElementById(`post-comment-${id}`);
    let btnUpdate = document.getElementById(`btn-update-comment-${id}`);
    let btnCancel = document.getElementById(`btn-cancel-comment-${id}`);

    const editCancel = () => {
        btnUpdate.parentNode.style.display = 'none';
        btnUpdate.parentNode.style.zIndex = '';
        comment.removeAttribute('contenteditable');
        comment.style.width = '100%';
        btnUpdate.removeEventListener('click', sendRequest);
    };
    if (btnCancel)
        btnCancel.addEventListener('click', ev => editCancel);
    if (btnUpdate && comment) {
        btnUpdate.parentNode.style.display = 'inline-block';
        btnUpdate.parentNode.style.zIndex = '1';
        comment.setAttribute("contenteditable", "true");
        comment.style.width = 'calc(100% - 70px)';
        comment.focus();
        document.querySelector(`.comment-actions-${id}`).classList.add('d-none');
    }

    const sendRequest = () => {
        if (comment.innerText.length === 0) {
            Main.message('Não é possível enviar enviar um comentário vazio.', 'danger');
            return;
        }
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'edit');
        formData.append('text', comment.innerText);
        fetch(`${Main.baseUrl}/comment/action`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(data => { console.log(data) })
            .catch(err => { console.log(err) });
        editCancel();
        btnUpdate.removeEventListener('click', sendRequest);
    }
    if (btnUpdate && btnCancel) {
        btnUpdate.addEventListener('click', sendRequest);
        btnCancel.onclick = (e) => {
            btnUpdate.parentNode.style.display = 'none';
            comment.removeAttribute('contenteditable');
            comment.style.width = '100%';
            btnUpdate.removeEventListener('click', sendRequest);
        };
    }
}

const btnLiEdit = document.querySelectorAll('[id^="container-of-comment-"] .opt-edit');
btnLiEdit.forEach(li => {
    li.addEventListener('click', evt => {
        const id = li.getAttribute('data-commentid');
        commentActionEdit(id);
    });
});

// TO REMOVER COMMENT
function commentActionRemove(id) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'delete');
    fetch(`${Main.baseUrl}/comment/action`, {
        method: 'POST',
        body: formData
    })
        .then(res => res.text())
        .then(data => { console.log(data) })
        .catch(err => { console.log(err) });
    const containerOfComment = document.getElementById(`container-of-comment-${id}`);
    if (containerOfComment)
        containerOfComment.remove();
}

const btnLiRemove = document.querySelectorAll('[id^="container-of-comment-"] .opt-delete');
btnLiRemove.forEach(li => {
    li.addEventListener('click', evt => {
        const id = li.getAttribute('data-commentid');
        commentActionRemove(id);
    });
});

//RESPONSE SUBMIT
const responseSubmit = (e) => {
    e.preventDefault();
    const formRepply = e.target;
    const formData = new FormData(formRepply);
    fetch(`${formRepply.getAttribute('action')}`, {
        method: formRepply.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.tpl) {
                document.getElementById(`response-list-container-${formRepply.comment_id.value}`).innerHTML += data.tpl;
            }
        })
        .catch(error => { console.log(error) });
}
const formsResponse = document.querySelectorAll('form[class^="form-response-"]');
formsResponse.forEach((formResponse) => {
    formResponse.addEventListener('submit', responseSubmit);
});

// RESPONSE ACTIONS
const toggleResponse = (id, responseId) => {
    const responseActions = document.querySelector(`#comments-container-${id} .response-actions-${responseId}`);
    responseActions.classList.toggle('d-none');
}

const btnsDivResponse = document.querySelectorAll('[id^=btn-response-actions-]');
btnsDivResponse.forEach(div => {
    div.addEventListener('click', evt => {
        const articleid = div.getAttribute('data-articleid');
        const responseid = div.getAttribute('data-responseid');
        toggleResponse(articleid, responseid);
    });
});

// EDIT REPPLY
function commentRepplyEdit(id) {
    const repply = document.getElementById(`comment-repply-${id}`);
    const btnSend = document.getElementById('btn-coment-reppy-send-' + id);
    btnSend.parentNode.classList.replace('d-none', 'd-block');
    repply.setAttribute('contenteditable', true);
    repply.focus();
    function removeEditable () {
            repply.removeAttribute('contenteditable');
            repply.blur();
            btnSend.parentNode.classList.replace('d-block', 'd-none');
    }
    document.getElementById('btn-coment-reppy-cancel-' + id)
        .addEventListener('click', removeEditable);

    btnSend.addEventListener('click', ev => {
            removeEditable();
            let formData = new FormData();
            formData.append('repply_id', id);
            formData.append('action', 'edit');
            formData.append('text', repply.innerText);
            fetch(`${Main.baseUrl}/comment/repply-actions`, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .catch(error => { console.log(error); });
        });
}

const btnsLisRepply = document.querySelectorAll('[id^="comment-repply-container-"] .opt-edit');
btnsLisRepply.forEach(li => {
    li.addEventListener('click', evt => {
        const id = li.getAttribute('data-responseid');
        commentRepplyEdit(id)
    });
});

// DELETE REPPLY
function commentRepplyDelete(id) {
    let formData = new FormData();
    formData.append('repply_id', id);
    formData.append('action', 'delete');
    fetch(`${Main.baseUrl}/comment/repply-actions`, {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`comment-repply-container-${id}`).remove();
            }
        })
        .catch(error => { console.log(error); });
}

const btnsLisResponseDelete = document.querySelectorAll('[id^="comment-repply-container-"] .opt-delete');
btnsLisResponseDelete.forEach(li => {
    li.addEventListener('click', evt => {
        const id = li.getAttribute('data-responseid');
        commentRepplyDelete(id);
    })
});
