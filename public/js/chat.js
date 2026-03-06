
let receiverId = window.receiverId;
let csrfToken = window.csrfToken;
let selectedImage = null;

function scrollBottom(){
    let chatBox = document.getElementById("chatBox");
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
}

// Load messages
function loadMessages(){
    fetch('/chat/fetch/' + receiverId)
    .then(res => res.text())
    .then(data => {
        let chatBox = document.getElementById("chatBox");
        chatBox.innerHTML = data;
        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

// Preview image before send
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    selectedImage = file;

    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('previewImg').src = reader.result;
        document.getElementById('imagePreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function cancelImage() {
    selectedImage = null;
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Image modal
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}

// Send message
document.getElementById('chatForm').addEventListener('submit', function(e){
    e.preventDefault();

    let messageInput = document.getElementById('messageInput');
    let message = messageInput.value;

    if(message.trim() === '' && !selectedImage) return;

    let formData = new FormData();
    formData.append('_token', csrfToken);
    if (message.trim() !== '') {
        formData.append('message', message);
    }
    if (selectedImage) {
        formData.append('image', selectedImage);
    }

    fetch('/chat/' + receiverId, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        messageInput.value = "";
        cancelImage();
        loadMessages();
    })
    .catch(error => console.error(error));
});

// Auto refresh every 3 seconds
setInterval(function(){
    loadMessages();
}, 3000);

// Scroll on first load
scrollBottom();
