@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    
    @php
        $colorCompleted = 'text-green-600';
        $colorPending = 'text-blue-600';
        $colorMissed = 'text-red-600';
    @endphp

    <div class="relative min-h-screen bg-pink-pucat flex items-center justify-center p-6">
        
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ================================================= --}}
            {{-- KOLOM KIRI (70% - Tugas Utama, Search) --}}
            {{-- ================================================= --}}
            <div class="lg:col-span-2 p-6 lg:p-8 bg-white shadow-2xl rounded-xl">

                {{-- Header (Tombol Tambah Tugas Dihapus dari Sini) --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-gray-700 text-lg font-semibold">
                            Halo, {{ Auth::user()->name }} 👋
                        </h2>
                        <h1 class="text-2xl font-bold text-custom-brown mt-1">
                            To-Do List
                        </h1>
                    </div>
                    {{-- Tombol Tambah Tugas Dihapus dari sini --}}
                </div>
                
                {{-- Form Filter dan Pencarian --}}
                <form action="{{ route('todos.index') }}" method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
                    
                    <div class="flex-grow">
                        <input type="search" 
                                name="search" 
                                placeholder="Cari tugas berdasarkan judul..." 
                                value="{{ request('search') }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                    </div>
                    
                    <button type="submit" 
                        class="px-4 py-2 bg-custom-purple text-white font-semibold rounded-lg hover:bg-custom-purple-dark transition w-full sm:w-auto">
                        Cari
                    </button>
                    
                    @if (request('search') || request('category'))
                        <a href="{{ route('todos.index') }}" 
                           class="px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition flex items-center justify-center w-full sm:w-auto">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- Daftar Tugas --}}
                <div class="space-y-4 max-h-[70vh] overflow-y-auto p-1">
                    @forelse ($todos->items() as $todo)
                        <div
                            class="bg-white rounded-xl p-4 shadow-md border border-gray-100 hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                
                                {{-- Judul dan Detail --}}
                                <div>
                                    {{-- Tanda Lingkaran --}}
                                    <span class="inline-block w-3 h-3 rounded-full me-2 
                                        @if($todo->status === 'completed') bg-green-500 
                                        @elseif($todo->status === 'pending') bg-blue-500
                                        @else bg-red-500 @endif">
                                    </span>
                                    
                                    <h4 class="inline font-semibold text-custom-brown text-lg">{{ $todo->title }}</h4>
                                    
                                    <p class="text-sm text-gray-600 mt-1 ml-5">
                                        <span class="font-medium text-custom-purple">{{ $todo->category->name ?? 'Tanpa Kategori' }}</span>
                                        • Deadline: {{ $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('d M Y H:i') : 'Tanpa Deadline' }}
                                    </p>
                                    <p class="text-xs text-gray-500 ml-5 mt-1">
                                        Status: <span class="capitalize font-semibold
                                        @if($todo->status === 'completed') text-green-600 
                                        @elseif($todo->status === 'pending') text-blue-600
                                        @else text-red-500 @endif">{{ $todo->status }}</span>
                                        
                                        {{-- Tambahkan keterangan jika status missed --}}
                                        @if($todo->status === 'missed')
                                            <span class="text-xs text-red-500">(Terlewat)</span>
                                        @endif
                                    </p>
                                </div>
                                
                                {{-- Tombol Aksi --}}
                                <div class="flex items-center gap-2">
                                    <button
                                        onclick="openEditModal({{ $todo->id }}, '{{ addslashes($todo->title) }}', '{{ $todo->category_id }}', '{{ $todo->status }}', '{{ $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('Y-m-d\TH:i') : '' }}', '{{ addslashes($todo->description) }}')"
                                        class="text-gray-500 hover:text-custom-purple p-1 rounded transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-9l5 5m-9-5l5 5m-9-5V7a2 2 0 012-2h4"></path></svg>
                                    </button>
                                    <form action="{{ route('todos.destroy', $todo->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-gray-500 hover:text-red-500 p-1 rounded transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 p-3 text-center bg-custom-cream/80 rounded-lg">Belum ada tugas, yuk tambahkan!</p>
                    @endforelse
                </div>
                
                {{-- Link Pagination --}}
                <div class="mt-4 flex justify-center p-2 border-t border-gray-300 pt-4">
                    {{ $todos->appends(['search' => request('search'), 'category' => request('category')])->links() }}
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- KOLOM KANAN (30% - Status dan Completed Tasks) --}}
            {{-- ================================================= --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- A. Task Status (Visualisasi Progress) --}}
                <div class="p-6 bg-white shadow-2xl rounded-xl">
                    <h2 class="text-xl font-semibold mb-4 text-custom-brown flex items-center gap-2">
                        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v14m-9 4V6l12-3"></path></svg>
                        Task Status (Total: {{ $totalTasks }})
                    </h2>
                    
                    @if ($totalTasks > 0)
                        {{-- Donut Charts (Simulasi dengan teks dan warna) --}}
                        <div class="flex justify-around text-center mb-6">
                            
                            {{-- Completed --}}
                            <div>
                                <div class="w-20 h-20 rounded-full inline-flex items-center justify-center border-4 border-green-600/50" 
                                    style="background: conic-gradient(rgb(52, 211, 153) {{ $progressPercentage }}%, rgb(229, 231, 235) {{ $progressPercentage }}%);">
                                    <span class="text-lg font-bold text-green-600">{{ $progressPercentage }}%</span>
                                </div>
                                <p class="text-sm mt-2 font-medium text-gray-700">Completed ({{ $completedTasks }})</p>
                            </div>
                            
                            {{-- Pending --}}
                            <div>
                                <div class="w-20 h-20 rounded-full inline-flex items-center justify-center border-4 border-blue-600/50"
                                    style="background: conic-gradient(rgb(59, 130, 246) {{ $pendingPercentage }}%, rgb(229, 231, 235) {{ $pendingPercentage }}%);">
                                    <span class="text-lg font-bold text-blue-600">{{ $pendingPercentage }}%</span>
                                </div>
                                <p class="text-sm mt-2 font-medium text-gray-700">Pending ({{ $pendingTasks }})</p>
                            </div>
                            
                            {{-- Missed --}}
                            <div>
                                <div class="w-20 h-20 rounded-full inline-flex items-center justify-center border-4 border-red-600/50"
                                    style="background: conic-gradient(rgb(239, 68, 68) {{ $missedPercentage }}%, rgb(229, 231, 235) {{ $missedPercentage }}%);">
                                    <span class="text-lg font-bold text-red-600">{{ $missedPercentage }}%</span>
                                </div>
                                <p class="text-sm mt-2 font-medium text-gray-700">Missed ({{ $missedTasks }})</p>
                            </div>

                        </div>
                    
                        {{-- Keterangan --}}
                        <div class="flex flex-wrap justify-center gap-4 text-sm mt-4 text-gray-600">
                            <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-600 me-1"></span> Completed</span>
                            <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 me-1"></span> Pending</span>
                            <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-600 me-1"></span> Missed</span>
                        </div>
                    @else
                        <p class="text-center text-gray-500">Tambahkan tugas untuk melihat status progres!</p>
                    @endif
                </div>

                {{-- B. Completed Tasks (Tugas Selesai) --}}
                <div class="p-6 bg-white shadow-2xl rounded-xl">
                    <h2 class="text-xl font-semibold mb-4 text-custom-brown flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tugas Selesai Terakhir
                    </h2>
                    
                    @forelse ($recentCompleted as $completedTodo)
                        <div class="border-b border-gray-100 last:border-b-0 py-3">
                            <h4 class="font-semibold text-gray-800 flex items-center">
                                <span class="w-3 h-3 rounded-full bg-green-500 me-2"></span>
                                {{ $completedTodo->title }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1 ml-5">
                                Done {{ \Carbon\Carbon::parse($completedTodo->updated_at)->diffForHumans() }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1 ml-5">
                                Kategori: {{ $completedTodo->category->name ?? '-' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada tugas yang baru saja diselesaikan.</p>
                    @endforelse
                </div>
            </div>

        </div> 
        {{-- Tombol Tambah Mengambang (Warna Ungu) --}}
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="fixed bottom-8 right-8 bg-custom-purple text-white text-3xl rounded-full w-14 h-14 flex items-center justify-center shadow-lg hover:bg-custom-purple-dark transition transform hover:scale-110 z-40">
            +
        </button>
    </div>

    {{-- MODALS (Tambah & Edit) TETAP DIBAWAH --}}
    
    {{-- Modal Tambah Tugas Baru --}}
    <div id="addModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-custom-white rounded-2xl p-6 w-full max-w-lg shadow-2xl relative">
            
            <button onclick="document.getElementById('addModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <h2 class="text-xl font-semibold mb-6 text-custom-brown">Tambah Tugas Baru</h2>
            
            <form action="{{ route('todos.store') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Judul Tugas --}}
                <div>
                    <label for="title_add" class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Tugas
                    </label>
                    <input type="text" name="title" id="title_add" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category_id_add" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori
                    </label>
                    <select name="category_id" id="category_id_add" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('categories.index') }}"
                        class="text-xs text-custom-purple hover:underline mt-1 inline-block">
                        + Kelola Kategori
                    </a>
                </div>

                {{-- Deadline --}}
                <div>
                    <label for="deadline_add" class="block text-sm font-medium text-gray-700 mb-1">
                        Deadline (Opsional)
                    </label>
                    <input type="datetime-local" name="deadline" id="deadline_add"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                </div>
                
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded-lg text-gray-800 font-semibold hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-custom-purple text-white font-semibold rounded-lg hover:bg-custom-purple-dark transition">
                        Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Tugas --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-custom-white rounded-2xl p-6 w-full max-w-lg shadow-2xl relative">
            
            <button onclick="document.getElementById('editModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <h2 class="text-xl font-semibold mb-6 text-custom-brown">Edit Tugas</h2>
            
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                    <input type="text" id="editTitle" name="title" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                </div>
                
                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="editCategory_id" name="category_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="editStatus" name="status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        {{-- Opsi Missed tetap ada, tapi disarankan status Missed di-set otomatis oleh controller --}}
                        <option value="missed">Missed</option> 
                    </select>
                </div>
                
                {{-- Deadline --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                    <input type="datetime-local" id="editDeadline" name="deadline"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus-custom-purple">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded-lg text-gray-800 font-semibold">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-custom-pink text-white font-semibold rounded-lg hover:bg-[#d69593] transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script JavaScript untuk Modal --}}
    <script>
        /**
         * Membuka modal edit dengan mengisi data todo yang dipilih.
         */
        function openEditModal(id, title, categoryId, status, deadline, description) {
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');

            // 1. Atur action form
            document.getElementById('editForm').action = `{{ url('todos') }}/${id}`;
            
            // 2. Isi nilai input Judul
            document.getElementById('editTitle').value = title;
            
            // 3. Isi nilai Deskripsi
            document.getElementById('editDescription').value = description;
            
            // 4. Isi nilai Status (Dropdown)
            document.getElementById('editStatus').value = status;
            
            // 5. Isi nilai Kategori (Dropdown)
            document.getElementById('editCategory_id').value = categoryId;
            
            // 6. Isi Deadline
            document.getElementById('editDeadline').value = deadline;
        }
    </script>
@endsection