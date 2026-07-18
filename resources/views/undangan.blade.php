<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Pernikahan</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.95), rgba(239, 232, 223, 0.95)),
                linear-gradient(180deg, #fef8f1 0%, #e9dfd4 100%);
            color: #2c2b2a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            width: min(520px, 100%);
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(44, 43, 42, 0.08);
            border-radius: 28px;
            box-shadow: 0 24px 50px rgba(81, 72, 64, 0.1);
            padding: 2rem 2rem 2.5rem;
        }
        .card p {
            margin: 0 0 1.35rem;
            line-height: 1.75;
            font-size: 1rem;
        }
        .card p:first-of-type {
            font-weight: 600;
            font-size: 1.05rem;
        }
        .actions {
            text-align: center;
            margin-top: 1.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.9rem 1.75rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            text-align: center;
            transition: transform 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-primary {
            background: #027281ff;
            color: #fff;
            border: 1px solid rgba(2, 114, 129, 0.9);
            box-shadow: 0 14px 30px rgba(2, 114, 129, 0.15);
        }
    </style>
</head>
<body>
    <div class="card">
        <p>Selamat! {{$linkUndangan->tamu_undangan}} diundang untuk menghadiri acara pernikahan kami.</p>
        <p>Silahkan klik buka dan ijinkan untuk membuka detail undangan di bawah ini.</p>
        <div class="actions">
            <a href="{{ $linkUndangan->link_undangan }}" class="btn btn-primary" target="_blank">BUKA dan Ijinkan</a>
        </div>
    </div>
</body>
</html>