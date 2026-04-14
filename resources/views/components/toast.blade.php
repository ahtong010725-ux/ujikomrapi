<link rel="stylesheet" href="{{ asset('css/toast.css') }}">

{{-- Global JS toast function — available on all pages --}}
<script>
function showToast(message, type) {
    type = type || 'success';
    var icons = {
        success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };
    var container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    var toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.innerHTML =
        '<div class="toast-icon-wrap">' + (icons[type] || icons.success) + '</div>' +
        '<div class="toast-body">' +
            '<div class="toast-title">' + (type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Peringatan!' : 'Info') + '</div>' +
            '<div class="toast-message">' + message + '</div>' +
        '</div>' +
        '<button class="toast-close" onclick="dismissToast(this.parentElement)">&times;</button>' +
        '<div class="toast-progress"><div class="toast-progress-bar toast-progress-' + type + '"></div></div>';
    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(function() {
        toast.classList.add('toast-show');
    });

    // Auto-dismiss after 5s
    var timer = setTimeout(function() { dismissToast(toast); }, 5000);
    toast.addEventListener('mouseenter', function() { clearTimeout(timer); });
    toast.addEventListener('mouseleave', function() {
        timer = setTimeout(function() { dismissToast(toast); }, 2000);
    });
}

function dismissToast(toast) {
    if (!toast || toast.classList.contains('toast-hiding')) return;
    toast.classList.add('toast-hiding');
    toast.classList.remove('toast-show');
    setTimeout(function() {
        toast.remove();
        var container = document.getElementById('toastContainer');
        if (container && container.children.length === 0) container.remove();
    }, 400);
}
</script>

{{-- Server-side flash messages rendered as toasts --}}
@if(session('success') || session('error') || session('warning') || session('info'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
    showToast(@json(session('error')), 'error');
    @endif
    @if(session('warning'))
    showToast(@json(session('warning')), 'warning');
    @endif
    @if(session('info'))
    showToast(@json(session('info')), 'info');
    @endif
});
</script>
@endif
