<h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-gray-700 dark:text-gray-200">
    Hubungkan Telegram 🤖
</h2>

@if (Auth::user()->telegram_chat_id)
    {{-- Status: Sudah Terhubung --}}
    <div class="bg-green-500 text-white p-4 rounded-lg flex justify-between items-center mb-4">
        <span>Anda sudah terhubung dengan Telegram. Notifikasi akan dikirimkan ke Chat ID: {{ Auth::user()->telegram_chat_id }}</span>
        
        {{-- Tombol untuk memutuskan koneksi (Disconnect) --}}
        <form method="POST" action="{{ route('telegram.disconnect') }}" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi Telegram?');">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                Putuskan Koneksi
            </button>
        </form>
    </div>
    
@else
    {{-- Status: Belum Terhubung --}}
    <p class="mb-4 text-gray-600 dark:text-gray-300">
        Hubungkan akun Telegram Anda untuk menerima notifikasi tugas, pengingat, dan pembaruan penting lainnya secara instan.
    </p>

    <div class="p-4 bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 rounded-md mb-6 text-gray-800 dark:text-gray-200">
        <p class="font-medium mb-2">Langkah-langkah:</p>
        <ol class="list-decimal list-inside space-y-1 text-sm">
            <li>Cari bot kami di Telegram: <b>@AppListMe_bot</b>.</li>
            <li>Kirim pesan <b>`/start`</b> ke bot tersebut.</li>
            <li>Setelah Anda mengirim `/start`, bot akan merespons dengan <b>Chat ID unik</b> Anda.</li>
            <li>Masukkan Chat ID tersebut di kolom di bawah ini dan klik Hubungkan.</li>
        </ol>
    </div>

    {{-- Form untuk memasukkan Chat ID --}}
    <form method="POST" action="{{ route('telegram.connect') }}">
        @csrf
        <div class="flex space-x-3">
            <input type="text" name="chat_id" placeholder="Masukkan Chat ID yang Anda dapatkan dari bot"
                   class="flex-grow border rounded p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 text-gray-800 dark:text-gray-100" required>
            
            <button class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-150">
                Hubungkan
            </button>
        </div>
        @error('chat_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </form>
@endif