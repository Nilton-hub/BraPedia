import notificationTpl from "../components/notification-tpl.js";
import '../autobahn.js';

export function notify() {
    const baseUrl = 'http://localhost';
    async function getChannels() {
        const request = await fetch(`${baseUrl}/notify`);
        return await request.json();
    }

    var conn = new ab.Session('ws://localhost:8080',
        function() {
            //article_Assincronismo_com_9_3
            getChannels().then(response => {
                response.map(channel => {
                    conn.subscribe(channel, (topic, data) => {
                        data = JSON.parse(data);
                        let notifyTpl;
                        switch (topic.split('_')[0]) {
                            case 'article':
                                notifyTpl = notificationTpl({
                                    username: data.username,
                                    msg: data.msg,
                                    photo: `${baseUrl}/uploads/profile/${data.photo}`, // 1662282486-eu-pb.jpg
                                    url: `${baseUrl}/artigo/9#${data.comment_id}` // comment
                                }, 'Comentou no seu artigo');
                                break;
                            case 'comment':
                                console.log(`${data.username} respondeu seu comentário: \"${data.msg}\"`);
                                // ${data.username} respondeu seu comentário: "Bla blá blá"
                                break;
                            case 'commentRepply':
                                console.log(`${data.username} respondeu seu comentário: \"${data.msg}\"`);
                                // ${data.username} respondeu seu comentário: "Bla blá blá"
                                break;
                        }
                        document.querySelector("#main-sidebar-menu").append(notifyTpl);
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
