@extends('layouts.app')

@section('title', 'Manajemen User Kasir - Bluewash Laundry')
@section('header_title', 'Manajemen User & Staf')
@section('header_subtitle', 'Kelola akun pengguna dan staf kasir laundry')

@section('content')
<div class="table-actions">
    <form action="{{ route('users.index') }}" method="GET" class="search-box-wrapper" style="margin: 0; max-width: 280px; width: 100%;">
        <i class="ri-search-line"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau username..." class="search-control">
    </form>

    <button onclick="openModal('addUserModal')" class="btn-primary" style="display: flex; align-items: center; gap: 8px;">
        <i class="ri-user-add-line"></i>
        <span>Tambah Akun Kasir</span>
    </button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="bw-table datatable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Peran (Role)</th>
                    <th>Tanggal Dibuat</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 700; color: #0f172a;">{{ $user->name }}</td>
                        <td style="color: #2563eb; font-weight: 600;">{{ $user->username }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span style="background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 11px; padding: 4px 12px; border-radius: 12px;">Admin / Owner</span>
                            @else
                                <span style="background: #f0fdf4; color: #15803d; font-weight: 800; font-size: 11px; padding: 4px 12px; border-radius: 12px;">Staf Kasir</span>
                            @endif
                        </td>
                        <td style="color: #64748b;">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td style="text-align: center;">
                            <div class="action-buttons-cell" style="justify-content: center;">
                                <button type="button" onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->username) }}', '{{ $user->role }}')" class="btn-icon edit" title="Edit Data Akun">
                                    <i class="ri-pencil-line"></i>
                                </button>

                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun kasir ini?')" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon delete" title="Hapus Akun">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">Belum ada akun pengguna/kasir.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="addUserModal" class="modal-overlay">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header">
            <h3>Tambah Akun Pengguna / Kasir</h3>
            <button class="btn-close-modal" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Rina Kasir 1" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Username *</label>
                    <input type="text" name="username" class="form-control" placeholder="kasir1" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Peran Akses (Role) *</label>
                    <select name="role" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                        <option value="staff">Staf Kasir (Akses Terbatas)</option>
                        <option value="admin">Administrator / Owner (Akses Penuh)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addUserModal')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<div id="editUserModal" class="modal-overlay">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header">
            <h3>Edit Data Akun Pengguna</h3>
            <button class="btn-close-modal" onclick="closeModal('editUserModal')">&times;</button>
        </div>
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Nama Lengkap *</label>
                    <input type="text" id="edit_name" name="name" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Username *</label>
                    <input type="text" id="edit_username" name="username" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Peran Akses (Role) *</label>
                    <select id="edit_role" name="role" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                        <option value="staff">Staf Kasir (Akses Terbatas)</option>
                        <option value="admin">Administrator / Owner (Akses Penuh)</option>
                    </select>
                </div>

                <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 4px 0;">

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #b45309; margin-bottom: 4px;">
                        <i class="ri-key-line"></i> Reset / Ubah Password (Opsional)
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mereset password" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                    <span style="font-size: 11px; color: #94a3b8; margin-top: 4px; display: block;">Isi password baru jika ingin mereset password pengguna ini.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editUserModal')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(id, name, username, role) {
        const form = document.getElementById('editUserForm');
        form.action = `/users/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value = role;
        document.getElementById('editUserModal').classList.add('active');
    }
</script>
@endsection
