<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT id, code, name, description FROM alternatives WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

$title = "Edit Alternatif - SPK PROMETHEE";

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">
    <div class="card">
      <h3 style="margin:0;">Edit Alternatif</h3>
      <p class="muted" style="margin:6px 0 0;">Perbarui data alternatif lokasi.</p>

      <form method="POST" action="/spk-promethee/public/alternatives/update.php" style="margin-top:14px; display:grid; gap:12px; max-width:560px;">
        <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">

        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Kode</label>
          <input name="code" required value="<?= htmlspecialchars($row["code"]) ?>" style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;">
        </div>

        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Nama Lokasi</label>
          <input name="name" required value="<?= htmlspecialchars($row["name"]) ?>" style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;">
        </div>

        <div>
          <label style="display:block; font-size:13px; margin-bottom:6px;">Keterangan (opsional)</label>
          <textarea name="description" rows="3" style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;"><?= htmlspecialchars($row["description"] ?? "") ?></textarea>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn btn-primary" type="submit">Update</button>
          <a class="btn" href="/spk-promethee/public/alternatives/index.php">Kembali</a>
        </div>
      </form>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
