<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PANEL LINK</title>
    <style>
        body { margin: 0; font-family: Inter, system-ui, sans-serif; background: #eef2ff; color: #0f172a; }
        .container { width: min(1120px, calc(100% - 2rem)); margin: 2rem auto; }
        .panel { background: #ffffff; border-radius: 24px; box-shadow: 0 28px 80px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .header { padding: 2rem; border-bottom: 1px solid #e2e8f0; }
        .header h1 { margin: 0 0 0.5rem; font-size: clamp(2rem, 2.5vw, 2.5rem); }
        .header p { margin: 0; color: #64748b; line-height: 1.7; }
        .content { padding: 2rem; display: grid; gap: 1.5rem; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; }
        .form-row { display: grid; gap: 0.75rem; }
        label { font-weight: 700; color: #334155; }
        input[type=text] { width: 100%; padding: 0.95rem 1rem; border-radius: 1rem; border: 1px solid #cbd5e1; background: #ffffff; }
        button { padding: 0.95rem 1rem; border-radius: 1rem; border: none; color: #ffffff; background: #4338ca; cursor: pointer; font-weight: 700; transition: background 0.2s ease; }
        button:hover { background: #3730a3; }
        .alert { padding: 1rem 1.25rem; border-radius: 1rem; background: #d1fae5; color: #0f766e; border: 1px solid #a7f3d0; }
        .table { width: 100%; border-collapse: collapse; font-size:0.95rem; }
        .table th, .table td { padding: 0.75rem 0.5rem; text-align: left; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .table th { color: #475569; font-size: 0.95rem; font-weight: 700; }
        .table td { color: #334155; font-size: 0.95rem; }
        .actions { display:flex; gap:0.4rem; align-items:center; }
        .small-btn { padding:0.38rem 0.5rem; border-radius:8px; border:1px solid transparent; background:#eef2ff; color:#1e3a8a; cursor:pointer; font-weight:700; }
        .small-btn.danger { background:#fee2e2; border-color:#fecaca; color:#991b1b; }
        .small-btn.ghost { background:transparent; border-color:transparent; color:#2563eb; }
        /* Modal */
        .modal-backdrop { position:fixed; inset:0; background:rgba(2,6,23,0.5); display:none; align-items:center; justify-content:center; z-index:60; }
        .modal { background:#fff; border-radius:12px; padding:1rem; width:min(720px,calc(100% - 3rem)); box-shadow:0 24px 64px rgba(2,6,23,0.32); }
        .modal h3{ margin:0 0 0.5rem; }
        .modal-row{ display:flex; gap:0.75rem; margin-bottom:0.5rem; }
        .modal-label{ min-width:110px; color:#475569; font-weight:700; }
        .modal-value{ color:#0f172a; word-break:break-word; }
        .badge { display: inline-flex; gap: 0.35rem; align-items: center; padding: 0.4rem 0.75rem; border-radius: 999px; background: #eef2ff; color: #1e3a8a; font-size: 0.85rem; }
        .link-button { color: #2563eb; text-decoration: none; font-weight: 700; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <div class="header">
                <h1>Panel Berbagi Lokasi</h1>
                {{-- <p>Buat tautan berbagi lokasi untuk teman. Ketika teman mengirim lokasi, data akan muncul di panel dengan latitude, longitude, Google Maps, dan perangkat yang digunakan.</p> --}}
                 <p>Buat tautan klik, shareloc parani antemi</p>
            </div>
            <div class="content">
                <div class="card">
                    <form method="POST" action="{{ route('share.create') }}">
                        @csrf
                        <div class="form-row">
                            <label for="name">Nama (opsional)</label>
                            <input id="name" name="name" type="text" placeholder="Contoh: Teman kantor atau keluarga" value="{{ old('name') }}">
                            @error('name')
                                <div class="alert" style="background:#fee2e2;border-color:#fecaca;color:#991b1b;">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit">Buat Tautan Berbagi</button>
                    </form>

                    @if (session('success'))
                        <div class="alert" style="margin-top:1rem;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('new_link'))
                        <div class="alert" style="margin-top:1rem;">
                            Tautan berbagi lokasi telah dibuat:
                            <div style="margin-top:0.5rem; display:flex; gap:0.5rem; align-items:center;">
                                <div id="new-link" style="word-break: break-all;">{{ session('new_link') }}</div>
                                <button id="copy-new-link" type="button" title="Salin tautan" style="background:#10b981;border:none;color:#fff;padding:0.45rem 0.6rem;border-radius:10px;cursor:pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card">
                    <h2 style="margin-top:0; margin-bottom:1rem; font-size:1.25rem;">Daftar Tautan Berbagi</h2>

                    @if ($trackingLinks->isEmpty())
                        <p class="muted">Belum ada tautan berbagi. Buat tautan baru untuk menerima lokasi dari teman.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tautan</th>
                                    <th>Status</th>
                                    <th>Lat / Long</th>
                                    <th>Perangkat</th>
                                    <th>Operator</th>
                                    <th>IP Publik</th>
                                    <th>User Agent</th>
                                    <th>Waktu</th>
                                    <th>Maps</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trackingLinks as $trackingLink)
                                    <tr>
                                        <td>{{ $trackingLink->name ?: 'Tanpa nama' }}</td>
                                        <td>
                                            <div class="actions">
                                                <a class="link-button" href="{{ url('/share/' . $trackingLink->token) }}" target="_blank">Buka</a>
                                                <button type="button" class="small-btn ghost detail-btn" title="Detil"
                                                    data-name="{{ $trackingLink->name ?: 'Tanpa nama' }}"
                                                    data-link="{{ url('/share/' . $trackingLink->token) }}"
                                                    data-lat="{{ $trackingLink->latestUpdate->latitude ?? '' }}"
                                                    data-lng="{{ $trackingLink->latestUpdate->longitude ?? '' }}"
                                                    data-device="{{ $trackingLink->latestUpdate->device ?? '' }}"
                                                    data-operator="{{ $trackingLink->latestUpdate->operator ?? '' }}"
                                                    data-ip="{{ $trackingLink->latestUpdate->ip_address ?? '' }}"
                                                    data-ua="{{ $trackingLink->latestUpdate->user_agent ?? '' }}"
                                                    data-time="{{ $trackingLink->latestUpdate ? $trackingLink->latestUpdate->created_at->format('d M Y H:i:s') : '' }}"
                                                >Detil</button>
                                                <button type="button" class="small-btn copy-link-btn" data-link="{{ url('/share/' . $trackingLink->token) }}" title="Salin tautan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                </button>
                                                <form method="POST" action="{{ route('share.destroy', ['token' => $trackingLink->token]) }}" style="display:inline;margin:0;padding:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Hapus tautan ini? Semua data lokasi terkait akan dihapus.')" title="Hapus tautan" class="small-btn danger" style="margin-left:4px;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($trackingLink->latestUpdate)
                                                <span class="badge">Diterima</span>
                                            @else
                                                <span class="badge" style="background:#f8fafc;color:#475569;">Belum ada pembaruan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trackingLink->latestUpdate)
                                                {{ $trackingLink->latestUpdate->latitude }}, {{ $trackingLink->latestUpdate->longitude }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trackingLink->latestUpdate)
                                                {{ $trackingLink->latestUpdate->device }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trackingLink->latestUpdate)
                                                {{ $trackingLink->latestUpdate->operator ?? 'Tidak diketahui' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="muted" style="max-width:170px; word-break:break-all;">
                                            @if ($trackingLink->latestUpdate)
                                                {{ $trackingLink->latestUpdate->ip_address ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="muted" style="max-width:280px; word-break:break-all;">
                                            @if ($trackingLink->latestUpdate)
                                                {{ \Illuminate\Support\Str::limit($trackingLink->latestUpdate->user_agent, 50) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="muted" style="white-space:nowrap;">
                                            @if ($trackingLink->latestUpdate)
                                                {{ $trackingLink->latestUpdate->created_at->format('d M Y H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($trackingLink->latestUpdate)
                                                <a class="link-button" href="https://maps.google.com/?q={{ $trackingLink->latestUpdate->latitude }},{{ $trackingLink->latestUpdate->longitude }}" target="_blank">Buka Maps</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Detail modal -->
    <div id="detail-backdrop" class="modal-backdrop" role="dialog" aria-hidden="true">
        <div class="modal" id="detail-modal">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                <h3>Detail Pembaruan</h3>
                <button id="detail-close" style="background:transparent;border:none;color:#64748b;cursor:pointer;font-weight:700;">Tutup</button>
            </div>
            <div class="modal-row"><div class="modal-label">Nama</div><div class="modal-value" id="detail-name">-</div></div>
            <div class="modal-row"><div class="modal-label">Koordinat</div><div class="modal-value" id="detail-coords">-</div></div>
            <div class="modal-row"><div class="modal-label">Perangkat</div><div class="modal-value" id="detail-device">-</div></div>
            <div class="modal-row"><div class="modal-label">Operator</div><div class="modal-value" id="detail-operator">-</div></div>
            <div class="modal-row"><div class="modal-label">IP</div><div class="modal-value" id="detail-ip">-</div></div>
            <div class="modal-row"><div class="modal-label">User Agent</div><div class="modal-value" id="detail-ua">-</div></div>
            <div class="modal-row"><div class="modal-label">Waktu</div><div class="modal-value" id="detail-time">-</div></div>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;justify-content:flex-end;">
                <a id="detail-maps-link" class="link-button" href="#" target="_blank">Buka di Maps</a>
            </div>
        </div>
    </div>

    <div id="copy-toast" style="position:fixed;bottom:24px;right:24px;background:#111827;color:#fff;padding:0.65rem 1rem;border-radius:8px;display:none;box-shadow:0 8px 24px rgba(0,0,0,0.12);">Tautan tersalin</div>
    <script>
        function showToast(message){
            const t = document.getElementById('copy-toast');
            t.textContent = message || 'Tautan tersalin';
            t.style.display = 'block';
            t.style.opacity = '1';
            setTimeout(()=>{ t.style.transition = 'opacity 300ms'; t.style.opacity = '0'; }, 1400);
            setTimeout(()=>{ t.style.display = 'none'; t.style.transition = ''; }, 1800);
        }

        function openDetailModal(data){
            document.getElementById('detail-name').textContent = data.name || '-';
            const coords = (data.lat && data.lng) ? (data.lat + ', ' + data.lng) : '-';
            document.getElementById('detail-coords').textContent = coords;
            document.getElementById('detail-device').textContent = data.device || '-';
            document.getElementById('detail-operator').textContent = data.operator || '-';
            document.getElementById('detail-ip').textContent = data.ip || '-';
            document.getElementById('detail-ua').textContent = data.ua || '-';
            document.getElementById('detail-time').textContent = data.time || '-';
            const mapsLink = document.getElementById('detail-maps-link');
            if (data.lat && data.lng) {
                mapsLink.href = 'https://maps.google.com/?q=' + encodeURIComponent(data.lat + ',' + data.lng);
                mapsLink.style.display = 'inline-block';
            } else {
                mapsLink.href = '#';
                mapsLink.style.display = 'none';
            }
            const back = document.getElementById('detail-backdrop');
            back.style.display = 'flex';
            back.setAttribute('aria-hidden','false');
        }

        function closeDetailModal(){
            const back = document.getElementById('detail-backdrop');
            back.style.display = 'none';
            back.setAttribute('aria-hidden','true');
        }

        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.copy-link-btn').forEach(btn => {
                btn.addEventListener('click', async function(){
                    const link = this.getAttribute('data-link');
                    try{
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(link);
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = link;
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            ta.remove();
                        }
                        showToast('Tautan disalin ke clipboard');
                    }catch(e){
                        showToast('Gagal menyalin tautan');
                    }
                });
            });

            const copyNew = document.getElementById('copy-new-link');
            if(copyNew){
                copyNew.addEventListener('click', async function(){
                    const text = document.getElementById('new-link')?.textContent || '';
                    try{
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(text);
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = text;
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            ta.remove();
                        }
                        showToast('Tautan disalin ke clipboard');
                    }catch(e){ showToast('Gagal menyalin tautan'); }
                });
            }

            // Detail modal handlers
            document.querySelectorAll('.detail-btn').forEach(btn => {
                btn.addEventListener('click', function(){
                    const data = {
                        name: this.getAttribute('data-name'),
                        link: this.getAttribute('data-link'),
                        lat: this.getAttribute('data-lat'),
                        lng: this.getAttribute('data-lng'),
                        device: this.getAttribute('data-device'),
                        operator: this.getAttribute('data-operator'),
                        ip: this.getAttribute('data-ip'),
                        ua: this.getAttribute('data-ua'),
                        time: this.getAttribute('data-time')
                    };
                    openDetailModal(data);
                });
            });

            document.getElementById('detail-close')?.addEventListener('click', closeDetailModal);
            document.getElementById('detail-backdrop')?.addEventListener('click', function(e){ if(e.target === this) closeDetailModal(); });
        });
    </script>
</body>
</html>
