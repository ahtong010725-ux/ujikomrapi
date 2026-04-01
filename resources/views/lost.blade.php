    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta charset="UTF-8">
        <title>Lost Items | I FOUND</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/lost.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    </head>
    <body>

    <!-- NAVBAR -->
 @include('components.navbar')


    <!-- MAIN -->
    <section class="lost-hero">
        <h1>Lost Items</h1>

        <div class="lost-action">
            <form action="/lost" method="GET" class="search-form">
                <input type="text" name="search" id="searchInput" placeholder="Cari barang..." value="{{ request('search') }}" autocomplete="off">
                <button type="submit" class="report-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Search
                </button>
            </form>
            @if(auth()->check())
            <button type="button" class="report-btn" onclick="openReportModal()" style="white-space: nowrap;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Report
            </button>
            @endif
        </div>
    </section>

    <section class="lost-list" id="itemList">

@foreach($items as $item)
<div class="item-card">
    <div class="card-head">
        <div class="avatar">
            @if($item->user && $item->user->photo)
                <img src="{{ asset('storage/' . $item->user->photo) }}" alt="{{ $item->user->name ?? 'User' }}">
            @else
                <img src="/images/anonymous.jpg" alt="user">
            @endif
        </div>
        <div>
            <strong>{{ $item->user->name ?? $item->name }}</strong>
            <small>{{ $item->created_at->format('d-m-Y') }}</small>
        </div>
    </div>

    @if($item->photo)
    <div class="card-img">
        <img src="{{ asset('storage/'.$item->photo) }}" alt="">
    </div>
    @endif

    <h4>{{ $item->item_name }} @if($item->status == 'resolved') <span style="color: green; font-size: 0.8em; border: 1px solid green; padding: 2px 6px; border-radius: 12px; margin-left: 5px;">Terverifikasi ✅</span> @endif</h4>

    @if($item->brand_name)
    <p style="font-size: 13px; color: #888; margin: 2px 0;">🏷️ {{ $item->brand_name }}</p>
    @endif
    @if($item->item_type)
    <p style="font-size: 13px; color: #888; margin: 2px 0;">📦 Jenis: {{ $item->item_type }}</p>
    @endif

    <p class="location">{{ $item->location }}</p>
    <p class="desc">{{ $item->description }}</p>

    @if($item->reward_offered)
    <p style="font-size: 13px; color: #e67e22; font-weight: 600; margin-top: 6px;">🎁 Imbalan: Rp. {{ number_format((int) preg_replace('/[^0-9]/', '', $item->reward_offered), 0, ',', '.') }}</p>
    @endif

   <div class="action-buttons" style="display: flex; gap: 5px; flex-wrap: wrap; margin-top: 10px;">

@if(auth()->check())

    @if(auth()->id() == $item->user_id)

        <!-- Owner buttons -->
        <button type="button" class="contact-btn" onclick="openEditModal({{ $item->id }})">
            Edit
        </button>

        <form action="/lost/{{ $item->id }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="contact-btn delete-btn">
                Delete
            </button>
        </form>

        <a href="/inbox" class="contact-btn">
            Inbox
        </a>

    @else

        <!-- Other users -->
        <a href="/chat/{{ $item->user_id }}" class="contact-btn">
            Chat
        </a>

    @endif

@endif

