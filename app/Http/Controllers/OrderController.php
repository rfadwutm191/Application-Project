<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Service;
use App\Models\DetailOrder;
use App\Models\LogNotifikasiWa;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Daftar transaksi
    public function index(Request $request)
    {
        $search = $request->query('search');
        $statusFilter = $request->query('status');

        $orders = Order::with(['customer', 'detailOrders.service', 'logNotifikasiWa'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_nota', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($custQ) use ($search) {
                          $custQ->where('nama', 'like', "%{$search}%")
                                ->orWhere('telepon', 'like', "%{$search}%");
                      });
                });
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                if (in_array($statusFilter, ['diproses', 'processing', 'confirmed', 'antrean', 'baru', 'proses'])) {
                    $query->whereIn('status', ['diproses', 'processing', 'confirmed', 'antrean', 'queued', 'pending', 'baru', 'proses']);
                } elseif (in_array($statusFilter, ['siap_diambil', 'ready'])) {
                    $query->whereIn('status', ['siap_diambil', 'ready']);
                } elseif (in_array($statusFilter, ['selesai', 'completed', 'diambil'])) {
                    $query->whereIn('status', ['selesai', 'completed', 'diambil']);
                } else {
                    $query->where('status', $statusFilter);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $customers = Customer::orderBy('nama', 'asc')->get();
        $services = Service::orderBy('nama_layanan', 'asc')->get();

        return view('orders.index', compact('orders', 'customers', 'services', 'search', 'statusFilter'));
    }

    // Form transaksi baru
    public function create()
    {
        $customers = Customer::where('status', 'aktif')->orderBy('nama', 'asc')->get();
        $services = Service::orderBy('nama_layanan', 'asc')->get();

        return view('orders.create', compact('customers', 'services'));
    }

    // Simpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'new_customer_name' => 'nullable|string|max:255',
            'new_customer_phone' => 'nullable|string|max:20',
            'service_id' => 'required_without:items|nullable|exists:services,id',
            'items' => 'nullable',
            'weight' => 'nullable|numeric|min:0',
            'qty' => 'nullable|integer|min:1',
            'is_express' => 'nullable',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        if ($request->filled('customer_id')) {
            $customer = Customer::find($request->customer_id);
            if (!$customer) {
                $customer = Customer::create([
                    'nama' => $request->new_customer_name ?? 'Pelanggan POS',
                    'telepon' => $request->new_customer_phone ?? '08000000000',
                    'alamat' => $request->input('new_customer_address', '-'),
                ]);
            }
        } else {
            $customer = Customer::create([
                'nama' => $request->new_customer_name ?? 'Pelanggan POS',
                'telepon' => $request->new_customer_phone ?? '08000000000',
                'alamat' => $request->input('new_customer_address', '-'),
            ]);
        }

        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ($lastOrder->id + 1001) : 1001;
        while(Order::where('kode_nota', 'BW-' . $nextNumber)->exists()) {
            $nextNumber++;
        }
        $orderCode = 'BW-' . $nextNumber;

        $isExpress = $request->has('is_express') && ($request->is_express == 1 || $request->is_express == '1' || $request->is_express == 'on' || $request->is_express == true);

        $itemsData = [];
        $subtotalSum = 0;
        $maxDuration = 2;

        $rawItems = $request->input('items');
        if (!empty($rawItems)) {
            $itemsArr = is_string($rawItems) ? json_decode($rawItems, true) : $rawItems;
            if (is_array($itemsArr) && count($itemsArr) > 0) {
                foreach ($itemsArr as $item) {
                    $svc = Service::find($item['id'] ?? null);
                    if ($svc) {
                        $svcType = $svc->tipe ?? $svc->type;
                        $unitPrice = $svc->harga ?? $svc->price;
                        $maxDuration = max($maxDuration, (int)($svc->durasi ?? $svc->duration ?? 2));
                        
                        $qty = (int)($item['qty'] ?? 1);
                        $weight = 0;
                        if ($svcType === 'kg') {
                            $weight = (float)($item['qty'] ?? 1);
                            $qty = 1;
                            $baseSub = (int)round($unitPrice * $weight);
                        } else {
                            $baseSub = (int)round($unitPrice * $qty);
                        }

                        $expressFee = $isExpress ? 10000 : 0;
                        $itemSubtotal = $baseSub + $expressFee;
                        $subtotalSum += $itemSubtotal;

                        $itemsData[] = [
                            'service_id' => $svc->id,
                            'harga_satuan' => $unitPrice,
                            'berat' => $weight,
                            'jumlah' => $qty,
                            'is_express' => $isExpress,
                            'subtotal' => $itemSubtotal,
                        ];
                    }
                }
            }
        }

        if (empty($itemsData) && $request->filled('service_id')) {
            $service = Service::findOrFail($request->service_id);
            $maxDuration = (int)($service->durasi ?? $service->duration ?? 2);
            $weight = (float)$request->input('weight', 0);
            $qty = (int)$request->input('qty', 1);

            if (($service->tipe ?? $service->type) === 'kg') {
                $subtotalBase = (int)round(($service->harga ?? $service->price) * $weight);
                $qty = 1;
            } else {
                $subtotalBase = (int)round(($service->harga ?? $service->price) * $qty);
                $weight = 0;
            }

            $expressFee = $isExpress ? 10000 : 0;
            $subtotalSum = $subtotalBase + $expressFee;

            $itemsData[] = [
                'service_id' => $service->id,
                'harga_satuan' => $service->harga ?? $service->price,
                'berat' => $weight,
                'jumlah' => $qty,
                'is_express' => $isExpress,
                'subtotal' => $subtotalSum,
            ];
        }

        $dateIn = Carbon::now();
        $dateOut = $isExpress ? Carbon::now()->addDay() : Carbon::now()->addDays($maxDuration);

        $discountPercent = (int)$request->input('discount', $request->input('persen_diskon', 0));
        $discountAmount = (int)round($subtotalSum * ($discountPercent / 100));
        $totalPrice = max(0, $subtotalSum - $discountAmount);

        $order = Order::create([
            'kode_nota' => $orderCode,
            'customer_id' => $customer->id,
            'user_id' => Auth::id(),
            'status' => 'diproses',
            'discount' => $discountPercent,
            'discount_amount' => $discountAmount,
            'total_biaya' => $totalPrice,
            'estimasi_selesai' => $dateOut,
            'catatan' => $request->notes,
        ]);

        foreach ($itemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            DetailOrder::create($itemData);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Transaksi baru {$orderCode} berhasil disimpan!",
                'order_id' => $order->id,
                'order_code' => $orderCode,
            ]);
        }

        if ($request->input('print_after_save') == '1' || $request->input('print_after_save') === 1) {
            return redirect()->route('orders.index')
                ->with('success', "Transaksi baru {$orderCode} berhasil disimpan!")
                ->with('print_order_id', $order->id);
        }

        return redirect()->route('orders.index')->with('success', "Transaksi baru {$orderCode} berhasil disimpan!");
    }

    // Update status transaksi
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($newStatus === 'confirmed') $newStatus = 'diproses';
        if ($newStatus === 'processing') $newStatus = 'diproses';
        if ($newStatus === 'ready') $newStatus = 'siap_diambil';
        if ($newStatus === 'completed') $newStatus = 'selesai';

        $order->updateStatus($newStatus);

        $statusLabels = [
            'diproses' => 'Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'batal' => 'Batal',
        ];
        $displayStatus = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        $waNotice = '';
        if (!in_array($oldStatus, ['siap_diambil', 'ready']) && in_array($newStatus, ['siap_diambil', 'ready'])) {
            $waResult = WhatsAppService::sendOrderReadyNotification($order);
            if (!empty($waResult['success'])) {
                $waNotice = ' (WA Terkirim)';
            } else {
                $waNotice = ' (' . ($waResult['message'] ?? 'Gagal WA') . ')';
            }
        }

        return back()->with('success', "Status {$order->order_code} diubah ke {$displayStatus}{$waNotice}.");
    }

    // Update data transaksi
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'nullable|string',
            'weight' => 'required_if:service_type,kg|numeric|min:0.1',
            'qty' => 'required_if:service_type,pcs|integer|min:1',
        ]);

        $detail = $order->detailOrders()->first();
        if ($detail) {
            $weight = (float)$request->input('weight', $detail->weight);
            $qty = (int)$request->input('qty', $detail->qty);
            $detail->weight = $weight;
            $detail->qty = $qty;
            $detail->calculateSubtotal();
            $detail->save();

            $order->hitungTotal();
        }

        if ($request->filled('status')) {
            $order->updateStatus($request->status);
        }

        return back()->with('success', "Data transaksi {$order->order_code} berhasil diperbarui!");
    }

    // Cetak struk transaksi
    public function receipt(Order $order)
    {
        $order->load(['customer', 'detailOrders', 'user']);
        return view('orders.receipt', compact('order'));
    }

    // Cari nota struk
    public function printSearch(Request $request)
    {
        $search = $request->query('search');
        $orders = collect();

        if ($search) {
            $orders = Order::where('kode_nota', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('telepon', 'like', "%{$search}%");
                })
                ->orderBy('id', 'desc')
                ->take(15)
                ->get();
        } else {
            $orders = Order::orderBy('id', 'desc')->take(10)->get();
        }

        return view('orders.print_search', compact('orders', 'search'));
    }

    // Hapus transaksi
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
