<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

$id = (int)($_POST["id"] ?? 0);
if ($id <= 0) {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM alternatives WHERE id = ?");
  $stmt->execute([$id]);
} catch (Throwable $e) {
  die("Gagal hapus. Error: " . htmlspecialchars($e->getMessage()));
}

header("Location: /spk-promethee/public/alternatives/index.php");
exit;
