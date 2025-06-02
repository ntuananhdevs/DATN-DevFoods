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

// Define sendMessage in the global scope
window.sendMessage = function (
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
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Failed to send message");
            }
        })
        .catch((error) => {
            console.error(error);
            alert("Failed to send message. Please try again.");
        });
};
