const articleOptions = document.getElementById('article-options'),
    modalContentHidePost = document.querySelector('.modal-content-hide-post'),
    modalContentDeletePost = document.querySelector('.modal-content-delete-post'),
    btnArticleDelete = document.getElementById('btn-article-delete'),
    btnArticleHide = document.getElementById('btn-article-hide'),
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
    if (articleOptions.classList.contains('toggle-opt-hidden') && !showArticleOptions) {
        articleOptions.classList.add('toggle-opt-show');
        articleOptions.classList.remove('toggle-opt-hidden');
        showArticleOptions = true;
    }

    setTimeout(() => {
        showArticleOptions = false;
        if (articleOptions.classList.contains('toggle-opt-show')) {
            articleOptions.classList.remove('toggle-opt-show');
            articleOptions.classList.add('toggle-opt-hidden');
        }
    }, 3000);
}
document.addEventListener('scroll', toggleOptionsArticle);

const deletePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentDeletePost.classList.remove('d-none');
        modalContentDeletePost.classList.replace('modal-hidden', 'modal-show');
    }
};
btnArticleDelete.addEventListener('click', deletePost);

const hidePost = () => {
    if (modal.classList.contains('d-none')) {
        modal.classList.remove('d-none');
        modalContentHidePost.classList.replace('modal-hidden', 'modal-show');
        modalContentHidePost.classList.remove('d-none');
    }
};
btnArticleHide.addEventListener('click', hidePost);