</div>
</div>
@endforeach
</section>

    <!-- Report Modal -->
    <div id="reportModal" class="report-modal-overlay" style="display:none;">
        <div class="report-modal-box">
            <div class="report-modal-header">
                <h3>📋 Report Lost Item</h3>
                <button type="button" class="report-modal-close" onclick="closeReportModal()">&times;</button>
            </div>
            <form id="reportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-form-row">
                    <label>Nama / Barang</label>
                    <input type="text" name="item_name" placeholder="Apa yang hilang?" required>
                </div>
                <div class="modal-form-row">
                    <label>Jenis Barang</label>
                    <input type="text" name="item_type" placeholder="Contoh: Elektronik, Pakaian, Dokumen...">
                </div>
                <div class="modal-form-row">
                    <label>Lokasi</label>
                    <input type="text" name="location" placeholder="Dimana kamu kehilangan?" required>
                </div>
                <div class="modal-form-row">
                    <label>Tanggal</label>
                    <input type="date" name="date" required>
                </div>
                <div class="modal-form-row">
                    <label>Deskripsi</label>
                    <textarea name="description" placeholder="Deskripsikan barang secara detail..." required></textarea>
                </div>
                <div class="modal-form-row">
                    <label>Imbalan (opsional)</label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">Rp.</span>
                        <input type="text" name="reward_offered" placeholder="Contoh: 50.000 atau Traktir makan">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label>Upload Foto</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div id="reportError" style="display:none; color:#e53935; font-size:13px; margin-bottom:10px; padding:8px 12px; background:rgba(229,57,53,0.08); border-radius:8px;"></div>
                <div class="modal-actions">
                    <button type="submit" class="modal-submit-btn" id="reportSubmitBtn">Submit Report</button>
                    <button type="button" class="modal-cancel-btn" onclick="closeReportModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="report-modal-overlay" style="display:none;">
        <div class="report-modal-box">
            <div class="report-modal-header">
                <h3>✏️ Edit Lost Item</h3>
                <button type="button" class="report-modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="_editId" id="editItemId">
                <div class="modal-form-row">
                    <label>Nama / Barang</label>
                    <input type="text" name="item_name" id="edit_item_name" required>
                </div>
                <div class="modal-form-row">
                    <label>Jenis Barang</label>
                    <input type="text" name="item_type" id="edit_item_type">
                </div>
                <div class="modal-form-row">
                    <label>Lokasi</label>
                    <input type="text" name="location" id="edit_location" required>
                </div>
                <div class="modal-form-row">
                    <label>Tanggal</label>
                    <input type="date" name="date" id="edit_date" required>
                </div>
                <div class="modal-form-row">
                    <label>Deskripsi</label>
                    <textarea name="description" id="edit_description" required></textarea>
                </div>
                <div class="modal-form-row">
                    <label>Imbalan (opsional)</label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">Rp.</span>
                        <input type="text" name="reward_offered" id="edit_reward_offered" placeholder="Contoh: 50.000">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label>Ganti Foto</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div id="editError" style="display:none; color:#e53935; font-size:13px; margin-bottom:10px; padding:8px 12px; background:rgba(229,57,53,0.08); border-radius:8px;"></div>
                <div class="modal-actions">
                    <button type="submit" class="modal-submit-btn" id="editSubmitBtn">Update Item</button>
                    <button type="button" class="modal-cancel-btn" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div>
            <h4>Site</h4>
            Lost<br>Report Lost<br>Found<br>Report Found
        </div>
        <div>
            <h4>Help</h4>
            Customer Support<br>Terms & Conditions<br>Privacy Policy
        </div>
        <div>
            <h4>Links</h4>
            LinkedIn<br>Facebook<br>YouTube<br>About Us
        </div>
        <div>
            <h4>Contact</h4>
            Tel: +94 716520690<br>
            Email: talkprojects@wenix.com
        </div>
    </footer>

    <script src="{{ asset('js/lost.js') }}"></script>
    <script src="{{ asset('js/home.js') }}"></script>
    <script src="{{ asset('js/theme.js') }}"></script>

    <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ==================== REPORT MODAL ====================
    function openReportModal() {
        document.getElementById('reportModal').style.display = 'flex';
        document.getElementById('reportError').style.display = 'none';
        document.getElementById('reportForm').reset();
    }
    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
    }

    document.getElementById('reportModal').addEventListener('click', function(e) {
        if (e.target === this) closeReportModal();
    });

    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = document.getElementById('reportSubmitBtn');
        let errDiv = document.getElementById('reportError');
        btn.disabled = true;
        btn.textContent = 'Submitting...';
        errDiv.style.display = 'none';

        let formData = new FormData(this);

        fetch('/report-lost', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) return res.json().then(d => { throw d; });
            return res.json();
        })
        .then(data => {
            closeReportModal();
            refreshItemList();
        })
        .catch(err => {
            if (err.errors) {
                let msgs = Object.values(err.errors).flat().join('<br>');
                errDiv.innerHTML = msgs;
            } else {
                errDiv.textContent = err.message || 'Terjadi kesalahan. Coba lagi.';
            }
            errDiv.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Submit Report';
        });
    });

    // ==================== EDIT MODAL ====================
    function openEditModal(itemId) {
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('editError').style.display = 'none';
        document.getElementById('editItemId').value = itemId;

        fetch('/lost/' + itemId + '/json', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_item_name').value = data.item_name || '';
            document.getElementById('edit_item_type').value = data.item_type || '';
            document.getElementById('edit_location').value = data.location || '';
            document.getElementById('edit_date').value = data.date || '';
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_reward_offered').value = data.reward_offered || '';
        });
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let btn = document.getElementById('editSubmitBtn');
        let errDiv = document.getElementById('editError');
        let itemId = document.getElementById('editItemId').value;
        btn.disabled = true;
        btn.textContent = 'Updating...';
        errDiv.style.display = 'none';

        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch('/lost/' + itemId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) return res.json().then(d => { throw d; });
            return res.json();
        })
        .then(data => {
            closeEditModal();
            refreshItemList();
        })
        .catch(err => {
            if (err.errors) {
                let msgs = Object.values(err.errors).flat().join('<br>');
                errDiv.innerHTML = msgs;
            } else {
                errDiv.textContent = err.message || 'Terjadi kesalahan. Coba lagi.';
            }
            errDiv.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Update Item';
        });
    });

    // ==================== LIVE SEARCH ====================
    let searchTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function() {
        let query = this.value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            let url = '/lost' + (query ? '?search=' + encodeURIComponent(query) : '');
            fetch(url)
                .then(res => res.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let newList = doc.getElementById('itemList');
                    if (newList) {
                        document.getElementById('itemList').innerHTML = newList.innerHTML;
                    }
                });
        }, 300);
    });

    // ==================== REFRESH HELPER ====================
    function refreshItemList() {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newList = doc.getElementById('itemList');
                if (newList) {
                    document.getElementById('itemList').innerHTML = newList.innerHTML;
                }
            });
    }

    // Auto-refresh every 15 seconds
    setInterval(function() {
        refreshItemList();
    }, 15000);

    // ==================== CURRENCY FORMAT ====================
    function formatRupiah(angka) {
        let num = angka.toString().replace(/[^0-9]/g, '');
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function applyRupiahFormat() {
        document.querySelectorAll('.reward-display').forEach(function(el) {
            let raw = el.dataset.raw || el.textContent.replace(/[^0-9]/g, '');
            if (raw && !isNaN(raw)) {
                el.dataset.raw = raw;
                el.textContent = 'Rp. ' + formatRupiah(raw);
            }
        });
    }

    // Format Rp. inputs as user types
    document.querySelectorAll('.input-prefix-wrap input[name="reward_offered"]').forEach(function(input) {
        input.addEventListener('input', function() {
            let pos = this.selectionStart;
            let raw = this.value.replace(/\./g, '');
            if (raw && !isNaN(raw)) {
                this.value = formatRupiah(raw);
            }
        });
    });

    // ==================== CARD ANIMATIONS ====================
    function animateCards() {
        let cards = document.querySelectorAll('#itemList .item-card');
        cards.forEach(function(card, i) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(24px)';
            card.style.transition = 'none';
            setTimeout(function() {
                card.style.transition = 'opacity 0.45s ease, transform 0.45s cubic-bezier(0.4,0,0.2,1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, i * 70);
        });
    }

    // Run on page load
    animateCards();

    // Override refreshItemList to also animate after refresh
    const _origRefresh = refreshItemList;
    refreshItemList = function() {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newList = doc.getElementById('itemList');
                if (newList) {
                    document.getElementById('itemList').innerHTML = newList.innerHTML;
                    animateCards();
                }
            });
    };
    </script>

    </body>
    </html>
