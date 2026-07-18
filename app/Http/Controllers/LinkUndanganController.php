<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkUndangan;

class LinkUndanganController extends Controller
{
    //

    public function show($id)
    {
        $linkUndangan = LinkUndangan::findOrFail($id);
        return view('undangan', compact('linkUndangan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'link_undangan' => 'required|string|max:255',
            'nama_pasangan_1' => 'required|string|max:255',
            'nama_pasangan_2' => 'required|string|max:255',
            'tanggal_pernikahan' => 'required|string|max:255',
            'lokasi_pernikahan' => 'required|string|max:255',
            'tamu_undangan' => 'nullable|string|max:255',
        ]);

        LinkUndangan::create([
            'link_undangan' => $request->link_undangan,
            'nama_pasangan_1' => $request->nama_pasangan_1,
            'nama_pasangan_2' => $request->nama_pasangan_2,
            'tanggal_pernikahan' => $request->tanggal_pernikahan,
            'lokasi_pernikahan' => $request->lokasi_pernikahan,
            'tamu_undangan' => $request->tamu_undangan,
        ]);
       

        return redirect()->back()->with('success', 'Link undangan berhasil disimpan.');
    }
}
