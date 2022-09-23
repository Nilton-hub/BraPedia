export const sendNotification = (channel, data) => {
    let msg = JSON.stringify(data);
    conn.publish(channel, msg);
};
