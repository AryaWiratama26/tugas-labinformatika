<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = ApiKey::latest()->paginate(20);
        return view('admin.api-keys.index', compact('keys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $generated = ApiKey::generatePlaintext();
        $key = ApiKey::create([
            'name' => $request->name,
            'key_hash' => $generated['hash'],
            'prefix' => $generated['prefix'],
            'expires_at' => $request->expires_at ?: null,
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('plain_key', $generated['plain'])
            ->with('success', "API key untuk '{$key->name}' berhasil dibuat. Salin key di bawah, tidak akan ditampilkan lagi.");
    }

    public function revoke(ApiKey $apiKey)
    {
        $apiKey->forceFill(['revoked_at' => now()])->save();
        return redirect()->route('admin.api-keys.index')
            ->with('success', "API key '{$apiKey->name}' dicabut.");
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return redirect()->route('admin.api-keys.index')
            ->with('success', "API key '{$apiKey->name}' dihapus permanen.");
    }
}
