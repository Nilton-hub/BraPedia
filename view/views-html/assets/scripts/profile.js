const btnProfileEdit = document.querySelector('#btn-profile-edit'),
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
});

// CHANGE PASSWORD
bntChangePassword.addEventListener('click', () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentChangePassword.classList.replace('modal-hidden', 'modal-show');
        modalContentChangePassword.classList.remove('d-none');
    }
});

const hidePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentHidePost.classList.replace('modal-hidden', 'modal-show');
        modalContentHidePost.classList.remove('d-none');
    }
};

btnsHide.forEach(element => {
    element.addEventListener('click', hidePost);
});

const deletePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentDeletePost.classList.remove('d-none');
        modalContentDeletePost.classList.replace('modal-hidden', 'modal-show');
    }
};
btnsDelete.forEach(element => {
    element.addEventListener('click', deletePost);
});
