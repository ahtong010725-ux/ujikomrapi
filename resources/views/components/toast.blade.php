<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
{{-- Toast Notification Component --}}
{{-- Include this in any page to show flash messages as toasts --}}

@if(session('success') || session('error') || session('warning') || session('info'))
<div id="toastContainer" style="position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;">

    @if(session('success'))
    <div class="toast-notification toast-success" style="pointer-events: auto;">
        <div class="toast-icon">✅</div>
        <div class="toast-content">{{ session('success') }}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="toast-notification toast-error" style="pointer-events: auto;">
        <div class="toast-icon">❌</div>
        <div class="toast-content">{{ session('error') }}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if(session('warning'))
    <div class="toast-notification toast-warning" style="pointer-events: auto;">
        <div class="toast-icon">⚠️</div>
        <div class="toast-content">{{ session('warning') }}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

    @if(session('info'))
    <div class="toast-notification toast-info" style="pointer-events: auto;">
        <div class="toast-icon">ℹ️</div>
        <div class="toast-content">{{ session('info') }}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
    @endif

</div>

<script>
    // Auto-dismiss toasts after 5 seconds
    document.querySelectorAll('.toast-notification').forEach(function(toast) {
        toast.style.animation = 'toastSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        setTimeout(function() {
            toast.style.animation = 'toastSlideOut 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(function() {
                toast.remove();
                // Remove container if empty
                var container = document.getElementById('toastContainer');
                if (container && container.children.length === 0) {
                    container.remove();
                }
            }, 350);
        }, 5000);
    });
</script>
@endif
