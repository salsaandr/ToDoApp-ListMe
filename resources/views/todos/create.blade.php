@extends('layouts.app')

@section('title', 'Tambah Tugas Baru - List Me')

@section('content')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4 text-center">Tambah Tugas Baru</h1>

                {{-- Form untuk membuat tugas baru --}}
                <form action="{{ route('todos.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    {{-- Judul Tugas --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" required
                            value="{{ old('title') }}"
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-custom-purple focus:ring-custom-purple dark:bg-gray-700 dark:text-gray-200 @error('title') border-red-500 @enderror">
                        
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        {{-- Catatan: Variabel $categories harus dikirim dari TodoController@create --}}
                        <select name="category_id" id="category_id" required
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-custom-purple focus:ring-custom-purple dark:bg-gray-700 dark:text-gray-200 @error('category_id') border-red-500 @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            
                            {{-- Memastikan $categories ada dan iterable --}}
                            @foreach (isset($categories) && is_iterable($categories) ? $categories : [] as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Link ke Kelola Kategori --}}
                        <a href="{{ route('categories.index') }}"
                            class="text-xs text-custom-purple hover:underline mt-2 inline-block dark:text-indigo-400">
                            + Kelola Kategori jika tidak ada pilihan
                        </a>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                            Deadline (Opsional)
                        </label>
                        <input type="datetime-local" name="deadline" id="deadline"
                            value="{{ old('deadline') }}"
                            class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-custom-purple focus:ring-custom-purple dark:bg-gray-700 dark:text-gray-200 @error('deadline') border-red-500 @enderror">
                        
                        @error('deadline')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-4">
                        {{-- Tombol Kembali --}}
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150">
                            Batal
                        </a>
                        
                        {{-- Tombol Simpan --}}
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-custom-purple border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-custom-purple-dark active:bg-custom-purple-dark focus:outline-none focus:border-custom-purple-dark focus:ring focus:ring-custom-purple/50 transition ease-in-out duration-150">
                            Simpan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection