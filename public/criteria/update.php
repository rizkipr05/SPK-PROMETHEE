<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/criteria/index.php");
  exit;
}

$id = (int)($_POST["id"] ?? 0);
$code = strtoupper(trim($_POST["code"] ?? ""));
$name = trim($_POST["name"] ?? "");
$type = trim($_POST["type"] ?? "benefit");

if ($id <= 0 || $code === "" || $name === "") {
  header("Location: /spk-promethee/public/criteria/index.php");
  exit;
}

if (!in_array($type, ["benefit","cost"], true)) {
  $type = "benefit";
}

try {
  $stmt = $pdo->prepare("UPDATE criteria SET code = ?, name = ?, type = ? WHERE id = ?");
  $stmt->execute([$code, $name, $type, $id]);
} catch (Throwable $e) {
  die("Gagal update. Pastikan kode kriteria unik. Error: " . htmlspecialchars($e->getMessage()));
}

header("Location: /spk-promethee/public/criteria/index.php");
exit;
