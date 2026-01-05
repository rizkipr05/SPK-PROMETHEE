<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/criteria/index.php");
  exit;
}

$code = strtoupper(trim($_POST["code"] ?? ""));
$name = trim($_POST["name"] ?? "");
$type = trim($_POST["type"] ?? "benefit");

if ($code === "" || $name === "") {
  header("Location: /spk-promethee/public/criteria/create.php");
  exit;
}

if (!in_array($type, ["benefit","cost"], true)) {
  $type = "benefit";
}

try {
  $stmt = $pdo->prepare("INSERT INTO criteria (code, name, type) VALUES (?, ?, ?)");
  $stmt->execute([$code, $name, $type]);
} catch (Throwable $e) {
  die("Gagal menyimpan. Pastikan kode kriteria unik. Error: " . htmlspecialchars($e->getMessage()));
}

header("Location: /spk-promethee/public/criteria/index.php");
exit;
