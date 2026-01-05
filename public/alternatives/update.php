<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

$id = (int)($_POST["id"] ?? 0);
$code = strtoupper(trim($_POST["code"] ?? ""));
$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$description = $description === "" ? null : $description;

if ($id <= 0 || $code === "" || $name === "") {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE alternatives SET code = ?, name = ?, description = ? WHERE id = ?");
  $stmt->execute([$code, $name, $description, $id]);
} catch (Throwable $e) {
  die("Gagal update. Pastikan kode alternatif unik. Error: " . htmlspecialchars($e->getMessage()));
}

header("Location: /spk-promethee/public/alternatives/index.php");
exit;
