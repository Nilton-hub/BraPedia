import * as Main from './main.js';

const formLogin = document.getElementById('form-login'),
    alertPlaceholder = document.getElementById('liveAlertPlaceholder'),
    divsFormHeader = document.querySelectorAll('.header-form div'),
    iconTogglePassword = document.getElementById('icon-toggle-password'),
    btnToggleModal = document.querySelector('#btn-toggle-modal'),
    /* SHARED */
    modal = document.querySelector('#modals'),
    modalClose = () => {
        if (!modal.classList.contains('d-none')) {
            modal.classList.add('d-none');
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

divsFormHeader.forEach((value) => {
    value.style.cursor = 'pointer';
});

// AUTH ALTERNATE
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

// PASSWORD VERIFY
const passwodRegister = document.getElementById('password-register'),
    passwodRegisterRepeat = document.getElementById('password-register_re');
const passwordVerify = () => {
    const passwordVerifyText = document.getElementById('passwordVerifyText');
    const password = passwodRegister.value;
    const passwordRepeat = passwodRegisterRepeat.value;

    if (password.length === 0 || passwordRepeat.length === 0) {
        passwordVerifyText.innerHTML = '';
        passwordVerifyText.className = '';
    }
    if (password.length > 0 && passwordRepeat.length > 0) {
        if (passwodRegister.value !== passwodRegisterRepeat.value) {
            if (passwordVerifyText.classList.contains('text-success')) {
                passwordVerifyText.classList.remove('text-succ  ess');
            }
            passwordVerifyText.classList.add('text-danger');
            passwordVerifyText.innerHTML = '<strong>Senhas não conferem</strong>';
        } else {
            if (passwordVerifyText.classList.contains('text-danger')) {
                passwordVerifyText.classList.remove('text-danger');
            }
            passwordVerifyText.classList.add('text-success');
            passwordVerifyText.innerHTML = '<strong>Senhas conferem</strong>';
        }
    }
};

passwodRegister.addEventListener('keyup', passwordVerify);
passwodRegisterRepeat.addEventListener('keyup', passwordVerify);

// MODAL
document.querySelector('#btn-close-modal').addEventListener('click', modalClose);
const toggleModal = () => {
    modal.classList.remove('d-none');

    document.querySelector('.modal-content').classList.remove('modal-hidden');
    document.querySelector('.modal-content').classList.add('modal-show');
}
btnToggleModal.addEventListener('click', toggleModal);

// ACTION FORM LOGIN
const loginSubmit = (e) => {
    e.preventDefault();
    const formData = new FormData(formLogin),
        formButton = formLogin.querySelector('[type="submit"]');
    let buttonContent = formButton.innerHTML;
    formButton.innerHTML = `<span class="btn-load"></span> Aguarde...`;
    formButton.setAttribute('disabled', '');
    fetch(formLogin.getAttribute('action'), {
        method: formLogin.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.message && alertPlaceholder) {
                alertPlaceholder.innerHTML = data.message
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
            formButton.innerHTML = buttonContent;
            formButton.removeAttribute('disabled');
        });
}
formLogin.addEventListener('submit', loginSubmit);

// ACTION FORM FORGET
const formForget = document.querySelector('form.form-account-forget');

if (!self.fetch) {
    console.log('No javascript fetch api');
}
