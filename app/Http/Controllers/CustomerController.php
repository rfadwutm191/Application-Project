<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Daftar pelanggan
    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('telepon', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('customers.index', compact('customers', 'search'));
    }

    // Simpan pelanggan baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required_without:nama|nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'nama' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $name = $request->input('nama', $request->input('name', 'Pelanggan POS'));
        $phone = $request->input('telepon', $request->input('phone')) ?: '-';
        $address = $request->input('alamat', $request->input('address', '-'));
        $status = $request->input('status', 'aktif');

        $customer = Customer::create([
            'nama' => $name,
            'telepon' => $phone,
            'alamat' => $address ?: '-',
            'status' => $status,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan baru berhasil ditambahkan.',
                'customer' => $customer
            ]);
        }

        return back()->with('success', 'Pelanggan baru berhasil ditambahkan.');
    }

    // Update data pelanggan
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'nullable|string|in:aktif,nonaktif',
        ]);

        $name = $request->input('nama', $request->input('name', $customer->nama));
        $phone = $request->input('telepon', $request->input('phone', $customer->telepon));
        $address = $request->input('alamat', $request->input('address', $customer->alamat));
        $status = $request->input('status', $customer->status ?? 'aktif');

        $customer->update([
            'nama' => $name,
            'telepon' => $phone,
            'alamat' => $address,
            'status' => $status,
        ]);

        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    // Hapus pelanggan
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus.');
    }
}
