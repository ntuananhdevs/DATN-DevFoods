import Pusher from "pusher-js";

const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
});

const channel = pusher.subscribe("chat-channel");
channel.bind("new-message", function (data) {
    const chatBox = document.getElementById("chat-box");
    chatBox.innerHTML += `<p><strong>${data.sender_id}:</strong> ${data.message}</p>`;
});

export function sendMessage(
    route,
    message,
    receiverId,
    senderType,
    receiverType
) {
    fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            message: message,
            receiver_id: receiverId,
            sender_type: senderType,
            receiver_type: receiverType,
        }),
    });
}
