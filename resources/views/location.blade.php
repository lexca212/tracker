<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Lokasi BTS</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f7f7fc; color: #1f2937; }
        .container { max-width: 720px; margin: 2rem auto; padding: 1.5rem; background: #fff; border-radius: 18px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08); }
        .heading { margin-bottom: 1rem; }
        .heading h1 { margin: 0; font-size: 2rem; }
        .card { padding: 1.25rem; border: 1px solid #e5e7eb; border-radius: 1rem; margin-bottom: 1rem; background: #f9fafb; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input[type=text], button { width: 100%; padding: 0.85rem 1rem; border-radius: 0.85rem; border: 1px solid #d1d5db; font-size: 1rem; }
        button { background: #2563eb; color: white; border: none; cursor: pointer; transition: transform 0.2s ease, background 0.2s ease; }
        button:hover { transform: translateY(-1px); background: #1d4ed8; }
        .result { margin-top: 1rem; padding: 1rem; border-radius: 1rem; background: #eef2ff; border: 1px solid #c7d2fe; }
        .alert { padding: 0.85rem 1rem; background: #f8d7da; color: #842029; border-radius: 0.75rem; margin-top: 1rem; }
        .success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .actions { display: grid; gap: 1rem; margin-top: 1rem; }
        .link-button { display: inline-flex; justify-content: center; align-items: center; padding: 0.85rem 1rem; background: #10b981; color: white; border-radius: 0.85rem; border: none; text-decoration: none; }
        .link-button:hover { background: #0f766e; }
        .small { font-size: 0.95rem; color: #4b5563; }
    </style>
</head>
<body>
    <div class="container">
        <div class="heading">
            <h1>Cek Lokasi dengan BTS</h1>
            <p class="small">Masukkan nomor telepon untuk memperkirakan area berdasarkan data BTS, atau gunakan tombol kirim lokasi untuk mengirim koordinat.</p>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('location.lookup') }}">
                @csrf
                <label for="phone">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" placeholder="Contoh: 081234567890" value="{{ old('phone') }}" required>
                @error('phone')
                    <div class="alert">{{ $message }}</div>
                @enderror
                <button type="submit">Cek Lokasi</button>
            </form>

            <div class="actions">
                <button id="sendLocationButton" type="button" class="link-button">Kirim Lokasi Saya</button>
                <a href="javascript:void(0)" id="shareLink" class="link-button">Klik kirim lokasi</a>
            </div>
        </div>

        @if ($result)
            <div class="result {{ $result['status'] === 'success' ? 'success' : '' }}">
                <strong>{{ $result['status'] === 'success' ? 'Hasil:' : 'Pemberitahuan:' }}</strong>
                <p>{{ $result['message'] }}</p>
                <p><strong>Nomor:</strong> {{ $result['phone'] ?? '-' }}</p>

                @if ($result['status'] === 'success')
                    <p><strong>Operator:</strong> {{ $result['operator'] }}</p>
                    <p><strong>BTS:</strong> {{ $result['bts'] }}</p>
                    <p><strong>Area:</strong> {{ $result['area'] }}</p>
                @endif
            </div>
        @endif

        <div id="locationResponse" class="result" style="display:none;"></div>
    </div>

    <script>
        const sendLocationButton = document.getElementById('sendLocationButton');
        const shareLink = document.getElementById('shareLink');
        const locationResponse = document.getElementById('locationResponse');

        function handleLocationResponse(data) {
            locationResponse.style.display = 'block';
            locationResponse.innerHTML = `
                <strong>Lokasi terkirim:</strong>
                <p>${data.message}</p>
                <p><strong>Area:</strong> ${data.area}</p>
                <p><strong>Latitude:</strong> ${data.latitude}</p>
                <p><strong>Longitude:</strong> ${data.longitude}</p>
            `;
        }

        function sendGeoLocation(position) {
            fetch('{{ route('location.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                })
            })
            .then(response => response.json())
            .then(data => handleLocationResponse(data))
            .catch(() => {
                locationResponse.style.display = 'block';
                locationResponse.innerHTML = '<strong>Gagal:</strong> Tidak dapat mengirim lokasi. Pastikan browser mendukung geolokasi dan coba lagi.';
            });
        }

        sendLocationButton.addEventListener('click', () => {
            if (!navigator.geolocation) {
                locationResponse.style.display = 'block';
                locationResponse.innerHTML = '<strong>Gagal:</strong> Geolokasi tidak didukung di browser ini.';
                return;
            }
            navigator.geolocation.getCurrentPosition(sendGeoLocation, () => {
                locationResponse.style.display = 'block';
                locationResponse.innerHTML = '<strong>Gagal:</strong> Pengguna menolak akses lokasi atau lokasi tidak tersedia.';
            });
        });

        shareLink.addEventListener('click', (event) => {
            event.preventDefault();
            sendLocationButton.click();
        });
    </script>
</body>
</html>
