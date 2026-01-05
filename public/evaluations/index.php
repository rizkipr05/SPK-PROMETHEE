<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/weights/index.php");
  exit;
}

$weights = $_POST["weights"] ?? null;
if (!is_array($weights) || count($weights) === 0) {
  $_SESSION["_flash"]["error"] = "Tidak ada bobot yang dikirim.";
  header("Location: /spk-promethee/public/weights/index.php");
  exit;
}

// validasi & hitung total
$total = 0.0;
$clean = [];

foreach ($weights as $criteria_id => $w) {
  $cid = (int)$criteria_id;
  $val = (float)$w;

  if ($cid <= 0) continue;

  if ($val < 0 || $val > 1) {
    $_SESSION["_flash"]["error"] = "Bobot harus di antara 0 sampai 1.";
    header("Location: /spk-promethee/public/weights/index.php");
    exit;
  }

  $clean[$cid] = $val;
  $total += $val;
}

// total harus 1.00 (toleransi)
$eps = 0.001; // toleransi
if (abs($total - 1.0) > $eps) {
  $_SESSION["_flash"]["error"] = "Total bobot harus = 1.00. Total kamu: " . number_format($total, 3);
  header("Location: /spk-promethee/public/weights/index.php");
  exit;
}

try {
  $pdo->beginTransaction();

  // upsert manual: insert jika belum ada, kalau ada update
  $check = $pdo->prepare("SELECT id FROM weights WHERE criteria_id = ? LIMIT 1");
  $ins   = $pdo->prepare("INSERT INTO weights (criteria_id, weight) VALUES (?, ?)");
  $upd   = $pdo->prepare("UPDATE weights SET weight = ? WHERE criteria_id = ?");

  foreach ($clean as $cid => $val) {
    $check->execute([$cid]);
    $exists = $check->fetch();

    if ($exists) {
      $upd->execute([$val, $cid]);
    } else {
      $ins->execute([$cid, $val]);
    }
  }

  $pdo->commit();
  $_SESSION["_flash"]["success"] = "Bobot berhasil disimpan. Total = " . number_format($total, 3);
} catch (Throwable $e) {
  $pdo->rollBack();
  $_SESSION["_flash"]["error"] = "Gagal menyimpan bobot. " . $e->getMessage();
}

header("Location: /spk-promethee/public/weights/index.php");
exit;
