<?php
declare(strict_types=1);

// app/Http/Controllers/Admin/ServiceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // Daftar ikon yang didukung (sesuaikan dengan SVG di view nanti)
    protected $supportedIcons = ['cross', 'book', 'heart', 'users', 'hands-helping', 'calendar'];
    // Daftar tema warna yang didukung (sesuaikan dengan kelas Tailwind)
    protected $supportedThemes = ['blue', 'green', 'orange', 'purple', 'red', 'indigo'];

    /**
     * Tampilkan daftar layanan.
     */
    public function index()
    {
        $services = Service::orderBy('order')->orderBy('created_at')->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Tampilkan form tambah layanan.
     */
    public function create()
    {
        $icons = $this->supportedIcons;
        $themes = $this->supportedThemes;
        return view('admin.services.create', compact('icons', 'themes'));
    }

    /**
     * Simpan layanan baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:services,title',
            'description' => 'nullable|string',
            'list_items' => 'nullable|string',
            'icon' => 'nullable|string|in:'.implode(',', $this->supportedIcons),
            'color_theme' => 'required|string|in:'.implode(',', $this->supportedThemes),
            'order' => 'required|integer|min:0',
        ]);

        try {
            Service::create($validatedData);
            return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan layanan. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan form edit layanan.
     */
    public function edit(Service $service) // Route Model Binding
    {
        $icons = $this->supportedIcons;
        $themes = $this->supportedThemes;
        return view('admin.services.edit', compact('service', 'icons', 'themes'));
    }

    /**
     * Update layanan yang ada.
     */
    public function update(Request $request, Service $service)
    {
         $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:services,title,' . $service->id,
            'description' => 'nullable|string',
            'list_items' => 'nullable|string',
            'icon' => 'nullable|string|in:'.implode(',', $this->supportedIcons),
            'color_theme' => 'required|string|in:'.implode(',', $this->supportedThemes),
            'order' => 'required|integer|min:0',
        ]);

         try {
            $service->update($validatedData);
            return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating service: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui layanan. Silakan coba lagi.');
        }
    }

    /**
     * Hapus layanan.
     */
    public function destroy(Service $service)
    {
         try {
            $service->delete();
            return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
             Log::error('Error deleting service: ' . $e->getMessage());
             return back()->with('error', 'Gagal menghapus layanan. Silakan coba lagi.');
        }
    }
}