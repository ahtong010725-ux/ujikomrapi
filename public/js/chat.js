
let receiverId = window.receiverId;
let csrfToken = window.csrfToken;

function scrollBottom(){
    let chatBox = document.getElementById("chatBox");
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Load pesan
function loadMessages(){
    fetch('/chat/fetch/' + receiverId)
    .then(res => res.text())
    .then(data => {
        let chatBox = document.getElementById("chatBox");
        chatBox.innerHTML = data;
        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

// Kirim pesan
document.getElementById('chatForm').addEventListener('submit', function(e){
    e.preventDefault();

    let messageInput = document.getElementById('messageInput');
    let message = messageInput.value;

    if(message.trim() === '') return;

    fetch('/chat/' + receiverId, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(res => res.json())
    .then(data => {
        messageInput.value = "";
        loadMessages();
    })
    .catch(error => console.error(error));
});

// Auto refresh tiap 2 detik
setInterval(function(){
    loadMessages();
}, 2000);

// Scroll pertama kali
scrollBottom();
