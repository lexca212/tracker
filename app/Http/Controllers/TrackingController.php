<?php

namespace App\Http\Controllers;

use App\Models\LocationUpdate;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function index()
    {
        $trackingLinks = TrackingLink::with('latestUpdate')
            ->orderByDesc('created_at')
            ->get();

        return view('panel', compact('trackingLinks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $token = $this->generateToken();

        $trackingLink = TrackingLink::create([
            'token' => $token,
            'name' => $request->name,
        ]);

        return Redirect::route('share.panel')
            ->with('success', 'Tautan berbagi lokasi berhasil dibuat.')
            ->with('new_link', url('/share/' . $trackingLink->token));
    }

    public function show(string $token)
    {
        $trackingLink = TrackingLink::where('token', $token)->firstOrFail();

        return view('track', compact('trackingLink'));
    }

    public function update(Request $request, string $token)
    {
        $trackingLink = TrackingLink::where('token', $token)->firstOrFail();

        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'device' => ['nullable', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string'],
            'operator' => ['nullable', 'string', 'max:255'],
        ]);

        $locationUpdate = LocationUpdate::create([
            'tracking_link_id' => $trackingLink->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'device' => $data['device'] ?? 'Unknown device',
            'user_agent' => $data['user_agent'] ?? $request->userAgent(),
            'ip_address' => $request->ip(),
            'operator' => $data['operator'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lokasi berhasil dikirim.',
            'latitude' => $locationUpdate->latitude,
            'longitude' => $locationUpdate->longitude,
            'map_url' => 'https://maps.google.com/?q=' . $locationUpdate->latitude . ',' . $locationUpdate->longitude,
            'device' => $locationUpdate->device,
            'operator' => $locationUpdate->operator,
            'user_agent' => $locationUpdate->user_agent,
            'ip_address' => $locationUpdate->ip_address,
            'sent_at' => $locationUpdate->created_at->toDateTimeString(),
        ]);
    }

    public function destroy(string $token)
    {
        $trackingLink = TrackingLink::where('token', $token)->first();

        if (! $trackingLink) {
            return Redirect::route('share.panel')->with('error', 'Tautan tidak ditemukan atau sudah dihapus.');
        }

        $trackingLink->delete();

        return Redirect::route('share.panel')->with('success', 'Tautan berbagi telah dihapus.');
    }

    protected function generateToken(): string
    {
        do {
            $token = Str::lower(Str::random(20));
        } while (TrackingLink::where('token', $token)->exists());

        return $token;
    }
}
