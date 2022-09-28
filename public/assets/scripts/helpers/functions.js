export const sendNotification = (channel, data) => {
    let msg = JSON.stringify(data);
    conn.publish(channel, msg);
};

export const date = (date = null) => {
    if (date) {
        date = date.substring(0, 10).split('-');
        return `${date[2]}/${date[1]}/${date[0]}`;
    }
    let objectDate = new Date();
    let month = objectDate.getMonth();
    month = month.toString().length === 1 ? `0${month}` : month;
    return `${objectDate.getDate()}/${month}/${objectDate.getFullYear()}`;
};

export const strRemume = (text, length = 100) => {
    let arrtext = text.split('');
    let textLength = arrtext.length;
    if (textLength > length) {
        let newText = [];
        for (const newTextKey in arrtext) {
            if (newTextKey <= length + 1){
                newText.push(arrtext[newTextKey]);
            }
        }
        return newText.join('') + '...';
    }
    return text;
}
