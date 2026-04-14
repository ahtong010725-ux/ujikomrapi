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
    margin: 30px auto;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.06);
    border: 1px solid rgba(255,255,255,0.6);
    overflow: hidden;
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.inbox-header {
    background: linear-gradient(135deg, #1a1a2e, #2d2d4e);
    color: white;
    padding: 18px 28px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.inbox-header svg { margin-right: 10px; }

.inbox-section-label {
    padding: 10px 28px;
    background: rgba(0,0,0,0.025);
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    color: #777;
    display: flex;
    align-items: center;
    gap: 8px;
}

.inbox-section-label svg { opacity: 0.5; }

.inbox-item {
    display: flex;
    align-items: center;
    padding: 16px 24px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    transition: all 0.25s ease;
    gap: 14px;
}

.inbox-item:hover {
    background: rgba(102,126,234,0.04);
}

.inbox-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: white;
    flex-shrink: 0;
    overflow: hidden;
}

.inbox-avatar.admin-avatar {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.inbox-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.inbox-user-info {
    flex: 1;
    min-width: 0;
}

.inbox-user-info strong {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #222;
}

.inbox-preview {
    font-size: 12px;
    color: #888;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}

.inbox-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
}

.inbox-time {
    font-size: 11px;
    color: #aaa;
}

.badge-admin {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 1px 7px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 600;
}

.unread-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    min-width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(102,126,234,0.3);
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

/* Dark Mode */
body.dark-theme .inbox-container {
    background: rgba(18,18,28,0.6);
    border-color: rgba(255,255,255,0.06);
}

body.dark-theme .inbox-header {
    background: linear-gradient(135deg, #12121a, #1e1e30);
}

body.dark-theme .inbox-item { color: #ddd; border-bottom-color: rgba(255,255,255,0.05); }
body.dark-theme .inbox-item:hover { background: rgba(255,255,255,0.04); }
body.dark-theme .inbox-user-info strong { color: #eee; }
body.dark-theme .inbox-section-label { background: rgba(255,255,255,0.03); color: #888; border-bottom-color: rgba(255,255,255,0.05); }
</style>
</head>

<body>

@include('components.navbar')

<div class="inbox-container">

    <div class="inbox-header">
        <div style="display:flex;align-items:center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
            Inbox
        </div>
    </div>

    <div id="inboxList">
        @include('partials.inbox-list', ['users' => $users])
    </div>

</div>

<footer>
    <div><h4>Site</h4>Lost<br>Report Lost<br>Found<br>Report Found</div>
    <div><h4>Help</h4>Customer Support<br>Terms & Conditions<br>Privacy Policy</div>
    <div><h4>Links</h4>LinkedIn<br>Facebook<br>YouTube<br>About Us</div>
    <div><h4>Contact</h4>Tel: +62 895 3440 39020<br>Email: rmukhrij@gmail.com</div>
</footer>

<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>

<script>
// Auto-refresh inbox every 5 seconds
function loadInbox() {
    fetch('/inbox/fetch')
    .then(res => res.text())
    .then(data => {
        document.getElementById("inboxList").innerHTML = data;
    });
}

setInterval(function() {
    loadInbox();
}, 5000);
</script>

</body>
</html>
