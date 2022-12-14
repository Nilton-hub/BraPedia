const formLogin = document.getElementById('form-login'),
      formRegister = document.getElementById('form-register'),
      divsFormHeader = document.querySelectorAll('.header-form div'),
      iconTogglePassword = document.getElementById('icon-toggle-password'),
      btnToggleModal = document.querySelector('#btn-toggle-modal'),
      /* SHARED */
      modal = document.querySelector('#modals'),
      modalClose = () => {
        if (!modal.classList.contains('d-none')) {
            modal.classList.add('d-none')
        }
      };

// SHOW OR HIDDEN PASSWORD
iconTogglePassword.addEventListener('click', () => {
    const inputPassword = document.querySelector('#password-login');

    iconTogglePassword.classList.toggle('icon-eye');
    iconTogglePassword.classList.toggle('icon-eye-slash');
    if (inputPassword.getAttribute('type') === 'password') {
        inputPassword.setAttribute('type', 'text');
    } else {
        inputPassword.setAttribute('type', 'password');
    }
});

divsFormHeader.forEach((value, key,) => {
    value.style.cursor = 'pointer';
});

// AUTH AUTERNATE
const authFocusLogin = (e) => {
    const element = e.target;
    if (!element.classList.contains('bg-warning')) {
        element.classList.add('bg-warning');
        element.classList.remove('bg-light');
        formLogin.classList.toggle('d-none');
        formRegister.classList.toggle('d-none');
    }
    if (!element.classList.contains('text-white')) {
        element.classList.add('text-white');
    }
    if (divsFormHeader[1].classList.contains('bg-warning')) {
        divsFormHeader[1].classList.remove('bg-warning');
    }
    if (divsFormHeader[1].classList.contains('text-white')) {
        divsFormHeader[1].classList.remove('text-white');
    }
}

const authFocusRegister = (e) => {
    const element = e.target;
    if (!element.classList.contains('bg-warning')) {
        element.classList.add('bg-warning');
        element.classList.remove('bg-light');
        formLogin.classList.toggle('d-none');
        formRegister.classList.toggle('d-none');
    }
    if (!element.classList.contains('text-white')) {
        element.classList.add('text-white');
    }
    if (divsFormHeader[0].classList.contains('bg-warning')) {
        divsFormHeader[0].classList.remove('bg-warning');
    }
    if (divsFormHeader[0].classList.contains('text-white')) {
        divsFormHeader[0].classList.remove('text-white');
    }
}

divsFormHeader[0].addEventListener('click', authFocusLogin);
divsFormHeader[1].addEventListener('click', authFocusRegister);

// MODAL
document.querySelector('#btn-close-modal').addEventListener('click', modalClose);
const toggleModal = () => {
    modal.classList.remove('d-none');
    
    document.querySelector('.modal-content').classList.remove('modal-hidden');
    document.querySelector('.modal-content').classList.add('modal-show');
}
btnToggleModal.addEventListener('click', toggleModal);
