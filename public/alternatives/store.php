<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/alternatives/index.php");
  exit;
}

$code = strtoupper(trim($_POST["code"] ?? ""));
$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$description = $description === "" ? null : $description;

if ($code === "" || $name === "") {
  header("Location: /spk-promethee/public/alternatives/create.php");
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT INTO alternatives (code, name, description) VALUES (?, ?, ?)");
  $stmt->execute([$code, $name, $description]);
} catch (Throwable $e) {
  // kalau code duplicate, MySQL akan error
  die("Gagal menyimpan. Pastikan kode alternatif unik. Error: " . htmlspecialchars($e->getMessage()));
}

header("Location: /spk-promethee/public/alternatives/index.php");
exit;
