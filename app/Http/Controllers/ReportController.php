<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'daily');
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $month = $request->query('month', Carbon::today()->format('Y-m'));

        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Laporan Harian
        $dailyQuery = Order::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->whereNotIn('status', ['batal', 'rejected', 'cancelled']);
        
        $dailyOrders = (clone $dailyQuery)->orderBy('id', 'desc')->get();
        $dailyTotalRevenue = (clone $dailyQuery)->whereIn('status', ['selesai', 'completed'])->sum('total_biaya');
        $dailyTotalCount = (clone $dailyQuery)->count();

        // Laporan Bulanan (Per Hari dalam bulan terpilih)
        $selectedYear = date('Y', strtotime($month));
        $selectedMonth = date('m', strtotime($month));

        $monthlyBreakdown = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN status IN ("selesai", "completed") THEN total_biaya ELSE 0 END) as revenue')
            )
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->whereNotIn('status', ['batal', 'rejected', 'cancelled'])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $monthlyTotalRevenue = Order::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->whereIn('status', ['selesai', 'completed'])
            ->sum('total_biaya');

        $monthlyTotalCount = Order::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->whereNotIn('status', ['batal', 'rejected', 'cancelled'])
            ->count();

        // Laporan per Layanan
        $serviceBreakdown = DB::table('detail_orders')
            ->join('orders', 'detail_orders.order_id', '=', 'orders.id')
            ->leftJoin('services', 'detail_orders.service_id', '=', 'services.id')
            ->select(
                DB::raw('COALESCE(services.nama_layanan, "Layanan Laundry") as service_name'),
                DB::raw('COALESCE(services.tipe, "kg") as service_type'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(CASE WHEN services.tipe = "kg" OR services.tipe IS NULL THEN detail_orders.berat ELSE detail_orders.jumlah END) as total_volume'),
                DB::raw('SUM(CASE WHEN orders.status IN ("selesai", "completed") THEN detail_orders.subtotal ELSE 0 END) as revenue')
            )
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
            ->whereNotIn('orders.status', ['batal', 'rejected', 'cancelled'])
            ->groupBy('services.nama_layanan', 'services.tipe')
            ->orderBy('revenue', 'desc')
            ->get();

        // Laporan per Kasir
        $cashierBreakdown = Order::leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                DB::raw('COALESCE(users.name, "Kasir Utama") as cashier_name'),
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(CASE WHEN orders.status IN ("selesai", "completed") THEN orders.total_biaya ELSE 0 END) as revenue')
            )
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
            ->whereNotIn('orders.status', ['batal', 'rejected', 'cancelled'])
            ->groupBy('users.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return view('reports.index', compact(
            'tab',
            'startDate',
            'endDate',
            'month',
            'dailyOrders',
            'dailyTotalRevenue',
            'dailyTotalCount',
            'monthlyBreakdown',
            'monthlyTotalRevenue',
            'monthlyTotalCount',
            'serviceBreakdown',
            'cashierBreakdown'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tab = $request->query('tab', 'daily');
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $month = $request->query('month', Carbon::today()->format('Y-m'));

        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        $filename = 'Laporan_Bluewash_' . strtoupper($tab) . '_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Prepare Data & Range
        if ($tab === 'daily') {
            $dailyOrders = Order::with(['customer', 'detailOrders.service', 'user'])
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->whereNotIn('status', ['batal', 'rejected', 'cancelled'])
                ->orderBy('id', 'desc')
                ->get();
            $dataCount = count($dailyOrders);
            $lastRow = 4 + max($dataCount, 1);
            $autoFilterRange = "R4C1:R{$lastRow}C8";

        } elseif ($tab === 'monthly') {
            $selectedYear = date('Y', strtotime($month));
            $selectedMonth = date('m', strtotime($month));

            $monthlyBreakdown = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(CASE WHEN status IN ("selesai", "completed") THEN total_biaya ELSE 0 END) as revenue')
                )
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->whereNotIn('status', ['batal', 'rejected', 'cancelled'])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
            $dataCount = count($monthlyBreakdown);
            $lastRow = 4 + max($dataCount, 1);
            $autoFilterRange = "R4C1:R{$lastRow}C4";

        } elseif ($tab === 'service') {
            $serviceBreakdown = DB::table('detail_orders')
                ->join('orders', 'detail_orders.order_id', '=', 'orders.id')
                ->leftJoin('services', 'detail_orders.service_id', '=', 'services.id')
                ->select(
                    DB::raw('COALESCE(services.nama_layanan, "Layanan Laundry") as service_name'),
                    DB::raw('COALESCE(services.tipe, "kg") as service_type'),
                    DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN services.tipe = "kg" OR services.tipe IS NULL THEN detail_orders.berat ELSE detail_orders.jumlah END) as total_volume'),
                    DB::raw('SUM(CASE WHEN orders.status IN ("selesai", "completed") THEN detail_orders.subtotal ELSE 0 END) as revenue')
                )
                ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
                ->whereNotIn('orders.status', ['batal', 'rejected', 'cancelled'])
                ->groupBy('services.nama_layanan', 'services.tipe')
                ->orderBy('revenue', 'desc')
                ->get();
            $dataCount = count($serviceBreakdown);
            $lastRow = 4 + max($dataCount, 1);
            $autoFilterRange = "R4C1:R{$lastRow}C6";

        } elseif ($tab === 'cashier') {
            $cashierBreakdown = Order::leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->select(
                    DB::raw('COALESCE(users.name, "Kasir Utama") as cashier_name'),
                    DB::raw('COUNT(orders.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN orders.status IN ("selesai", "completed") THEN orders.total_biaya ELSE 0 END) as revenue')
                )
                ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
                ->whereNotIn('orders.status', ['batal', 'rejected', 'cancelled'])
                ->groupBy('users.name')
                ->orderBy('revenue', 'desc')
                ->get();
            $dataCount = count($cashierBreakdown);
            $lastRow = 4 + max($dataCount, 1);
            $autoFilterRange = "R4C1:R{$lastRow}C4";
        }

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Laundry</x:Name><x:WorksheetOptions><x:DisplayGridlines/><x:AutoFilter><x:Range>' . $autoFilterRange . '</x:Range></x:AutoFilter></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'body, table { font-family: "Calibri", Arial, sans-serif; font-size: 11pt; color: #000000; }';
        $html .= 'table { border-collapse: collapse; }';
        $html .= 'th { background-color: #1f497d; color: #ffffff; font-weight: bold; padding: 6px 12px; border: 1px solid #1f497d; text-align: center; vertical-align: middle; white-space: nowrap; }';
        $html .= 'td { padding: 6px 10px; border: 1px solid #d9d9d9; vertical-align: middle; }';
        $html .= '.text-center { text-align: center; white-space: nowrap; }';
        $html .= '.text-nowrap { white-space: nowrap; }';
        $html .= '.text-left { text-align: left; }';
        $html .= '.text-right { text-align: right; white-space: nowrap; }';
        $html .= '.text-bold { font-weight: bold; }';
        $html .= '.num-currency { mso-number-format:"\#\,\#\#0"; text-align: right; white-space: nowrap; }';
        $html .= '.num-text { mso-number-format:"\@"; text-align: center; white-space: nowrap; }';
        $html .= '.title-header { font-size: 14pt; font-weight: bold; color: #1f497d; text-align: center; }';
        $html .= '.subtitle-header { font-size: 10pt; color: #595959; font-style: italic; text-align: center; }';

        $html .= '.total-row td { background-color: #f2f2f2; font-weight: bold; border: 1px solid #d9d9d9; border-top: 2px solid #1f497d; border-bottom: 2px solid #1f497d; }';

        $html .= '</style></head><body>';

        if ($tab === 'daily') {
            $html .= '<table>';
            $html .= '<tr><td colspan="8" class="title-header" align="center" style="border:none;">BLUEWASH LAUNDRY - LAPORAN TRANSAKSI HARIAN</td></tr>';
            $html .= '<tr><td colspan="8" class="subtitle-header" align="center" style="border:none;">Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) . ' | Dicetak: ' . Carbon::now()->translatedFormat('d M Y H:i') . '</td></tr>';
            $html .= '<tr><td colspan="8" style="border:none;"></td></tr>';

            $html .= '<thead><tr>';
            $html .= '<th>No</th>';
            $html .= '<th>Kode Nota</th>';
            $html .= '<th>Tanggal Masuk</th>';
            $html .= '<th>Nama Pelanggan</th>';
            $html .= '<th>No. Telepon</th>';
            $html .= '<th>Detail Layanan</th>';
            $html .= '<th>Status</th>';
            $html .= '<th>Total Biaya (Rp)</th>';
            $html .= '</tr></thead><tbody>';

            $totalRevenue = 0;
            foreach ($dailyOrders as $idx => $order) {
                $isSelesai = in_array($order->status, ['selesai', 'completed']);
                if ($isSelesai) {
                    $totalRevenue += $order->total_biaya;
                }

                $serviceDetails = [];
                foreach ($order->detailOrders as $detail) {
                    $name = $detail->service->nama_layanan ?? 'Layanan';
                    $vol = ($detail->service->tipe ?? 'kg') === 'kg' ? ($detail->berat . ' Kg') : ($detail->jumlah . ' Pcs');
                    $serviceDetails[] = $name . ' (' . $vol . ')';
                }

                $statusText = $isSelesai ? 'Selesai' : 'Diproses';

                $html .= '<tr>';
                $html .= '<td class="text-center">' . ($idx + 1) . '</td>';
                $html .= '<td class="num-text text-bold">' . e($order->kode_nota) . '</td>';
                $html .= '<td class="text-center">' . ($order->created_at ? $order->created_at->format('d/m/Y H:i') : '-') . '</td>';
                $html .= '<td class="text-nowrap">' . e($order->customer->nama ?? '-') . '</td>';
                $html .= '<td class="num-text">' . e($order->customer->telepon ?? '-') . '</td>';
                $html .= '<td>' . e(implode(', ', $serviceDetails)) . '</td>';
                $html .= '<td class="text-center">' . $statusText . '</td>';
                $html .= '<td class="num-currency">' . $order->total_biaya . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total-row">';
            $html .= '<td colspan="7" class="text-right text-bold">TOTAL PENDAPATAN (SELESAI):</td>';
            $html .= '<td class="num-currency text-bold">' . $totalRevenue . '</td>';
            $html .= '</tr>';
            $html .= '</tbody></table>';

        } elseif ($tab === 'monthly') {
            $html .= '<table>';
            $html .= '<tr><td colspan="4" class="title-header" align="center" style="border:none;">BLUEWASH LAUNDRY - LAPORAN BULANAN</td></tr>';
            $html .= '<tr><td colspan="4" class="subtitle-header" align="center" style="border:none;">Bulan & Tahun: ' . Carbon::parse($month . '-01')->translatedFormat('F Y') . ' | Dicetak: ' . Carbon::now()->translatedFormat('d M Y H:i') . '</td></tr>';
            $html .= '<tr><td colspan="4" style="border:none;"></td></tr>';

            $html .= '<thead><tr>';
            $html .= '<th>No</th>';
            $html .= '<th>Tanggal</th>';
            $html .= '<th>Total Transaksi</th>';
            $html .= '<th>Total Pendapatan (Rp)</th>';
            $html .= '</tr></thead><tbody>';

            $grandRevenue = 0;
            $grandOrders = 0;
            foreach ($monthlyBreakdown as $idx => $row) {
                $grandRevenue += $row->revenue;
                $grandOrders += $row->total_orders;

                $html .= '<tr>';
                $html .= '<td class="text-center">' . ($idx + 1) . '</td>';
                $html .= '<td class="text-center">' . Carbon::parse($row->date)->format('d/m/Y') . '</td>';
                $html .= '<td class="text-center">' . $row->total_orders . '</td>';
                $html .= '<td class="num-currency">' . $row->revenue . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total-row">';
            $html .= '<td colspan="2" class="text-right text-bold">TOTAL KESELURUHAN:</td>';
            $html .= '<td class="text-center text-bold">' . $grandOrders . '</td>';
            $html .= '<td class="num-currency text-bold">' . $grandRevenue . '</td>';
            $html .= '</tr>';
            $html .= '</tbody></table>';

        } elseif ($tab === 'service') {
            $html .= '<table>';
            $html .= '<tr><td colspan="6" class="title-header" align="center" style="border:none;">BLUEWASH LAUNDRY - LAPORAN PER JENIS LAYANAN</td></tr>';
            $html .= '<tr><td colspan="6" class="subtitle-header" align="center" style="border:none;">Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) . ' | Dicetak: ' . Carbon::now()->translatedFormat('d M Y H:i') . '</td></tr>';
            $html .= '<tr><td colspan="6" style="border:none;"></td></tr>';

            $html .= '<thead><tr>';
            $html .= '<th>No</th>';
            $html .= '<th>Nama Layanan</th>';
            $html .= '<th>Satuan</th>';
            $html .= '<th>Total Transaksi</th>';
            $html .= '<th>Total Volume</th>';
            $html .= '<th>Total Pendapatan (Rp)</th>';
            $html .= '</tr></thead><tbody>';

            $grandRevenue = 0;
            foreach ($serviceBreakdown as $idx => $row) {
                $grandRevenue += $row->revenue;

                $html .= '<tr>';
                $html .= '<td class="text-center">' . ($idx + 1) . '</td>';
                $html .= '<td class="text-nowrap text-bold">' . e($row->service_name) . '</td>';
                $html .= '<td class="text-center">' . strtoupper($row->service_type) . '</td>';
                $html .= '<td class="text-center">' . $row->total_orders . '</td>';
                $html .= '<td class="text-center">' . $row->total_volume . '</td>';
                $html .= '<td class="num-currency">' . $row->revenue . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total-row">';
            $html .= '<td colspan="5" class="text-right text-bold">TOTAL PENDAPATAN:</td>';
            $html .= '<td class="num-currency text-bold">' . $grandRevenue . '</td>';
            $html .= '</tr>';
            $html .= '</tbody>***</table>';

        } elseif ($tab === 'cashier') {
            $html .= '<table>';
            $html .= '<tr><td colspan="4" class="title-header" align="center" style="border:none;">BLUEWASH LAUNDRY - LAPORAN PER STAF KASIR</td></tr>';
            $html .= '<tr><td colspan="4" class="subtitle-header" align="center" style="border:none;">Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate)) . ' | Dicetak: ' . Carbon::now()->translatedFormat('d M Y H:i') . '</td></tr>';
            $html .= '<tr><td colspan="4" style="border:none;"></td></tr>';

            $html .= '<thead><tr>';
            $html .= '<th>No</th>';
            $html .= '<th>Nama Staf Kasir</th>';
            $html .= '<th>Total Transaksi</th>';
            $html .= '<th>Total Pendapatan (Rp)</th>';
            $html .= '</tr></thead><tbody>';

            $grandRevenue = 0;
            foreach ($cashierBreakdown as $idx => $row) {
                $grandRevenue += $row->revenue;

                $html .= '<tr>';
                $html .= '<td class="text-center">' . ($idx + 1) . '</td>';
                $html .= '<td class="text-nowrap text-bold">' . e($row->cashier_name) . '</td>';
                $html .= '<td class="text-center">' . $row->total_orders . '</td>';
                $html .= '<td class="num-currency">' . $row->revenue . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total-row">';
            $html .= '<td colspan="3" class="text-right text-bold">TOTAL PENDAPATAN:</td>';
            $html .= '<td class="num-currency text-bold">' . $grandRevenue . '</td>';
            $html .= '</tr>';
            $html .= '</tbody></table>';
        }

        $html .= '</body></html>';

        return response($html, 200, $headers);
    }

}


