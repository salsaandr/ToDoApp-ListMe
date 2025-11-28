@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
    <div class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        @if (session('status'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('status') }}
            </div>
        @endif

        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4 text-center">Kelola Kategori</h1>

        <form action="{{ route('categories.store') }}" method="POST" class="flex gap-2 mb-6">
            @csrf
            <input type="text" name="name" placeholder="Nama kategori baru"
                class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-ungu-kustom focus:border-ungu-kustom dark:bg-gray-700 dark:text-gray-200">
            <button type="submit"
                class="px-4 py-2 bg-ungu-kustom text-white font-semibold rounded-lg hover:bg-[#794D6A] transition">
                Tambah
            </button>
        </form>

        <table class="w-full text-sm text-gray-700 dark:text-gray-300">
            <thead>
                <tr class="border-b border-gray-300 dark:border-gray-700">
                    <th class="py-2 text-left">Nama Kategori</th>
                    <th class="py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="py-2">{{ $category->name }}</td>
                        <td class="text-center">
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection