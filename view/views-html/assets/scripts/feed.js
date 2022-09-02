const topLink = document.querySelector('[href="#main-header"]'),
    bottomReference = document.querySelector('#bottom-reference'),
    btnsToggleComments = document.querySelectorAll('p.btn-toggle-comments');

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

// BTN TOGGLE COMENTS
btnsToggleComments.forEach(value => {
    const pText = ['Exibir todos os comentários...', 'Ocultar todos os comentários'];
    value.innerHTML = pText[0];
    const id = value.getAttribute('id');
    const toggleComments = () => {
        let currentCommentsContainer = document.querySelector(`#${id} ~ div[id^="comments-container-"]`);
        currentCommentsContainer.classList.toggle('comments-show');
        if (value.innerHTML === pText[0]) {
            value.innerHTML = pText[1];
        } else {
            value.innerHTML = pText[0];
        }
    }

    value.addEventListener('click', toggleComments);
})













