<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    /**
     * Constructor untuk menerapkan middleware role (hanya admin).
     */
    public function __construct()
    {
        // Hanya role 'admin' yang diizinkan mengakses controller ini
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan halaman pengaturan sistem.
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    // Anda bisa menambahkan metode lain di sini untuk menyimpan pengaturan, dll.
    // public function update(Request $request) { ... }
}
