<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inbox | I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">

<style>
.inbox-container {
    max-width: 720px;
    margin: 40px auto;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.06);
    border: 1px solid rgba(255,255,255,0.7);
    overflow: hidden;
}

.inbox-header {
    background: linear-gradient(135deg, #2e7d32, #43a047);
    color: white;
    padding: 22px 30px;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.inbox-item {
    display: flex;
    align-items: center;
    padding: 18px 28px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    transition: all 0.25s ease;
    gap: 16px;
}

.inbox-item:hover {
    background: rgba(46,125,50,0.04);
}

.inbox-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: white;
    flex-shrink: 0;
}

.inbox-user-info {
    flex: 1;
}

.inbox-user-info strong {
    display: block;
    font-size: 14px;
    color: #222;
}

.inbox-user-info small {
    font-size: 12px;
    color: #888;
}

.unread-badge {
    background: linear-gradient(135deg, #e53935, #ef5350);
    color: white;
    min-width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(229,57,53,0.3);
}

.empty-state {
    padding: 60px 40px;
    text-align: center;
    color: #999;
    font-size: 15px;
}

.empty-state::before {
    content: "📭";
    display: block;
    font-size: 48px;
    margin-bottom: 16px;
}
</style>
</head>

<body>

@include('components.navbar')

<div class="inbox-container">

    <div id="inboxList">
        @include('partials.inbox-list', ['users' => $users])
    </div>

</div>

<footer>
    <div><h4>Site</h4>Lost<br>Report Lost<br>Found<br>Report Found</div>
    <div><h4>Help</h4>Customer Support<br>Terms & Conditions<br>Privacy Policy</div>
    <div><h4>Links</h4>LinkedIn<br>Facebook<br>YouTube<br>About Us</div>
    <div><h4>Contact</h4>Tel: +94 716520690<br>Email: talkprojects@wenix.com</div>
</footer>

<script src="{{ asset('js/home.js') }}"></script>

<script>
function loadInbox() {
    fetch('/inbox/fetch')
    .then(res => res.text())
    .then(data => {
        document.getElementById("inboxList").innerHTML = data;
    });
}

setInterval(function() {
    loadInbox();
}, 3000);
</script>

</body>
</html>
