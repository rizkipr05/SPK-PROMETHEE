# SPK PROMETHEE

Aplikasi Sistem Pendukung Keputusan (SPK) berbasis metode PROMETHEE untuk melakukan perankingan alternatif berdasarkan kriteria dan bobot.

## Tech Stack
- PHP 8.x (native, procedural)
- MySQL/MariaDB
- HTML, CSS, JavaScript (vanilla)

## Tech Stack Icons (Inline SVG)
<div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
  <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid #e5e7eb; border-radius:10px;">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M3 12c2-4 6-6 9-6s7 2 9 6c-2 4-6 6-9 6s-7-2-9-6z"></path>
      <circle cx="12" cy="12" r="2.5"></circle>
    </svg>
    <strong>PHP</strong>
  </div>
  <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid #e5e7eb; border-radius:10px;">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M4 8h16"></path>
      <path d="M6 8v8a4 4 0 0 0 8 0V8"></path>
      <path d="M10 6h4"></path>
    </svg>
    <strong>MySQL</strong>
  </div>
  <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid #e5e7eb; border-radius:10px;">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M3 7h18"></path>
      <path d="M3 12h18"></path>
      <path d="M3 17h18"></path>
      <circle cx="7" cy="7" r="1.5"></circle>
      <circle cx="12" cy="12" r="1.5"></circle>
      <circle cx="17" cy="17" r="1.5"></circle>
    </svg>
    <strong>HTML/CSS/JS</strong>
  </div>
</div>

## Fitur Utama
- Manajemen alternatif, kriteria, dan bobot.
- Input nilai evaluasi alternatif × kriteria.
- Perhitungan PROMETHEE (Leaving Flow, Entering Flow, Net Flow, Ranking).
- Detail langkah perhitungan (range, matriks preferensi, flow).
- Cetak laporan (via fitur print browser).
- Dashboard ringkasan data dan grafik sederhana.
- Autentikasi sederhana (login/logout).

## Struktur Proyek
```
app/
  auth/             # Autentikasi (login, cek sesi)
  config/           # Konfigurasi database
  helpers/          # Helper flash message, dll.
layouts/
  header.php        # Layout header
  navbar.php        # Topbar
  sidebar.php       # Sidebar navigasi
  footer.php        # Footer + script
  icons.php         # SVG icon helper
public/
  assets/
    css/            # Styles
    js/             # Script (print)
  alternatives/     # CRUD alternatif
  criteria/         # CRUD kriteria
  weights/          # Input bobot
  evaluations/      # Input nilai
  promethee/        # Perhitungan & hasil
  dashboard.php     # Dashboard
  login.php         # Login
  logout.php        # Logout
```

## Alur Penggunaan
1. Login ke sistem.
2. Tambahkan data Alternatif.
3. Tambahkan data Kriteria.
4. Atur Bobot kriteria (total harus 1.00).
5. Input Nilai alternatif × kriteria.
6. Jalankan Hitung PROMETHEE untuk mendapatkan ranking.
7. Lihat Hasil dan Detail perhitungan.
8. Cetak laporan jika diperlukan.

## Halaman Utama
- Dashboard: `public/dashboard.php`
- Alternatif: `public/alternatives/index.php`
- Kriteria: `public/criteria/index.php`
- Bobot: `public/weights/index.php`
- Nilai: `public/evaluations/index.php`
- Hitung PROMETHEE: `public/promethee/calculate.php`
- Hasil: `public/promethee/result.php`
- Detail: `public/promethee/detail.php`

## Instalasi Lokal (XAMPP)
1. Pindahkan folder project ke: `/opt/lampp/htdocs/spk-promethee`
2. Buat database: `spk_promethee`
3. Import struktur tabel sesuai kebutuhan (alternatives, criteria, weights, evaluations, results, result_runs, users).
4. Atur koneksi database di: `app/config/database.php`
5. Buka di browser: `http://localhost:8080/spk-promethee/public/login.php`

## Autentikasi
- User disimpan di tabel `users` dengan kolom minimal: `id`, `name`, `username`, `password_hash`, `role`.
- Password menggunakan `password_hash()` bawaan PHP.

## Catatan Perhitungan
- Total bobot harus = 1.00.
- Semua nilai evaluasi harus terisi sebelum perhitungan.

## Lisensi
Gunakan sesuai kebutuhan proyek.
