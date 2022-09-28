export function element(config) {
    // {name: "", class: "", classes: [], id: "", attrs: [{name: '', value: ''}], text: "", child: "", childs: []}
    const e = document.createElement(config.name);
    if (config.class) {
        e.classList.add(config.class);
    }
    if (config.classes) {
        config.classes.forEach(c => {
            e.classList.add(c);
        });
    }
    if (config.id) {
        e.setAttribute('id', config.id);
    }
    if (config.attrs) {
        config.attrs.forEach(attr => {
            e.setAttribute(attr.name, attr.value);
        });
    }
    if (config.text) {
        e.textContent = config.text;
    }
    if (config.child) {
        e.append(config.child);
    }
    if (config.childs) {
        config.childs.forEach(child => {
            e.append(child);
        });
    }
    if (config.html) {
        e.innerHTML += config.html;
    }
    return e;
}
