import * as Main from './main.js';
import { sendNotification } from './helpers/functions.js';

let articleOptions = document.getElementById('article-options'),
    alertPlaceholder = document.getElementById('liveAlertPlaceholder'),
    modalContentHidePost = document.querySelector('.modal-content-hide-post'),
    modalContentDeletePost = document.querySelector('.modal-content-delete-post'),
    btnArticleDelete = document.getElementById('btn-article-delete'),
    btnArticleHide = document.getElementById('btn-article-hide'),
    btnCommentActions = document.querySelectorAll('[class^="btn-comment-actions"]'),
    btnResponseActions = document.querySelectorAll('[class^="btn-response-actions"]'),
    commentActions = document.querySelectorAll('[class^="comment-actions"]'),
    formComment = document.querySelector('form#form-comment'),
    /* SHARED */
    modal = document.querySelector('#modals'),
    modalClose = () => {
        // MAIN MODAL
        if (!modal.classList.contains('d-none')) {
            modal.classList.add('d-none');
        }
        // delete post
        if (!modalContentDeletePost.classList.contains('d-none')) {
            modalContentDeletePost.classList.add('d-none');
        }
        if (!modalContentDeletePost.classList.contains('modal-show', 'modal-hidden')) {
            modalContentDeletePost.classList.replace('modal-show', 'modal-hidden');
        }
        // hide post
        if (!modalContentHidePost.classList.contains('d-none')) {
            modalContentHidePost.classList.add('d-none');
        }
        if (!modalContentHidePost.classList.contains('modal-show', 'modal-hidden')) {
            modalContentHidePost.classList.replace('modal-show', 'modal-hidden');
        }
    }
document.querySelector('#btn-close-modal').addEventListener('click', modalClose);
let showArticleOptions = false;

const toggleOptionsArticle = () => {
    if (articleOptions && articleOptions.classList.contains('toggle-opt-hidden') && !showArticleOptions) {
        articleOptions.classList.add('toggle-opt-show');
        articleOptions.classList.remove('toggle-opt-hidden');
        showArticleOptions = true;
    }

    let timeout = setTimeout(() => {
        showArticleOptions = false;
        if (articleOptions && articleOptions.classList.contains('toggle-opt-show')) {
            articleOptions.classList.remove('toggle-opt-show');
            articleOptions.classList.add('toggle-opt-hidden');
        }
        clearTimeout(timeout);
    }, 3000);
    setTimeout(() => clearTimeout(timeout), 3000);
}
document.addEventListener('scroll', toggleOptionsArticle);

const deletePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentDeletePost.classList.remove('d-none');
        modalContentDeletePost.classList.replace('modal-hidden', 'modal-show');
    }
};
if (btnArticleDelete) {
    btnArticleDelete.addEventListener('click', deletePost);
}

const hidePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentHidePost.classList.replace('modal-hidden', 'modal-show');
        modalContentHidePost.classList.remove('d-none');
    }
};
if (btnArticleHide) {
    btnArticleHide.addEventListener('click', hidePost);
}

// CLOSE COMMENT ACTIONS
window.addEventListener('click', e => {
    const element = e.target;
    if (
        !element.classList.contains('no-close-comment-opts') &&
        !element.classList.contains('btn-response-actions') &&
        !element.classList.contains('btn-comment-actions')
    ) {
        // if (!responseActions.classList.contains('d-none')) {
        //     responseActions.classList.add('d-none');
        // }
    }
});

// BTN COMMENT ACTIONS
function btnCommentShowActions() {
    btnCommentActions.forEach((value, key) => {
        let cssClass = value.getAttribute('class').replace('btn-', '');
        const showCommentOptions = (e) => {
            document.querySelector(`.${cssClass}`).classList.toggle('d-none');
        }
        value.addEventListener('click', showCommentOptions);
    });
}

btnCommentShowActions();

// SUBMIT COMMETN
const submitComment = (e) => {
    e.preventDefault();
    const article_id = formComment.article_id.value;
    const formData = new FormData(formComment);
    const commentText = formComment.comment.value;
    fetch(formComment.getAttribute('action'), {
        method: formComment.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            btnCommentShowActions();
            if (data.commentTpl) {
                document.querySelector('div.comment-list-container').innerHTML += data.commentTpl;
            }
            if (data.comment_id) {
                document.querySelector('#form-comment [name="comment"]').value = '';
                setTimeout(() => window.location.href = `${Main.baseUrl}/artigo/${article_id}#container-of-comment-${data.comment_id}`, 200);
            }
            if (data.channel) {
                const notificationData = {};
                notificationData.url = `http://localhost/artigo/${formComment.article_id.value}#container-of-comment-${notificationData.comment_id}`;
                notificationData.photo = data.photo;
                notificationData.username = formComment.name.value;
                notificationData.msg = commentText;
                notificationData.comment_id = data.comment_id;
                notificationData.element_id = formComment.article_id.value;
                notificationData.id = data.id;
                sendNotification(data.channel, notificationData);
                console.log(data.comment_id);
            }
        })
        .catch(error => {
            message(`Para comentar é necessário fazer login! <a href="${baseUrl}/login" title="Entrar">Entrar</a>`, 'danger');
            console.error(error)
        });
}
formComment.addEventListener('submit', submitComment);

