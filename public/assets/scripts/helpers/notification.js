import Notification from "../components/notification.js";
import { login } from "./functions.js";
import '../autobahn.js';

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
            const notifications = getChannels();
            if (!await notifications) {
                return;
            }
            notifications.then(response => {
                response.map(channel => {
                    conn.subscribe(channel, (topic, data) => {
                        data = JSON.parse(data);
                        let notifyTpl;
                        let element_id;
                        const notificationData = {};
                        switch (topic.split('_')[0]) {
                            case 'article': // ARTIGO
                                element_id = (data.comment_id ? `container-of-comment-${data.comment_id}` : null);
                                element_id = (element_id ?? `container-of-comment-${data.id}`);
                                console.log(data, element_id);
                                notificationData.username = data.username;
                                notificationData.msg = data.msg;
                                notificationData.photo = `${baseUrl}/${data.photo}`;
                                notificationData.url = `${baseUrl}/artigo/${data.element_id}#${element_id}`;
                                notificationData.comment_id = data.comment_id ?? null;
                                notificationData.id = data.id ?? null;
                                notifyTpl = Notification(notificationData, 'Comentou no seu artigo');
                                break;
                            case 'comment': // COMENTÁTRIO
                                notificationData.username = data.username;
                                notificationData.msg = data.msg;
                                notificationData.photo = `${baseUrl}/${data.photo}`;
                                notificationData.url = `${baseUrl}/artigo/${element_id}#${data.comment_id}`;
                                notificationData.comment_id = data.comment_id ?? null;
                                notificationData.id = data.id ?? null;
                                notifyTpl = Notification(notificationData, `respondeu seu comentário`);
                                break;
                        }
                        document.querySelector("div.notification-container").after(notifyTpl);
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
