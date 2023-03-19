const alertPlaceholder = document.getElementById('liveAlertPlaceholder'),
    formRegister = document.getElementById('form-register'),
    passwodRegister = document.getElementById('password-register'),
    passwodRegisterRepeat = document.getElementById('password-register_re');

const wrapper = document.createElement('div');
const message = (message, type) => {
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('');
    alertPlaceholder.append(wrapper);
}

const formForget = document.querySelector('form.form-account-forget');
const accountForget = (evt) => {
    evt.preventDefault();
    const formData = new FormData(formForget);
    const btnForget = formForget.btn_forget;
    btnForget.innerText = 'Aguarde...';
    btnForget.setAttribute('disabled', '');
    fetch(`${formForget.getAttribute('action')}`, {
        method: formForget.getAttribute('method'),
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.message) message(data.message, data.type);
            if (!data.success) btnForget.removeAttribute('disabled');
            btnForget.innerText = 'Pronto';
        })
        .catch(error => {
            message('Erro ao enviar, tente novamente mais tarde', 'warning');
        });
}
formForget.addEventListener('submit', accountForget);

// ACTION FORM REGISTER
const emailRegister = document.getElementById('email-register');
const resgisterSubmit = (e) => {
    e.preventDefault();
    const forData = new FormData(formRegister),
        formButton = formRegister.querySelector('[type="submit"]');
    let outpuForm = false,
        buttonContent = formButton.innerHTML;
    if (
        passwodRegister.value.length === 0
        || passwodRegisterRepeat.value.length === 0
        || emailRegister.value.length === 0
    ) {
        message('Preencha todos os campos para cria sua conta!', 'danger');
    } else if (passwodRegister.value !== passwodRegisterRepeat.value)
        message('As senhas informadas não batem!', 'danger');
    else if (grecaptcha.getResponse().length === 0)
        message('Você precisa marcar o campo de sou humano para continuar!', 'danger');
    else outpuForm = true;
    if (outpuForm) {
        const requestUrl = formRegister.getAttribute('action');
        formButton.innerHTML = `<span class="btn-load"></span> Aguarde...`;
        formButton.setAttribute('disabled', '');
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
                formButton.innerHTML = buttonContent;
                formButton.removeAttribute('disabled');
            });
    }
};

formRegister.addEventListener('submit', resgisterSubmit);
