@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0;">📚 Daftar Siswa</h1>
    <p style="color: #888; font-size: 13px; margin-bottom: 20px;">Data master siswa. Siswa yang ditambahkan di sini bisa mendaftar akun menggunakan NISN-nya.</p>

    {{-- Add Student Form --}}
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px; font-size: 15px;">➕ Tambah Siswa Baru</h3>
        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf
            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">NISN</label>
                    <input type="text" name="nisn" placeholder="Masukkan NISN" required
                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif; font-size: 13px; background: rgba(255,255,255,0.5);">
                    @error('nisn')
                        <small style="color: #e53935; font-size: 11px;">{{ $message }}</small>
                    @enderror
                </div>
                <div style="flex: 2; min-width: 200px;">
                    <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama lengkap siswa" required
                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif; font-size: 13px; background: rgba(255,255,255,0.5);">
                </div>
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Kelas</label>
                    <input type="text" name="kelas" placeholder="e.g. XII RPL 1" required
                        style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif; font-size: 13px; background: rgba(255,255,255,0.5);">
                </div>
                <div>
                    <button type="submit" class="btn-approve" style="padding: 10px 20px;">
                        ➕ Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Filter & Search --}}
    <div class="card" style="margin-bottom: 20px; padding: 16px 20px;">
        <form method="GET" action="{{ route('admin.students') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Cari NISN atau nama..."
                    style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif; font-size: 13px; background: rgba(255,255,255,0.5);">
            </div>
            <div>
                <select name="kelas" onchange="this.form.submit()"
                    style="padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif; font-size: 13px; background: rgba(255,255,255,0.5); cursor: pointer;">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas }}" {{ $kelasFilter == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-approve" style="padding: 10px 16px; font-size: 12px;">Cari</button>
            @if($search || $kelasFilter)
                <a href="{{ route('admin.students') }}" style="color: #e53935; font-size: 12px; text-decoration: none;">✕ Reset</a>
            @endif
        </form>
    </div>

    {{-- Stats --}}
    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <div class="card" style="flex: 1; min-width: 140px; text-align: center; padding: 16px;">
            <div style="font-size: 28px; font-weight: 700; color: #2e7d32;">{{ $students->count() }}</div>
            <div style="font-size: 12px; color: #888;">Total Siswa{{ $kelasFilter ? " ($kelasFilter)" : '' }}</div>
        </div>
        <div class="card" style="flex: 1; min-width: 140px; text-align: center; padding: 16px;">
            <div style="font-size: 28px; font-weight: 700; color: #2e7d32;">{{ $students->where('is_registered', true)->count() }}</div>
            <div style="font-size: 12px; color: #888;">Sudah Daftar</div>
        </div>
        <div class="card" style="flex: 1; min-width: 140px; text-align: center; padding: 16px;">
            <div style="font-size: 28px; font-weight: 700; color: #e65100;">{{ $students->where('is_registered', false)->count() }}</div>
            <div style="font-size: 12px; color: #888;">Belum Daftar</div>
        </div>
    </div>

    {{-- Student Table --}}
    <div class="card" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $student->nisn }}</strong></td>
                    <td>{{ $student->name }}</td>
                    <td>
                        <span class="status-badge" style="background: linear-gradient(135deg, #1565c0, #1976d2); font-size: 11px;">
                            {{ $student->kelas }}
                        </span>
                    </td>
                    <td>
                        @if($student->is_registered)
                            <span class="status-badge" style="background: #28a745;">✅ Sudah Daftar</span>
                        @else
                            <span class="status-badge" style="background: #6c757d;">❌ Belum Daftar</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                            {{-- Edit Button --}}
                            <button type="button" class="btn-approve" style="font-size: 11px; padding: 5px 10px;"
                                onclick="toggleEdit({{ $student->id }})">
                                ✏️ Edit
                            </button>

                            {{-- Delete --}}
                            @if(!$student->is_registered)
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data siswa {{ $student->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" style="font-size: 11px; padding: 5px 10px;">🗑️ Hapus</button>
                                </form>
                            @else
                                <button class="btn-danger" style="font-size: 11px; padding: 5px 10px; opacity: 0.4; cursor: not-allowed;" disabled
                                    title="Hapus akun user terlebih dahulu">🗑️ Hapus</button>
                            @endif
                        </div>

                        {{-- Edit Form (hidden by default) --}}
                        <div id="edit-{{ $student->id }}" style="display: none; margin-top: 8px;">
                            <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                    <input type="text" name="name" value="{{ $student->name }}" required
                                        style="padding: 6px 10px; border-radius: 8px; border: 1px solid #ddd; font-size: 12px; width: 160px; font-family: 'Poppins', sans-serif;">
                                    <input type="text" name="kelas" value="{{ $student->kelas }}" required
                                        style="padding: 6px 10px; border-radius: 8px; border: 1px solid #ddd; font-size: 12px; width: 100px; font-family: 'Poppins', sans-serif;">
                                    <button type="submit" class="btn-approve" style="font-size: 11px; padding: 5px 10px;">💾 Simpan</button>
                                    <button type="button" class="btn-reject" style="font-size: 11px; padding: 5px 10px;"
                                        onclick="toggleEdit({{ $student->id }})">Batal</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                        Belum ada data siswa. Tambahkan siswa baru di atas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function toggleEdit(id) {
            const el = document.getElementById('edit-' + id);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endsection
