<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected array $btsData = [
        '0811' => ['area' => 'Jakarta Selatan', 'bts' => 'BTS Jakarta Selatan', 'operator' => 'Telkomsel'],
        '0812' => ['area' => 'Bandung', 'bts' => 'BTS Bandung', 'operator' => 'Telkomsel'],
        '0813' => ['area' => 'Surabaya', 'bts' => 'BTS Surabaya', 'operator' => 'Telkomsel'],
        '0821' => ['area' => 'Bogor', 'bts' => 'BTS Bogor', 'operator' => 'Indosat'],
        '0822' => ['area' => 'Semarang', 'bts' => 'BTS Semarang', 'operator' => 'Indosat'],
        '0823' => ['area' => 'Medan', 'bts' => 'BTS Medan', 'operator' => 'Indosat'],
    ];

    public function index(Request $request)
    {
        return view('location', [
            'result' => $request->session()->get('result'),
        ]);
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'min:6'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $prefix = substr($phone, 0, 4);
        $result = $this->findBtsByPrefix($prefix);

        if ($result === null) {
            $result = [
                'status' => 'error',
                'message' => 'Nomor tidak ditemukan dalam koleksi BTS. Pastikan nomor valid dan gunakan awalan yang benar.',
                'phone' => $request->phone,
            ];
        } else {
            $result = array_merge($result, [
                'status' => 'success',
                'message' => 'Lokasi diperkirakan berdasar awalan nomor telepon dan data BTS.',
                'phone' => $request->phone,
            ]);
        }

        return redirect()->route('location.index')->with('result', $result);
    }

    public function sendLocation(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $area = $this->approximateArea((float) $data['latitude'], (float) $data['longitude']);

        return response()->json([
            'status' => 'success',
            'area' => $area,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'message' => 'Lokasi berhasil dikirim dan diperkirakan berdasarkan koordinat GPS.',
        ]);
    }

    protected function findBtsByPrefix(string $prefix): ?array
    {
        return $this->btsData[$prefix] ?? null;
    }

    protected function approximateArea(float $latitude, float $longitude): string
    {
        if ($latitude < -6.0 && $latitude > -7.0 && $longitude > 106.0 && $longitude < 107.0) {
            return 'Jakarta Raya (perkiraan dari koordinat)';
        }

        if ($latitude < -7.0 && $latitude > -7.9 && $longitude > 107.0 && $longitude < 108.0) {
            return 'Bandung / Jawa Barat (perkiraan dari koordinat)';
        }

        if ($latitude < -7.0 && $latitude > -8.0 && $longitude > 112.0 && $longitude < 113.0) {
            return 'Surabaya / Jawa Timur (perkiraan dari koordinat)';
        }

        if ($latitude > 1.0 && $latitude < 3.0 && $longitude > 98.0 && $longitude < 99.5) {
            return 'Medan / Sumatera Utara (perkiraan dari koordinat)';
        }

        return 'Area tidak teridentifikasi (koordinat di luar simulasi sederhana).';
    }
}
