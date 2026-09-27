@extends('layouts.app')

@section('title', 'Daftar Pelanggan - Bluewash Laundry')
@section('header_title', 'Data Pelanggan')
@section('header_subtitle', 'Manajemen data pelanggan laundry')

@section('content')
<div class="table-actions">
    <form action="{{ route('customers.index') }}" method="GET" class="search-box-wrapper" style="margin: 0;">
        <i class="ri-search-line"></i>
        <input type="text" name="search" class="search-control" placeholder="Cari nama atau HP..." value="{{ $search }}">
    </form>
    
    <button class="btn-primary" onclick="openModal('addCustomerModal')">
        <i class="ri-user-add-line"></i>
        <span>Tambah Pelanggan</span>
    </button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="bw-table datatable">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Status Pelanggan</th>
                    <th>Alamat</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $index => $customer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: #0f172a;">{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>
                            @if(strtolower($customer->status ?? 'aktif') === 'aktif')
                                <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    Pelanggan Aktif
                                </span>
                            @else
                                <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            <div class="action-buttons-cell" style="justify-content: center;">
                                <button class="btn-icon edit" onclick="openEditModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}', '{{ $customer->phone }}', '{{ addslashes($customer->address) }}', '{{ $customer->status ?? 'aktif' }}')" title="Edit Pelanggan">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus Pelanggan">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="addCustomerModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Tambah Pelanggan Baru</h3>
            <button class="btn-close-modal" onclick="closeModal('addCustomerModal')">&times;</button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-container">
                        <i class="ri-user-line input-icon"></i>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama..." required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="phone">No. Telepon / WhatsApp</label>
                    <div class="input-container">
                        <i class="ri-phone-line input-icon"></i>
                        <input type="text" name="phone" class="form-control" placeholder="Masukkan nomor telepon..." required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="add_status">Status Pelanggan</label>
                    <div class="input-container">
                        <i class="ri-checkbox-circle-line input-icon"></i>
                        <select id="add_status" name="status" class="form-control" style="padding-left: 42px; cursor: pointer;">
                            <option value="aktif" selected>Pelanggan Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <div class="input-container">
                        <i class="ri-map-pin-line input-icon" style="top: 18px;"></i>
                        <textarea name="address" class="form-control" style="height: 80px; padding-left: 46px; resize: none;" placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addCustomerModal')">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editCustomerModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Edit Data Pelanggan</h3>
            <button class="btn-close-modal" onclick="closeModal('editCustomerModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit_name">Nama Lengkap</label>
                    <div class="input-container">
                        <i class="ri-user-line input-icon"></i>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit_phone">No. Telepon / WhatsApp</label>
                    <div class="input-container">
                        <i class="ri-phone-line input-icon"></i>
                        <input type="text" id="edit_phone" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit_status">Status Pelanggan</label>
                    <div class="input-container">
                        <i class="ri-checkbox-circle-line input-icon"></i>
                        <select id="edit_status" name="status" class="form-control" style="padding-left: 42px; cursor: pointer;" required>
                            <option value="aktif">Pelanggan Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_address">Alamat Lengkap</label>
                    <div class="input-container">
                        <i class="ri-map-pin-line input-icon" style="top: 18px;"></i>
                        <textarea id="edit_address" name="address" class="form-control" style="height: 80px; padding-left: 46px; resize: none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editCustomerModal')">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    <span>Simpan Perubahan</span>
                </button>
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

    function openEditModal(id, name, phone, address, status) {
        const modal = document.getElementById('editCustomerModal');
        const form = document.getElementById('editForm');
        
        form.action = `/customers/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_status').value = status || 'aktif';
        
        modal.classList.add('active');
    }
</script>
@endsection
