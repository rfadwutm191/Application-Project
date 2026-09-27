<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id', 'asc')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:kg,pcs',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        Service::create($request->only('name', 'type', 'price', 'duration'));

        return back()->with('success', 'Layanan laundry baru berhasil ditambahkan.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:kg,pcs',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        $service->update($request->only('name', 'type', 'price', 'duration'));

        return back()->with('success', 'Layanan laundry berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Layanan laundry berhasil dihapus.');
    }
}
