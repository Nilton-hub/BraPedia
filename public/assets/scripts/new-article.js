const formArticle = document.querySelector('form.form-article');

const tinymceConfig = {
    selector: 'textarea#article-text',
    autosave_ask_before_unload: false,
    powerpaste_allow_local_images: true,
    menubar: false,
    plugins: [
        'a11ychecker', 'advcode', 'advlist', 'anchor', 'autolink', 'codesample', 'fullscreen', 'help',
        'image', 'editimage', 'tinydrive', 'lists', 'link', 'media', 'powerpaste', 'preview',
        'searchreplace', 'table', 'template', 'tinymcespellchecker', 'visualblocks', 'wordcount'
    ],
    toolbar: 'insertfile a11ycheck undo redo | bold italic underline | forecolor backcolor |  codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image',
    spellchecker_dialog: true,
    spellchecker_ignore_list: ['Ephox', 'Moxiecode'],
    tinydrive_demo_files_url: '../_images/tiny-drive-demo/demo_files.json',
    tinydrive_token_provider: (success, failure) => {
        success({token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJqb2huZG9lIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.Ks_BdfH4CWilyzLNk8S2gDARFhuxIauLa8PwhdEQhEo'});
    },
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
};
tinymce.init(tinymceConfig);

const articleSubmit = (e) => {
    e.preventDefault();
    const formData = new FormData(formArticle);
    document.querySelector('form.form-article input[type="file"]')
        .addEventListener('change', (e) => {
            if (e.target.files !== null && e.target.files.length > 0) {
                formData.append('cover', e.target.files[0]);
            }
    });

    const xhr = new XMLHttpRequest();
    let percent = 0;
    const progressBar = document.querySelector('#progress-bar-article');
    const progressText = document.querySelector('.progress-test');
    const progress = (e) => {
        percent = Math.floor((e.loaded * 100) / e.total);
        progressText.innerText = 'Aguade, carregando...';
        progressBar.style.width = `${percent}%`;
        progressBar.innerText = `${percent}%`;
        if (percent >= 100) {
            progressText.innerText = '';
            progressBar.style.width = 0;
            progressBar.innerText = '';
        }
    }
    xhr.upload.addEventListener('progress', progress);
    xhr.open('POST', `${formArticle.getAttribute('action')}`);
    xhr.send(formData);
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 300) {
            const res = JSON.parse(xhr.responseText);
            if (res.message) {
                alertPlaceholder.innerHTML = res.message;
            }
            if (percent >= 100) {
                progressText.innerText = 'Concluído';
                progressBar.style.width = 0;
                progressBar.innerText = '';
            }
            console.log(xhr.responseText);
        }
    }
}
formArticle.addEventListener('submit', articleSubmit);
