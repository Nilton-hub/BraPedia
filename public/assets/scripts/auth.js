import * as Main from './main.js';

const formLogin = document.getElementById('form-login'),
    alertPlaceholder = document.getElementById('liveAlertPlaceholder'),
    formRegister = document.getElementById('form-register'),
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

divsFormHeader.forEach((value, key,) => {
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
                passwordVerifyText.classList.remove('text-success');
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
    const formData = new FormData(formLogin);
    fetch(formLogin.getAttribute('action'), {
        method: formLogin.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            if (data.message && alertPlaceholder) {
                alertPlaceholder.innerHTML = data.message
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        });
}

formLogin.addEventListener('submit', loginSubmit);
// ACTION FORM REGISTER
const emailRegister = document.getElementById('email-register');
const resgisterSubmit = (e) => {
    e.preventDefault();
    const forData = new FormData(formRegister);

    let outpuForm = false;
    if (passwodRegister.value.length === 0 || passwodRegisterRepeat.value.length === 0 || emailRegister.value.length === 0) {
        message('Preencha todos os campos para cria sua conta!', 'danger');
    } else if (passwodRegister.value !== passwodRegisterRepeat.value) {
        message('As senhas informadas não batem!', 'danger');
    } else {
        outpuForm = true;
    }
    if (outpuForm) {
        const requestUrl = formRegister.getAttribute('action');
        fetch(requestUrl, {
            method: 'POST',
            body: forData
        })
            .then(data => data.json())
            .then(res => {
                if (res.message && alertPlaceholder) {
                    alertPlaceholder.innerHTML = res.message;
                }
                if (res.redirect) {
                    window.location.href = res.redirect;
                }
                return res;
            });
    }
};

formRegister.addEventListener('submit', resgisterSubmit);
// ACTION FORM FORGET

if (self.fetch) {
    console.log(true);
} else {
    console.log(false);
}
