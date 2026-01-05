<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/evaluations/index.php");
  exit;
}

$values = $_POST["values"] ?? null;
if (!is_array($values) || count($values) === 0) {
  $_SESSION["_flash"]["error"] = "Tidak ada nilai yang dikirim.";
  header("Location: /spk-promethee/public/evaluations/index.php");
  exit;
}

try {
  $pdo->beginTransaction();

  // Pastikan alternatif & kriteria yang dikirim memang ada (untuk keamanan)
  $altIds = array_map(fn($r) => (int)$r["id"], $pdo->query("SELECT id FROM alternatives")->fetchAll());
  $critIds = array_map(fn($r) => (int)$r["id"], $pdo->query("SELECT id FROM criteria")->fetchAll());

  $altSet = array_flip($altIds);
  $critSet = array_flip($critIds);

  // Upsert manual: insert jika belum ada, update jika ada
  $check = $pdo->prepare("SELECT id FROM evaluations WHERE alternative_id = ? AND criteria_id = ? LIMIT 1");
  $ins   = $pdo->prepare("INSERT INTO evaluations (alternative_id, criteria_id, value) VALUES (?, ?, ?)");
  $upd   = $pdo->prepare("UPDATE evaluations SET value = ? WHERE alternative_id = ? AND criteria_id = ?");

  $countSaved = 0;

  foreach ($values as $aid => $critMap) {
    $aid = (int)$aid;
    if ($aid <= 0 || !isset($altSet[$aid])) continue;
    if (!is_array($critMap)) continue;

    foreach ($critMap as $cid => $val) {
      $cid = (int)$cid;
      if ($cid <= 0 || !isset($critSet[$cid])) continue;

      // nilai wajib angka
      if ($val === "" || $val === null || !is_numeric($val)) {
        $pdo->rollBack();
        $_SESSION["_flash"]["error"] = "Semua nilai wajib diisi angka.";
        header("Location: /spk-promethee/public/evaluations/index.php");
        exit;
      }

      $num = (float)$val;

      $check->execute([$aid, $cid]);
      $exists = $check->fetch();

      if ($exists) {
        $upd->execute([$num, $aid, $cid]);
      } else {
        $ins->execute([$aid, $cid, $num]);
      }
      $countSaved++;
    }
  }

  $pdo->commit();
  $_SESSION["_flash"]["success"] = "Berhasil menyimpan $countSaved nilai evaluasi.";
} catch (Throwable $e) {
  $pdo->rollBack();
  $_SESSION["_flash"]["error"] = "Gagal menyimpan nilai. " . $e->getMessage();
}

header("Location: /spk-promethee/public/evaluations/index.php");
exit;
