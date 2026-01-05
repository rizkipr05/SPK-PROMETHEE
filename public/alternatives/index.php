<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

$title = "Alternatif (Lokasi) - SPK PROMETHEE";

$rows = $pdo->query("SELECT id, code, name, description FROM alternatives ORDER BY id DESC")->fetchAll();

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">
    <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
      <div>
        <h3 style="margin:0;">Alternatif (Lokasi)</h3>
        <p class="muted" style="margin:6px 0 0;">Kelola daftar lokasi A/B/C yang akan diranking.</p>
      </div>
      <a class="btn btn-primary" href="/spk-promethee/public/alternatives/create.php">+ Tambah Alternatif</a>
    </div>

    <div class="card" style="margin-top:14px; overflow:auto;">
      <table style="width:100%; border-collapse:collapse; min-width:720px;">
        <thead>
          <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
            <th style="padding:10px;">Kode</th>
            <th style="padding:10px;">Nama Lokasi</th>
            <th style="padding:10px;">Keterangan</th>
            <th style="padding:10px; width:170px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr>
              <td colspan="4" style="padding:14px;" class="muted">Belum ada data alternatif.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px; font-weight:800;"><?= htmlspecialchars($r["code"]) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($r["name"]) ?></td>
                <td style="padding:10px;" class="muted"><?= htmlspecialchars($r["description"] ?? "-") ?></td>
                <td style="padding:10px; display:flex; gap:8px;">
                  <a class="btn" href="/spk-promethee/public/alternatives/edit.php?id=<?= (int)$r["id"] ?>">Edit</a>

                  <form method="POST" action="/spk-promethee/public/alternatives/delete.php" onsubmit="return confirm('Hapus alternatif ini?');">
                    <input type="hidden" name="id" value="<?= (int)$r["id"] ?>">
                    <button class="btn" type="submit" style="border-color:#fecaca;">Hapus</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
