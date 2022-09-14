import * as Main from './main.js';

const btnProfileEdit = document.querySelector('#btn-profile-edit'),
    alertPlaceholder = document.getElementById('liveAlertPlaceholder'),
    bntChangePassword = document.querySelector('.btn-change-password'),
    modalContentChangePassword = document.querySelector('.modal-content-change-password'),
    modalContentProfileEdit = document.querySelector('.modal-content-edit'),
    modalContentHidePost = document.querySelector('.modal-content-hide-post'), //
    modalContentDeletePost = document.querySelector('.modal-content-delete-post'), //
    btnsHide = document.querySelectorAll('article[data-post-id] li.post-hide'),
    btnsDelete = document.querySelectorAll('article[data-post-id] li.post-delete'),
    /* SHARED */
    modal = document.querySelector('#modals'),
    modalClose = () => {
        // MAIN MODAL
        if (!modal.classList.contains('d-none')) {
            modal.classList.add('d-none');
        }
        // change password
        if (!modalContentChangePassword.classList.contains('modal-show', 'modal-hidden')) {
            modalContentChangePassword.classList.replace('modal-show', 'modal-hidden');
        }
        if (!modalContentChangePassword.classList.contains('d-none')) {
            modalContentChangePassword.classList.add('d-none');
        }
        // profile edit
        if (!modalContentProfileEdit.classList.contains('modal-show', 'modal-hidden')) {
            modalContentProfileEdit.classList.replace('modal-show', 'modal-hidden');
        }
        if (!modalContentProfileEdit.classList.contains('d-none')) {
            modalContentProfileEdit.classList.add('d-none');
        }
        // hide post
        if (!modalContentHidePost.classList.contains('d-none')) {
            modalContentHidePost.classList.add('d-none');
        }
        if (!modalContentHidePost.classList.contains('modal-show', 'modal-hidden')) {
            modalContentHidePost.classList.replace('modal-show', 'modal-hidden');
        }
        // delete post
        if (!modalContentDeletePost.classList.contains('d-none')) {
            modalContentDeletePost.classList.add('d-none');
        }
        if (!modalContentDeletePost.classList.contains('modal-show', 'modal-hidden')) {
            modalContentDeletePost.classList.replace('modal-show', 'modal-hidden');
        }
    };

// PROFILE EDIT
document.querySelector('#btn-close-modal').addEventListener('click', modalClose);
btnProfileEdit.addEventListener('click', () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentProfileEdit.classList.replace('modal-hidden', 'modal-show');
        modalContentProfileEdit.classList.remove('d-none');
    }
    const profileEdit = document.querySelector('form.user-profile-edit');
    profileEdit.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(profileEdit);
        fetch(`${profileEdit.getAttribute('action')}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.message) {
                    alertPlaceholder.innerHTML = data.message;
                }
                if (data.success) {
                    document.getElementById('main-user-name').innerText = profileEdit.name.value;
                    document.getElementById('main-user-email').innerText = profileEdit.email.value;
                    modalClose();
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
});

// CHANGE PASSWORD
bntChangePassword.addEventListener('click', () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentChangePassword.classList.replace('modal-hidden', 'modal-show');
        modalContentChangePassword.classList.remove('d-none');
    }
    const formChangePassword = document.querySelector('form.form-change-password');

    formChangePassword.addEventListener('submit', (e) => {
        const formData = new FormData(formChangePassword);
        e.preventDefault();
        fetch(`${formChangePassword.getAttribute('action')}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.message) {
                    alertPlaceholder.innerHTML = data.message;
                }
            })
            .catch(error => {
                console.log(error)
            })
    });
});

