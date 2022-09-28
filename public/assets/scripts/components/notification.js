import { element } from '../helpers/element.js';
import { date, strRemume } from "../helpers/functions.js";
import { baseUrl } from "../main.js";

// data = {url: "", photo: "", username: "", msg: "", comment_id: "", id: ""}
function Notification(data, msg, id) {
    console.log(data.photo);
    const divPhoto = element({
        name: 'div',
        attrs: [{
            name: 'style',
            value: `background-image: url(${data.photo}); width: 45px; height: 45px; background-size: cover; border-radius: 50%;`
        }]
    });

    let dateStr = data.created_at ? date(data.created_at) : date();
    const divTime = element({
        name: 'div',
        class: 'notify-time',
        text: dateStr
    });

    const divOptions = element({
        name: 'i',
        classes: ['notify-options', 'icon-trash'],
        attrs: [{name: 'title', value: 'Remover'}]
    });
    divOptions.addEventListener('click', (e) => {
        e.stopPropagation();
        container.removeEventListener('click', handleRedirectNofication);
        if (data.id) {
            const post = new FormData();
            post.append("id", data.id);
            fetch(`${baseUrl}/remove-notifications`, {
                method: 'POST',
                body: post
            });
        }
        container.remove();
    });
    let message = data.msg.length > 50 ? strRemume(data.msg, 50) : data.msg;
    const divText = element({
        name: 'p',
        text: `${data.username} ${msg}: \"${message}\"`
    });

    const divContent = element({
        name: 'div',
        attrs: [{name: 'style', value: 'flex-grow: 1'}],
        childs: [divText, divTime],
        class: 'notify'
    });

    const container = element({
        name: 'div',
        classes: ['notify-content', 'd-flex', 'gap-2', 'p-1', 'border-bottom'],
        attrs: [{name: 'style', value: 'cursor: pointer'}],
        childs: [divPhoto, divContent, divOptions],
        class: 'notify-item'
    });
    const handleRedirectNofication = ({ target }) =>  window.location.href = data.url;
    container.addEventListener('click', handleRedirectNofication);
    return container;
}

export default Notification;
