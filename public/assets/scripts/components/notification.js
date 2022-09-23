function Notification(data, msg) {
    const container = document.createElement('div');
    container.classList.add('notify');
    container.classList.add('d-flex');
    container.classList.add('gap-2');
    container.classList.add('p-1');
    container.classList.add('border-bottom');
    container.setAttribute('style','cursor: pointer');
    container.onclick = ({ target }) =>  window.location.href = data.url;

    const divPhoto = document.createElement('div');
    divPhoto.setAttribute('style',
        `background-image: url(${data.photo}); width: 45px; height: 45px; background-size: cover; border-radius: 50%;`);

    const divText = document.createElement('div');
    divText.setAttribute('style', 'flex-grow: 1;');
    divText.innerHTML = `${data.username} ${msg}: \"${data.msg}\"`;

    container.append(divPhoto, divText);
    return container;
}

export default Notification;
