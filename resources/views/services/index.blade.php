@extends('layouts.app')

@section('title', 'Tarif Layanan - Bluewash Laundry')
@section('header_title', 'Layanan & Tarif Harga')
@section('header_subtitle', 'Manajemen katalog jasa laundry Bluewash')

@section('content')
<div class="table-actions" style="justify-content: flex-end;">
    <button class="btn-primary" onclick="openModal('addServiceModal')">
        <i class="ri-price-tag-3-line"></i>
        <span>Tambah Layanan</span>
    </button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="bw-table datatable">
            <thead>
                <tr>
                    <th style="width: 80px;">No</th>
                    <th>Nama Layanan</th>
                    <th>Satuan / Tarif</th>
                    <th>Harga per Satuan</th>
                    <th>Durasi Kerja</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: #0f172a;">{{ $service->name }}</td>
                        <td>
                            @if($service->type == 'kg')
                                <span class="badge" style="background: rgba(14, 165, 233, 0.1); color: var(--primary);">KILOGRAM (Kg)</span>
                            @else
                                <span class="badge" style="background: rgba(236, 72, 153, 0.1); color: #f472b6;">SATUAN (Pcs)</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--accent);">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td>{{ $service->duration }} Hari</td>
                        <td>
                            <div class="action-buttons-cell" style="justify-content: center;">
                                <button class="btn-icon edit" onclick="openEditModal('{{ $service->id }}', '{{ addslashes($service->name) }}', '{{ $service->type }}', '{{ $service->price }}', '{{ $service->duration }}')" title="Edit Layanan">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus Layanan">
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

<div id="addServiceModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Tambah Layanan Baru</h3>
            <button class="btn-close-modal" onclick="closeModal('addServiceModal')">&times;</button>
        </div>
        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="name">Nama Layanan</label>
                    <div class="input-container">
                        <i class="ri-layout-grid-line input-icon"></i>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Cuci Setrika Bedcover..." required>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="type">Tipe Satuan</label>
                        <div class="input-container">
                            <i class="ri-scale-line input-icon"></i>
                            <select name="type" class="form-control" style="appearance: none; -webkit-appearance: none; padding-right: 30px;" required>
                                <option value="kg">Per Kilogram (Kg)</option>
                                <option value="pcs">Per Satuan (Pcs)</option>
                            </select>
                            <i class="ri-arrow-down-s-line" style="position: absolute; right: 12px; pointer-events: none; color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="duration">Durasi Kerja (Hari)</label>
                        <div class="input-container">
                            <i class="ri-time-line input-icon"></i>
                            <input type="number" name="duration" class="form-control" min="1" placeholder="Hari..." required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="price">Harga per Satuan (Rp)</label>
                    <div class="input-container">
                        <i class="ri-money-dollar-circle-line input-icon"></i>
                        <input type="number" name="price" class="form-control" min="0" placeholder="Contoh: 15000..." required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addServiceModal')">Batal</button>
                <button type="submit" class="btn-primary">
                    <i class="ri-save-line"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editServiceModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Edit Layanan Laundry</h3>
            <button class="btn-close-modal" onclick="closeModal('editServiceModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit_name">Nama Layanan</label>
                    <div class="input-container">
                        <i class="ri-layout-grid-line input-icon"></i>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="edit_type">Tipe Satuan</label>
                        <div class="input-container">
                            <i class="ri-scale-line input-icon"></i>
                            <select id="edit_type" name="type" class="form-control" style="appearance: none; -webkit-appearance: none; padding-right: 30px;" required>
                                <option value="kg">Per Kilogram (Kg)</option>
                                <option value="pcs">Per Satuan (Pcs)</option>
                            </select>
                            <i class="ri-arrow-down-s-line" style="position: absolute; right: 12px; pointer-events: none; color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_duration">Durasi Kerja (Hari)</label>
                        <div class="input-container">
                            <i class="ri-time-line input-icon"></i>
                            <input type="number" id="edit_duration" name="duration" class="form-control" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_price">Harga per Satuan (Rp)</label>
                    <div class="input-container">
                        <i class="ri-money-dollar-circle-line input-icon"></i>
                        <input type="number" id="edit_price" name="price" class="form-control" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editServiceModal')">Batal</button>
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

    function openEditModal(id, name, type, price, duration) {
        const modal = document.getElementById('editServiceModal');
        const form = document.getElementById('editForm');
        
        form.action = `/services/${id}`;
        
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_duration').value = duration;
        
        modal.classList.add('active');
    }
</script>
@endsection
