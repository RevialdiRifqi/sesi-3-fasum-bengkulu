# WebGIS Fasilitas Umum Kota Bengkulu

Studi kasus Sesi 3 (GIS & peluang karier): aplikasi web untuk memetakan sekolah, rumah sakit, dan kantor pemerintahan di Kota Bengkulu.

**Stack:** Laravel 11, MySQL, Leaflet, OpenStreetMap.

## 1. Studi Kasus

**Latar belakang.** Informasi lokasi fasilitas umum di Kota Bengkulu tersebar dan tidak terstruktur. Warga sulit menemukan lokasi dan kontak, sementara pengelola belum punya data berbasis lokasi.

**Rumusan masalah.** Bagaimana membangun sistem yang memudahkan warga mencari fasilitas umum dan membantu pengelola mengelola datanya?

**Kaitan dengan materi pemateri (Widi Primadepa).**

| Materi | Penerapan di proyek |
|---|---|
| GIS = analisis data berbasis lokasi, bukan sekadar peta | Pencarian fasilitas terdekat (rumus Haversine di SQL) |
| Penerapan di pemerintahan & aset infrastruktur | Data sekolah, RS, kantor pemerintahan sebagai aset publik |
| Teknologi: SQL, Python, QGIS, PostGIS | SQL spasial; ekspor GeoJSON yang bisa dibuka di QGIS |
| Portofolio & proyek nyata | Repositori GitHub dengan README dan seeder data |
| Komunikasi & bahasa Inggris | Dokumentasi README, kode berbahasa Inggris |

**Fitur.**
1. Peta interaktif dengan penanda per kategori
2. Filter kategori dan pencarian nama
3. Tombol "Terdekat dari saya" (geolokasi + jarak km)
4. Endpoint GeoJSON `/api/fasilitas` (bisa dibuka di QGIS)
5. Tambah fasilitas dengan klik peta

**Rancangan basis data** (`fasilitas`): id, nama, kategori (sekolah | rumah_sakit | pemerintahan), alamat, telepon, latitude, longitude, timestamps.

**Pengembangan lanjutan:** login admin, upload foto, migrasi ke PostgreSQL + PostGIS, analisis cakupan layanan (buffer), rute ke lokasi.

## 2. Cara Menjalankan

```bash
composer create-project laravel/laravel gis-fasum-bengkulu
cd gis-fasum-bengkulu
# salin isi folder ini (app, database, resources, routes) menimpa proyek Laravel
```
Atur `.env`:
```
DB_CONNECTION=mysql
DB_DATABASE=gis_fasum
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate --seed
php artisan serve
```
Buka http://127.0.0.1:8000

> Koordinat data contoh bersifat perkiraan. Verifikasi di OpenStreetMap sebelum dipakai.

## 3. Upload ke GitHub
```bash
git init
git add .
git commit -m "feat: WebGIS fasilitas umum Kota Bengkulu"
git branch -M main
git remote add origin https://github.com/USERNAME/gis-fasum-bengkulu.git
git push -u origin main
```
