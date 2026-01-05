<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
$title = "Tambah Alternatif - SPK PROMETHEE";

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">
    <div class="card">
      <h3 style="margin:0;">Tambah Alternatif</h3>
      <p class="muted" style="margin:6px 0 0;">Isi kode (misal A/B/C) dan nama lokasi.</p>

      <form method="POST" action="/spk-promethee/public/alternatives/store.php" style="margin-top:14px; display:grid; gap:12px; max-width:560px;">
        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Kode</label>
          <input name="code" required placeholder="A" style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;">
        </div>

        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Nama Lokasi</label>
          <input name="name" required placeholder="Lokasi A" style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;">
        </div>

        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Keterangan (opsional)</label>
          <textarea name="description" rows="3" placeholder="Catatan singkat..." style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;"></textarea>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a class="btn" href="/spk-promethee/public/alternatives/index.php">Kembali</a>
        </div>
      </form>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
