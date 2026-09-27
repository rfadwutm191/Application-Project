@extends('layouts.app')

@section('title', 'Laporan Keuangan & Operasional - Bluewash Laundry')
@section('header_title', 'Laporan & Analisis Transaksi')
@section('header_subtitle', 'Tinjauan ringkasan pendapatan harian, bulanan, per layanan, dan per kasir')

@section('content')
<div style="width: 100%; margin: 0; display: flex; flex-direction: column; gap: 24px;">

    {{-- Toolbar Filter --}}
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04); padding: 16px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            
            {{-- Filter laporan --}}
            <div style="display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; flex: 1; min-width: 0;">
                {{-- Jenis laporan --}}
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                        <i class="ri-bar-chart-2-line" style="color: #2563eb;"></i> Jenis Laporan
                    </label>
                    <select class="form-control" onchange="window.location.href=this.value" style="padding: 8px 12px; font-size: 13px; font-weight: 700; border-radius: 8px; border: 1.5px solid #2563eb; background: #ffffff; color: #0f172a; cursor: pointer; min-width: 190px;">
                        <option value="{{ route('reports.index', ['tab' => 'daily', 'start_date' => $startDate, 'end_date' => $endDate]) }}" {{ $tab === 'daily' ? 'selected' : '' }}>Laporan Harian</option>
                        <option value="{{ route('reports.index', ['tab' => 'monthly', 'month' => $month]) }}" {{ $tab === 'monthly' ? 'selected' : '' }}>Laporan Bulanan</option>
                        <option value="{{ route('reports.index', ['tab' => 'service', 'start_date' => $startDate, 'end_date' => $endDate]) }}" {{ $tab === 'service' ? 'selected' : '' }}>Per Jenis Layanan</option>
                        <option value="{{ route('reports.index', ['tab' => 'cashier', 'start_date' => $startDate, 'end_date' => $endDate]) }}" {{ $tab === 'cashier' ? 'selected' : '' }}>Per Staf Kasir</option>
                    </select>
                </div>

                {{-- Filter tanggal --}}
                <form action="{{ route('reports.index') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    @if($tab === 'monthly')
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Pilih Bulan & Tahun</label>
                            <input type="month" name="month" value="{{ $month }}" class="form-control" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; min-width: 200px;" onchange="this.form.submit()">
                        </div>
                    @else
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;">
                        </div>
                        <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 9px 18px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37,99,235,0.2);">
                            <i class="ri-filter-3-line"></i> Filter Data
                        </button>
                    @endif
                </form>
            </div>

            {{-- Tombol ekspor --}}
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.export_excel', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate, 'month' => $month]) }}" class="btn" style="background: #16a34a; color: white; padding: 9px 18px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(22,163,74,0.25); white-space: nowrap;">
                    <i class="ri-file-excel-2-line" style="font-size: 16px;"></i> Export Excel
                </a>
            </div>


        </div>
    </div>

    @if($tab === 'daily')
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-info">
                    <span class="stat-label">Total Pemasukan (Selesai)</span>
                    <span class="stat-value">Rp {{ number_format($dailyTotalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="stat-icon-wrapper blue">
                    <i class="ri-wallet-3-line"></i>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-info">
                    <span class="stat-label">Total Pesanan Masuk</span>
                    <span class="stat-value">{{ number_format($dailyTotalCount) }} Transaksi</span>
                </div>
                <div class="stat-icon-wrapper green">
                    <i class="ri-shopping-basket-line"></i>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-file-list-3-line" style="color: #2563eb;"></i>
                    <span>Rincian Transaksi Harian</span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="bw-table datatable">
                    <thead>
                        <tr>
                            <th>No. Nota</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th style="text-align: right;">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyOrders as $order)
                            <tr>
                                <td style="font-weight: 700; color: #2563eb;">{{ $order->order_code }}</td>
                                <td style="color: #64748b;">{{ $order->order_date ? $order->order_date->format('d/m/Y') : $order->created_at->format('d/m/Y') }}</td>
                                <td style="font-weight: 600; color: #0f172a;">{{ $order->customer_name }}</td>
                                <td>{{ $order->service_name }}</td>
                                <td>{{ $order->cashier_name }}</td>
                                <td>
                                    @if(in_array($order->status, ['completed', 'selesai']))
                                        <span class="badge badge-completed">Selesai</span>
                                    @else
                                        <span class="badge badge-washing">Diproses</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">Tidak ada transaksi pada periode tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'monthly')
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-info">
                    <span class="stat-label">Total Pemasukan Bulan {{ date('F Y', strtotime($month)) }}</span>
                    <span class="stat-value">Rp {{ number_format($monthlyTotalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="stat-icon-wrapper blue">
                    <i class="ri-bank-card-line"></i>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-info">
                    <span class="stat-label">Total Transaksi Bulanan</span>
                    <span class="stat-value">{{ number_format($monthlyTotalCount) }} Pesanan</span>
                </div>
                <div class="stat-icon-wrapper purple">
                    <i class="ri-file-list-3-line"></i>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-bar-chart-box-line" style="color: #2563eb;"></i>
                    <span>Rincian Pendapatan Bulanan (per Hari)</span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="bw-table datatable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah Transaksi</th>
                            <th style="text-align: right;">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyBreakdown as $row)
                            <tr>
                                <td style="font-weight: 700; color: #0f172a;">{{ date('d F Y', strtotime($row->date)) }}</td>
                                <td style="font-weight: 600;">{{ $row->total_orders }} Transaksi</td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada data pendapatan pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'service')
        <div class="table-card">
            <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-price-tag-3-line" style="color: #2563eb;"></i>
                    <span>Analisis Per Jenis Layanan Laundry</span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="bw-table datatable">
                    <thead>
                        <tr>
                            <th>Jenis Layanan</th>
                            <th>Tipe Unit</th>
                            <th>Total Pesanan</th>
                            <th>Volume Cucian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceBreakdown as $srv)
                            <tr>
                                <td style="font-weight: 700; color: #0f172a;">{{ $srv->service_name }}</td>
                                <td style="text-transform: uppercase; font-weight: 600;"><span class="badge badge-regular">{{ $srv->service_type }}</span></td>
                                <td style="font-weight: 600;">{{ $srv->total_orders }} Transaksi</td>
                                <td style="font-weight: 600; color: #2563eb;">
                                    {{ $srv->service_type === 'kg' ? number_format($srv->total_volume, 1).' Kg' : number_format($srv->total_volume).' Pcs' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada data transaksi per layanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'cashier')
        <div class="table-card">
            <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #ffffff;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-user-star-line" style="color: #2563eb;"></i>
                    <span>Performa Staf Kasir</span>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="bw-table datatable">
                    <thead>
                        <tr>
                            <th>Nama Kasir / Petugas</th>
                            <th>Total Transaksi Diinput</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashierBreakdown as $csh)
                            <tr>
                                <td style="font-weight: 700; color: #0f172a;">
                                    <i class="ri-user-3-line" style="color: #2563eb; margin-right: 6px;"></i> {{ $csh->cashier_name }}
                                </td>
                                <td style="font-weight: 600;">{{ $csh->total_orders }} Transaksi</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada data performa kasir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
{{-- Template ekspor excel --}}
<div id="excelExportArea" style="display: none;">
    <table style="border-collapse: collapse; font-family: monospace, Arial, sans-serif; font-size: 11pt; color: #000000;">
        <tr>
            <td colspan="{{ $tab === 'daily' ? 8 : ($tab === 'service' ? 6 : 4) }}" style="font-size: 12pt; font-weight: normal; color: #000000; text-align: center; border: none;">BLUEWASH LAUNDRY</td>
        </tr>
        <tr>
            <td colspan="{{ $tab === 'daily' ? 8 : ($tab === 'service' ? 6 : 4) }}" style="font-size: 11pt; font-weight: normal; color: #000000; text-align: center; border: none;">
                @if($tab === 'daily') LAPORAN HARIAN
                @elseif($tab === 'monthly') LAPORAN PENDAPATAN BULANAN
                @elseif($tab === 'service') LAPORAN OMZET PER JENIS LAYANAN
                @elseif($tab === 'cashier') LAPORAN PERFORMA STAF KASIR
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ $tab === 'daily' ? 8 : ($tab === 'service' ? 6 : 4) }}" style="font-size: 11pt; color: #000000; text-align: left; border: none;">
                @if($tab === 'monthly') Periode: {{ date('F Y', strtotime($month)) }}
                @else Periode: {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="{{ $tab === 'daily' ? 8 : ($tab === 'service' ? 6 : 4) }}" style="font-size: 11pt; color: #000000; text-align: left; border: none;">
                Dicetak: {{ Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ $tab === 'daily' ? 8 : ($tab === 'service' ? 6 : 4) }}" style="border-bottom: 1px solid #000000; border-top: none; border-left: none; border-right: none;"></td>
        </tr>

        @if($tab === 'daily')
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">No</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">No. Nota</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Tanggal</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Pelanggan</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Layanan</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Kasir</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Status</th>
                    <th style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-left: none; border-right: none; padding: 6px 10px; background-color: #ffffff; color: #000000; font-weight: normal; text-align: left;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyOrders as $idx => $order)
                    <tr style="color: #000000;">
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ $idx + 1 }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ $order->order_code }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ $order->order_date ? $order->order_date->format('d/m/y') : $order->created_at->format('d/m/y') }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ substr($order->customer_name, 0, 15) }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ substr($order->service_name, 0, 15) }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ substr($order->cashier_name, 0, 10) }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">{{ in_array($order->status, ['completed', 'selesai']) ? 'Selesai' : 'Diproses' }}</td>
                        <td style="border: none; padding: 5px 8px; text-align: left; color: #000000;">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="border: none; padding: 10px; text-align: center; color: #000000;">Tidak ada data transaksi.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr><td colspan="8" style="border-top: 1px solid #000000; border-bottom: none; border-left: none; border-right: none;"></td></tr>
                <tr style="color: #000000;">
                    <td colspan="8" style="border: none; padding: 6px 10px 0 10px; font-weight: normal; text-align: center; background-color: #ffffff; color: #000000;">Total Pendapatan:</td>
                </tr>
                <tr style="color: #000000;">
                    <td colspan="8" style="border: none; padding: 0 10px 6px 10px; font-weight: normal; text-align: center; background-color: #ffffff; color: #000000;">Rp{{ number_format($dailyTotalRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>

        @elseif($tab === 'monthly')
            <thead>
                <tr>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">No</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: left;">Tanggal</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">Jumlah Transaksi</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: right;">Total Pendapatan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyBreakdown as $idx => $row)
                    <tr style="color: #000000;">
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; color: #000000;">{{ date('d/m/Y', strtotime($row->date)) }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $row->total_orders }} Transaksi</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: right; color: #000000;">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="border: 1px solid #cbd5e1; padding: 10px; text-align: center; color: #000000;">Belum ada data bulanan.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="color: #000000;">
                    <td colspan="3" style="border-top: 2px solid #64748b; border-bottom: 2px double #64748b; border-left: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold; text-align: right; background-color: #f8fafc; color: #000000;">Total Pendapatan:</td>
                    <td style="border-top: 2px solid #64748b; border-bottom: 2px double #64748b; border-right: 1px solid #cbd5e1; padding: 6px 10px; font-weight: bold; text-align: right; background-color: #f8fafc; color: #000000;">Rp {{ number_format($monthlyTotalRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>

        @elseif($tab === 'service')
            <thead>
                <tr>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">No</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: left;">Jenis Layanan</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">Tipe Unit</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">Total Pesanan</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">Volume Cucian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceBreakdown as $idx => $srv)
                    <tr style="color: #000000;">
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; color: #000000;">{{ $srv->service_name }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; text-transform: uppercase; color: #000000;">{{ $srv->service_type }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $srv->total_orders }} Transaksi</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $srv->service_type === 'kg' ? number_format($srv->total_volume, 1).' Kg' : number_format($srv->total_volume).' Pcs' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="border: 1px solid #cbd5e1; padding: 10px; text-align: center; color: #000000;">Belum ada data layanan.</td></tr>
                @endforelse
            </tbody>

        @elseif($tab === 'cashier')
            <thead>
                <tr>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">No</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: left;">Nama Kasir / Petugas</th>
                    <th style="border: 1px solid #94a3b8; padding: 6px 10px; background-color: #f1f5f9; color: #000000; font-weight: bold; text-align: center;">Total Transaksi Diinput</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashierBreakdown as $idx => $csh)
                    <tr style="color: #000000;">
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; color: #000000;">{{ $csh->cashier_name }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; color: #000000;">{{ $csh->total_orders }} Transaksi</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="border: 1px solid #cbd5e1; padding: 10px; text-align: center; color: #000000;">Belum ada data kasir.</td></tr>
                @endforelse
            </tbody>
        @endif
    </table>
</div>

{{-- Modal Pratinjau Laporan --}}
<div id="printReportModal" class="modal-overlay">
    <div class="modal-container" style="max-width: 820px; border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; padding: 16px 24px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="ri-printer-line" style="color: #059669;"></i>
                <span>Pratinjau Dokumen Laporan</span>
            </h3>
            <button class="btn-close-modal" onclick="closeModal('printReportModal')" style="border: none; background: transparent; font-size: 22px; cursor: pointer; color: #64748b;">&times;</button>
        </div>

        <div class="modal-body" style="padding: 24px; max-height: 75vh; overflow-y: auto;">
            {{-- Area cetak --}}
            <div id="printableReportArea" style="background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #1e293b;">
                
                {{-- Header laporan --}}
                <div style="text-align: center; border-bottom: 2px double #0f172a; padding-bottom: 14px; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">BLUEWASH LAUNDRY</h2>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #475569;">Sistem Informasi Manajemen & Laporan Keuangan Laundry</p>
                    <div style="margin-top: 10px; font-size: 14px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px;">
                        @if($tab === 'daily') LAPORAN TRANSAKSI HARIAN
                        @elseif($tab === 'monthly') LAPORAN PENDAPATAN BULANAN
                        @elseif($tab === 'service') LAPORAN OMZET PER JENIS LAYANAN
                        @elseif($tab === 'cashier') LAPORAN PERFORMA STAF KASIR
                        @endif
                    </div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        @if($tab === 'monthly') Periode Bulan: <strong>{{ date('F Y', strtotime($month)) }}</strong>
                        @else Periode Tanggal: <strong>{{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</strong>
                        @endif
                    </div>
                </div>

                {{-- Info cetak --}}
                <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: #64748b; margin-bottom: 16px; background: #f8fafc; padding: 10px 14px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <span>Dicetak Oleh: <strong>{{ Auth::user()->name }} ({{ Auth::user()->role === 'admin' ? 'Administrator' : 'Kasir' }})</strong></span>
                    <span>Waktu Cetak: <strong>{{ Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</strong></span>
                </div>

                {{-- Tabel cetak --}}
                @if($tab === 'daily')
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #334155; font-weight: 700;">
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">No. Nota</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Tanggal</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Pelanggan</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Layanan</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Kasir</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Status</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: right;">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyOrders as $order)
                                <tr>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; font-weight: 700;">{{ $order->order_code }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $order->order_date ? $order->order_date->format('d/m/Y') : $order->created_at->format('d/m/Y') }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $order->customer_name }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $order->service_name }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $order->cashier_name }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ in_array($order->status, ['completed', 'selesai']) ? 'Selesai' : 'Diproses' }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; font-weight: 700;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="border: 1px solid #e2e8f0; padding: 14px; text-align: center;">Tidak ada data transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; font-size: 13px; font-weight: 700;">
                        <span>TOTAL PEMASUKAN SELESAI:</span>
                        <span style="color: #059669; font-size: 15px;">Rp {{ number_format($dailyTotalRevenue, 0, ',', '.') }}</span>
                    </div>

                @elseif($tab === 'monthly')
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #334155; font-weight: 700;">
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Tanggal</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Jumlah Transaksi</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: right;">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyBreakdown as $row)
                                <tr>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; font-weight: 700;">{{ date('d F Y', strtotime($row->date)) }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $row->total_orders }} Transaksi</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; font-weight: 700;">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="border: 1px solid #e2e8f0; padding: 14px; text-align: center;">Belum ada data bulanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; font-size: 13px; font-weight: 700;">
                        <span>TOTAL PENDAPATAN BULAN {{ date('F Y', strtotime($month)) }}:</span>
                        <span style="color: #059669; font-size: 15px;">Rp {{ number_format($monthlyTotalRevenue, 0, ',', '.') }}</span>
                    </div>

                @elseif($tab === 'service')
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #334155; font-weight: 700;">
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Jenis Layanan</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Tipe Unit</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Total Pesanan</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Volume Cucian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceBreakdown as $srv)
                                <tr>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; font-weight: 700;">{{ $srv->service_name }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; text-transform: uppercase;">{{ $srv->service_type }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $srv->total_orders }} Transaksi</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $srv->service_type === 'kg' ? number_format($srv->total_volume, 1).' Kg' : number_format($srv->total_volume).' Pcs' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="border: 1px solid #e2e8f0; padding: 14px; text-align: center;">Belum ada data layanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                @elseif($tab === 'cashier')
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #334155; font-weight: 700;">
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Nama Kasir / Petugas</th>
                                <th style="border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left;">Total Transaksi Diinput</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashierBreakdown as $csh)
                                <tr>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px; font-weight: 700;">{{ $csh->cashier_name }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 8px 10px;">{{ $csh->total_orders }} Transaksi</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="border: 1px solid #e2e8f0; padding: 14px; text-align: center;">Belum ada data kasir.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif

                {{-- Tanda tangan --}}
                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <div style="text-align: center; width: 200px; font-size: 12px; color: #475569;">
                        <div>Penanggung Jawab,</div>
                        <div style="height: 50px;"></div>
                        <div style="font-weight: 700; color: #0f172a; text-decoration: underline;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 10.5px; color: #64748b;">Bluewash Laundry POS</div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn-secondary" onclick="closeModal('printReportModal')">Batal / Tutup</button>
            <button type="button" onclick="exportReportToExcel()" class="btn-primary" style="background: #16a34a; color: #ffffff; border-color: #16a34a; font-weight: 700; border-radius: 8px; padding: 9px 20px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(22,163,74,0.25);">
                <i class="ri-file-excel-2-line"></i>
                <span>Export Excel</span>
            </button>
            <button type="button" onclick="executeReportPrint()" class="btn-primary" style="background: #059669; color: #ffffff; border-color: #059669; font-weight: 700; border-radius: 8px; padding: 9px 20px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(5,150,105,0.25);">
                <i class="ri-printer-line"></i>
                <span>Cetak Sekarang</span>
            </button>
        </div>
    </div>
</div>

<script>
    function openPrintReportModal() {
        document.getElementById('printReportModal').classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function executeReportPrint() {
        window.print();
    }

    function exportReportToExcel() {
        const area = document.getElementById('excelExportArea');
        if (!area) return;

        const tabName = '{{ $tab }}';
        const dateStr = '{{ date("Ymd_His") }}';
        const filename = 'Laporan_Bluewash_' + tabName.toUpperCase() + '_' + dateStr + '.xls';

        const excelXml = '<xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet><' + 'x:Name>Laporan Laundry</' + 'x:Name><' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions></' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml>';

        const html = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <!--[if gte mso 9]>
                ${excelXml}
                <![endif]-->
                <style>
                    body, table, th, td, tr, p, span, div { font-family: monospace, Arial, sans-serif; font-size: 11pt; color: #000000 !important; }
                    table { border-collapse: collapse; width: 100%; color: #000000 !important; }
                    th, td { border: none; padding: 6px 10px; color: #000000 !important; }
                    th { background-color: #ffffff; color: #000000 !important; font-weight: normal; text-align: left; }
                </style>
            </head>
            <body>
                ${area.innerHTML}
            </body>
            </html>
        `;

        const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }


</script>

<style>
    .table-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #printableReportArea, #printableReportArea * {
            visibility: visible;
        }
        #printableReportArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            padding: 0 !important;
        }
        #printReportModal, #printReportModal .modal-container, #printReportModal .modal-body {
            max-height: none !important;
            overflow: visible !important;
            border: none !important;
            box-shadow: none !important;
            background: white !important;
        }
    }
</style>
@endsection
