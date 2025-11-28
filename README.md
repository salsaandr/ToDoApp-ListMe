#ListMe – To-Do List Web App

ListMe adalah aplikasi manajemen tugas harian berbasis Laravel yang membantu pengguna mencatat, mengelola, dan memantau progres tugas. Aplikasi ini dilengkapi dengan kategori tugas, progress bar, serta light/dark mode yang disimpan di database sesuai preferensi pengguna.

##Fitur Utama
1. Autentikasi (Register & Login)
User dapat membuat akun. Dengan memasukkan nama email dan password user akan memiliki sebuah akun yang bisa digunakan untuk login mengakses web.

2. Dashboard
- Menampilkan semua tugas user dalam satu halaman.
- Filter tugas berdasarkan kategori atau status.
- Menampilkan jumlah tugas selesai dengan progress bar.
- Dapat mengganti tema tampilan kapan saja.

3. Manajemen Tugas (CRUD To-Do)

- Tambah, edit, dan hapus tugas.
- Set deadline dan status tugas: Pending, Finished, Missed.

4. Kategori Tugas

- User bisa membuat kategori seperti: Organisasi, PR, Kuliah, Kerja, dll.
- Tugas dapat dikelompokkan sesuai kategori.

5. Progress Bar
- Menghitung persentase tugas selesai perkategori dari total tugas.
- Contoh: 7 selesai dari 10 tugas → 70%.

6. Tugas Seminggu ke Depan
- Menampilkan tugas yang deadline-nya dalam 7 hari mendatang.
- Membantu user fokus pada tugas yang lebih mendesak.

##Teknologi yang Digunakan

- Laravel 11 – Backend framework
- MySQL – Database
- Blade Template – Frontend
- Tailwind CSS / Bootstrap – Styling UI
- Laravel Breeze – Autentikasi
- Eloquent ORM – Manajemen data
- Git & GitHub – Version control