document.getElementById('btn-delete-article-cancel').onclick = () => {
    modalClose();
};
// TO HIDDEN POST
const hidePost = (e) => {
    const modalConfig = [
        'Ao exibir um artigo, ele aparecerá também no feed para os outros usuários.',
        'Ao ocultar um artigo, ele não aparecerá mais no feed para os outros usuários. Mas você ainda poderá vê-lo no seu Perfil.'
    ];
    let textAlert;
    let btn = e.target;
    const divModal = document.querySelector('.modal-content-hide-post');
    if (e.target.getAttribute('href') === null) {
        btn = btn.parentNode;
    }
    let articleStatus = btn.getAttribute('data-status');
    const btnConfirm = document.getElementById('btn-hidde-article-confirm');
    if (articleStatus === '0' || articleStatus === 0) {
        textAlert = modalConfig[1];
        btnConfirm.innerText = 'Ocultar';
    } else {
        textAlert = modalConfig[0];
        btnConfirm.innerText = 'Exibir';
    }
    divModal.childNodes[1].innerText = textAlert;
    const toggleHidePost = (e) => {
        let formData = new FormData();
        formData.append('id', btn.getAttribute('data-article-id'));
        fetch(`${Main.baseUrl}/artigo/ocultar/${btn.getAttribute('data-article-id')}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                modalClose();
                if (data.success !== 'undefined' && data.success === 1) {
                    if (btn.childNodes[0].classList.contains('icon-eye')) {
                        btn.childNodes[0].classList.replace('icon-eye', 'icon-blocked');
                        btn.setAttribute('title', 'Não ocultar');
                        btnConfirm.innerText = 'Exibir';
                    } else {
                        btn.childNodes[0].classList.replace('icon-blocked', 'icon-eye');
                        btn.setAttribute('title', 'Ocultar');
                        btnConfirm.innerText = 'Ocultar';
                    }
                    if (articleStatus === 0 || articleStatus === '0') {
                        btn.setAttribute('data-status', 1);
                    } else {
                        btn.setAttribute('data-status', 0);
                    }
                }
            })
            .catch(error => {
                console.error(error)
            });
    }
    btnConfirm.addEventListener('click', toggleHidePost);
    document.querySelector('#btn-close-modal').onclick = () => {
        btnConfirm.removeEventListener('click', toggleHidePost);
    }
    document.getElementById('btn-hidde-article-cancel').onclick = () => {
        btnConfirm.removeEventListener('click', toggleHidePost);
        modalClose();
    };
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentHidePost.classList.replace('modal-hidden', 'modal-show');
        modalContentHidePost.classList.remove('d-none');
    }
};
btnsHide.forEach(element => {
    document.getElementById('btn-hidde-article-cancel').onclick = () => {
        modalClose()
    };
    element.addEventListener('click', hidePost);
});

// POST DELETE
const deletePost = (e) => {
    let btn = e.target;
    const btnConfirm = document.getElementById('btn-delete-article-confirm');
    const btnCancel = document.getElementById('btn-delete-article-cancel');
    if (btn.getAttribute('href') === null) {
        btn = btn.parentNode;
    }
    let id = btn.getAttribute('data-article-id');
    const postDeleteAction = () => {
        id = 100000;
        fetch(`${Main.baseUrl}/artigo/deletar/${id}`, {
            method: 'POST',
            body: `{id: ${id}}`
        })
            .then(res => res.text())
            .then(data => {
                console.log(data);
            }).catch(error => {
            console.log(error);
        });
    }
    btnConfirm.addEventListener('click', postDeleteAction);
    document.getElementById('btn-delete-article-cancel')
        .addEventListener('click', () => {
            btnConfirm.removeEventListener('click', postDeleteAction);
        });
    document.querySelector('#btn-close-modal').onclick = () => {
        btnConfirm.removeEventListener('click', postDeleteAction);
    }
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentDeletePost.classList.remove('d-none');
        modalContentDeletePost.classList.replace('modal-hidden', 'modal-show');
    }
    document.getElementById('btn-delete-article-cancel').onclick = () => {
        modalClose()
    };
};
btnsDelete.forEach(element => {
    element.addEventListener('click', deletePost);
});

// PROFILE PICTURE
const profilePictureForm = document.querySelector('form.profile-picture-update-form');
const inputFileProfilePicture = document.getElementById('image-profile');
const updateProfilePictureAction = (e) => {
    if (inputFileProfilePicture.files !== null && inputFileProfilePicture.files.length > 0) {
        const formData = new FormData(profilePictureForm);
        formData.append('photo', inputFileProfilePicture.files[0]);
        fetch(`${profilePictureForm.getAttribute('action')}`, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.message) {
                    alertPlaceholder.innerHTML = data.message;
                }
                if (data.path) {
                    // document.querySelector('div.profile-picture').style.backgroundImage = `${Main.baseUrl}/${data.path}`;
                    document.querySelector('div.profile-picture')
                        .setAttribute('style', `${Main.baseUrl}/${data.path}; background-size: cover; background-repeat: no-repeat; background-position: center;`)
                    setTimeout(() => { window.location.reload(); }, 2000);
                }
                console.log(data);
            })
            .catch(err => console.error(err));
    }
}
inputFileProfilePicture.addEventListener('change', updateProfilePictureAction);
