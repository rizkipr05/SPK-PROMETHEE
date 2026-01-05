<?php
// app/auth/login_process.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../config/database.php";  // $pdo
require_once __DIR__ . "/../helpers/flash.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: /spk-promethee/public/login.php");
  exit;
}

// CSRF ringan (opsional). Kalau kamu belum pakai token, boleh hapus blok ini.
$csrf_ok = isset($_POST["csrf"], $_SESSION["csrf"]) && hash_equals($_SESSION["csrf"], $_POST["csrf"]);
if (!$csrf_ok) {
  flash_set("error", "Sesi tidak valid. Silakan coba lagi.");
  header("Location: /spk-promethee/public/login.php");
  exit;
}

$username = trim($_POST["username"] ?? "");
$password = (string)($_POST["password"] ?? "");

if ($username === "" || $password === "") {
  flash_set("error", "Username dan password wajib diisi.");
  header("Location: /spk-promethee/public/login.php");
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT id, name, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($password, $user["password_hash"])) {
    flash_set("error", "Username atau password salah.");
    header("Location: /spk-promethee/public/login.php");
    exit;
  }

  // Security best practice
  session_regenerate_id(true);

  // simpan data minimal
  $_SESSION["user"] = [
    "id" => (int)$user["id"],
    "name" => $user["name"],
    "username" => $user["username"],
    "role" => $user["role"],
  ];

  // boleh hapus csrf supaya tiap login fresh
  unset($_SESSION["csrf"]);

  header("Location: /spk-promethee/public/dashboard.php");
  exit;

} catch (Throwable $e) {
  flash_set("error", "Terjadi error saat login. Coba lagi.");
  header("Location: /spk-promethee/public/login.php");
  exit;
}