// EDIT COMMENTS
const commentActionEdit = (id) => {
    let comment = document.getElementById(`post-comment-${id}`);
    let formComment = document.getElementById('form-comment-response-' + id);
    let btnEdit = document.getElementById(`btn-comment-update-${id}`);
    let btnCancel = document.getElementById(`btn-comment-delete-${id}`);
    let divBtnOptions = document.querySelector(`#container-of-comment-${id} .comment-actions-div`);
    divBtnOptions.classList.remove('d-none');
    const hideDivBtnOptions = () => {
        comment.removeAttribute('contenteditable');
        comment.blur();
        divBtnOptions.classList.add('d-none');
    }
    btnCancel.onclick = hideDivBtnOptions;
    comment.setAttribute('contenteditable', 'true');
    comment.focus();
    formComment.classList.add('mt-5');
    btnEdit.onclick = (e) => {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', 'edit');
        formData.append('text', comment.innerText);
        fetch(`${Main.baseUrl}/comment/action`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(data => {
                hideDivBtnOptions()
            })
            .catch(err => {
                console.log(err);
            });
    };
}

const btnsComentEdit = document.querySelectorAll(`[id^="commentActionEdit-"]`);
btnsComentEdit.forEach((button) => {
    button.addEventListener('click', evt => {
        let id = evt.target.id;
        let pos = parseInt(id.search('-'));
        id = id.substring(pos + 1);
        commentActionEdit(id);
    });
});

// DELETE COMMENT
const commentActionDelete = (id) => {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'delete');
    fetch(`${Main.baseUrl}/comment/action`, {
        method: 'POST',
        body: formData
    })
        .then(res => res.text())
        .catch(error => console.error(error));
    document.getElementById(`container-of-comment-${id}`).remove();
}

const btnsComentDelete = document.querySelectorAll(`[id^="commentActionDelete-"]`);
btnsComentDelete.forEach((button) => {
    button.addEventListener('click', evt => {
        let id = evt.target.id;
        let pos = parseInt(id.search('-'));
        id = id.substring(pos + 1);
        commentActionDelete(id);
    });
});

// BTN RESPONSE ACTIONS
btnResponseActions.forEach((value, key) => {
    let responseActions = document.querySelector(`div.${value.getAttribute('class').replace('btn-', '')}`);
    const showCommentOptions = (e) => {
        responseActions.classList.toggle('d-none');
    }
    value.addEventListener('click', showCommentOptions);
});

// EDIT RESPONSES
function responseActionEdit(id) {
    const spanRepply = document.querySelector(`#comment-response-${id} span`),
        divResponseActions = document.getElementById(`div-response-actions-${id}`),
        btnUpdateRepply = document.querySelector(`#div-response-actions-${id} .btn-response-action-save`);
    spanRepply.setAttribute('contenteditable', 'true');
    divResponseActions.classList.remove('d-none');
    spanRepply.focus();
    const actionEdit = (e) => {
        focusRemve();
        const formData = new FormData();
        formData.append('repply_id', id);
        formData.append('action', 'edit');
        formData.append('text', spanRepply.innerText);
        fetch(`${Main.baseUrl}/comment/repply-actions`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(data => {
                console.log(data)
            })
            .catch(error => {
                console.log(error)
            });
        btnUpdateRepply.removeEventListener('click', actionEdit);
    }

    function focusRemve() {
        spanRepply.removeAttribute('contenteditable');
        divResponseActions.classList.add('d-none');
        spanRepply.blur();
        btnUpdateRepply.removeEventListener('click', actionEdit);
    }

    document.querySelector(`#div-response-actions-${id} .btn-response-action-cancel`)
        .addEventListener('click', focusRemve);
    btnUpdateRepply.addEventListener('click', actionEdit);
}

const btnsResponsesEdit = document.querySelectorAll('[id^="responseActionEdit-"]');
btnsResponsesEdit.forEach(button => {
    button.addEventListener('click', evt => {
        let id = evt.target.id;
        const pos = parseInt(id.search('-'));
        id = id.substring(pos + 1);
        responseActionEdit(id);
    });
});

// DELETE RESPONSE
function responseActionDelete(id) {
    const formData = new FormData();
    formData.append('repply_id', id);
    formData.append('action', 'delete');
    fetch(`${Main.baseUrl}/comment/repply-actions`, {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`response-container-${id}`).remove();
            }
        })
        .catch(err => console.error(err));
}

const btnsResponsesDelete = document.querySelectorAll('[id^="responseActionDelete-"]');
btnsResponsesDelete.forEach(button => {
    button.addEventListener('click', evt => {
        const pos = parseInt(button.id.search('-'));
        const id = button.id.substring(pos + 1);
        responseActionDelete(id);
    });
});

// RESPONSE SUBMIT
const responseSubmit = (e) => {
    e.preventDefault();
    const formRepply = e.target;
    const commentId = formRepply.comment_id.value;
    const formData = new FormData(formRepply);
    fetch(formRepply.getAttribute('action'), {
        method: formRepply.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.tpl) {
                document.getElementById(`response-list-container-${commentId}`).innerHTML += data.tpl;
                formRepply.reset();
            }
        })
        .catch(err => {
            console.error(err)
        });
}

const formsResponse = document.querySelectorAll('[id^="form-comment-response-"]');
formsResponse.forEach((value) => {
    value.addEventListener('submit', responseSubmit);
});
