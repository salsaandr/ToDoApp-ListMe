<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'List-Me App') }}</title>
    @vite(['resources/css/pages/auth/login.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased" style="background-color: #eda3a1;">

    <div class="login-card-wrapper">
        <div>
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 object-contain mx-auto">
            </a>
        </div>

        <div class="sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg login-card mx-auto">
            {{ $slot }}
        </div>
    </div>

    <div id="auth-loading-screen" style="display: none;">
        <div class="ball"></div>
        <div class="shadow"></div>
        <div class="loading-bar">Processing...</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[method="POST"]');
            const loadingScreen = document.getElementById('auth-loading-screen');

            if (loadingScreen && forms.length > 0) {
                forms.forEach(form => {
                    form.addEventListener('submit', function () {
                        loadingScreen.style.display = 'flex';
                        const buttons = form.querySelectorAll('button[type="submit"], .btn-login');
                        buttons.forEach(btn => {
                            btn.disabled = true;
                            btn.textContent = 'Processing...';
                        });
                    });
                });
            }
        });
    </script>
</body>