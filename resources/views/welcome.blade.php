<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List-Me App - Get Organized</title>

    @vite(['resources/css/pages/welcome.css', 'resources/js/app.js'])

</head>

<body class="antialiased">

    <div class="landing-container">

       <div>
            <a href="/">
              <img src="{{ asset('images/logo.png') }}" class="w-40 h-40 mx-auto object-contain mb-6">
            </a>
        </div>

        <h1 class="text-5xl font-extrabold text-gray-800 mb-3">
            List-Me App
        </h1>

        <p class="text-lg text-gray-500 mb-10">
            Get it done. Get organized. Start listing now.
        </p>

        <div class="actions">
            <a href="{{ route('login') }}" class="btn-primary">
                Login
            </a>
            <a href="{{ route('register') }}" class="btn-secondary">
                Register
            </a>
        </div>

    </div>

    <script>
        window.addEventListener('load', function () {
            const body = document.body;
            // Beri jeda 1.5 detik agar animasi sempat terlihat
            setTimeout(function () {
                // Menambahkan kelas 'loaded' ke body untuk memicu CSS transisi
                body.classList.add('loaded');
            }, 1500);
        });
    </script>
</body>

</html>