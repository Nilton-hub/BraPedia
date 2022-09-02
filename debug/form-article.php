<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1 style="font-family: sans-serif; text-align: center;">Adicionar Artigo</h1>
<form method="post" action=""
      style="display: flex; flex-direction: column; width: 30rem; font-family: sans-serif; margin: 50px auto;">
    <div style="display: flex; gap: 1rem; flex; margin-bottom: 0.5rem;">
        <label for="title" style="width: 30%;">Titulo: </label>
        <input type="text" name="title" id="title" style="width: 68%;">
    </div>
    <div style="display: flex; gap: 1rem; flex; margin-bottom: 0.5rem;">
        <label for="subtitle" style="width: 30%;">Subtitulo: </label>
        <input type="text" name="subtitle" id="subtitle" style="width: 68%;">
    </div>
    <div style="display: flex; gap: 1rem; flex; margin-bottom: 0.5rem;">
        <label for="cover" style="width: 30%;">Capa: </label>
        <input type="file" name="cover" id="cover" style="width: 68%;">
    </div>
    <div style="display: flex; gap: 1rem; flex; margin-bottom: 0.5rem;">
        <label for="article-text" style="width: 30%;">Conteúdo: </label>
        <textarea name="content" id="article-text" cols="30" rows="10" style="width: 68%;"></textarea>
    </div>
    <button type="submit" style="padding: 5px; border-radius: 5px; cursor: pointer;">Salvar</button>
</form>

<script src="http://localhost:8080/assets/scripts/tinymce.min.js"></script>
<script>
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

    const form = document.querySelector('form');
    console.log(form);

    const articleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('title', form.title.value);
        formData.append('subtitle', form.subtitle.value);
        formData.append('content', form.content.value);
        document.querySelector('form input[type="file"]')
            .addEventListener('change', (e) => {
                if (e.target.files !== null && e.target.files.length > 0) {
                    formData.append('cover', e.target.files[0]);
                }
            });

        const xhr = new XMLHttpRequest();
        const progress = (e) => {
            let percent = Math.floor((e.loaded * 100) / e.total);
            const progressBar = document.querySelector('#progress-bar-article');
            const progressText = document.querySelector('.progress-test');
            progressText.innerText = 'Aguade, carregando...';
            progressBar.style.width = `${percent}%`;
            progressBar.innerText = `${percent}%`;
            if (percent >= 100) {
                progressText.innerText = 'Concluído';
                progressBar.style.width = 0;
                progressBar.innerText = '';
            }
        }
        xhr.upload.addEventListener('progress', progress);
        xhr.open('POST', `${form.getAttribute('action')}`);
        xhr.send(formData);
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 300) {
                // Obter e tratar os dados de resposta
                // 'const res = JSON.parse(xhr.responseText);
                // if (res.message) {
                //     alertPlaceholder.innerHTML = res.message;
                // }'
                console.log(xhr.responseText);
            }
        }
    }
    form.addEventListener('submit', articleSubmit);
</script>
</body>
</html>