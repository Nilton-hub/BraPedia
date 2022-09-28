import Notification from "../components/notification.js";
import '../autobahn.js';

// VERIFICA SE O USUÁRIO ESTÁ AUTENTICADO
const login = () => {
    const cookies = document.cookie.split(';')
        .map(e => e.split('=')[0].replaceAll(' ', ''));
    return cookies.indexOf('userToken') !== -1;
};

export function notify() {
    if (!login()) {
        return null;
    }
    const baseUrl = 'http://localhost';
    async function getChannels() {
        const request = await fetch(`${baseUrl}/notify`);
        return await request.json();
    }

    var conn = new ab.Session('ws://localhost:8080',
        async function () {
            //article_Assincronismo_com_9_3
            const notifications = getChannels();
            if (!await notifications) {
                return;
            }
            notifications.then(response => {
                response.map(channel => {
                    conn.subscribe(channel, (topic, data) => {
                        data = JSON.parse(data);
                        let notifyTpl;
                        let element_id = (data.element_id ?? '');
                        const notificationData = {};
                        switch (topic.split('_')[0]) {
                            case 'article': // ARTIGO
                                notificationData.username = data.username;
                                notificationData.msg = data.msg;
                                notificationData.photo = `${baseUrl}/${data.photo}`;
                                notificationData.url = `${baseUrl}/artigo/${data.comment_id}#${element_id}`;
                                notifyTpl = Notification(notificationData, 'Comentou no seu artigo');
                                break;
                            case 'comment': // COMENTÁTRIO
                                notificationData.username = data.username;
                                notificationData.msg = data.msg;
                                notificationData.photo = `${baseUrl}/${data.photo}`;
                                notificationData.url = `${baseUrl}/artigo/${element_id}#${data.comment_id}`;
                                notifyTpl = Notification(notificationData, `respondeu seu comentário`);
                                break;
                        }
                        document.querySelector(".notification-sidebar").append(notifyTpl);
                    });
                });
            });
            /*conn.subscribe('kittensCategory', function(topic, data) {
                console.log('New article published to category "' + topic + '" : ' + data.title);
            });*/
        },
        function() {
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );
}
