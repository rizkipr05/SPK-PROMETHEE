<?php
// public/login.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../app/helpers/flash.php";

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION["user"])) {
  header("Location: /spk-promethee/public/dashboard.php");
  exit;
}

// CSRF token
if (!isset($_SESSION["csrf"])) {
  $_SESSION["csrf"] = bin2hex(random_bytes(16));
}

$error = flash_get("error");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - SPK PROMETHEE</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fb;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center}
    .card{width:100%;max-width:420px;background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:26px}
    h1{margin:0 0 6px;font-size:22px}
    p{margin:0 0 18px;color:#555;font-size:14px}
    .alert{background:#ffe8e8;color:#b00020;padding:10px 12px;border-radius:10px;margin-bottom:14px;font-size:13px}
    label{display:block;font-size:13px;margin:10px 0 6px;color:#333}
    input{width:100%;padding:12px 12px;border:1px solid #dfe3ea;border-radius:12px;outline:none}
    input:focus{border-color:#4f46e5;box-shadow:0 0 0 4px rgba(79,70,229,.12)}
    button{width:100%;margin-top:16px;padding:12px;border:0;border-radius:12px;background:#4f46e5;color:#fff;font-weight:700;cursor:pointer}
    button:hover{filter:brightness(.95)}
    .hint{margin-top:12px;font-size:12px;color:#666}
  </style>
</head>
<body>
  <div class="card">
    <h1>Login Admin</h1>
    <p>SPK PROMETHEE - Sistem Pendukung Keputusan</p>

    <?php if ($error): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/spk-promethee/app/auth/login_process.php" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION["csrf"]) ?>">

      <label>Username</label>
      <input type="text" name="username" placeholder="admin" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <button type="submit">Masuk</button>
    </form>

    <div class="hint">
      *Pastikan user admin sudah ada di tabel <b>users</b> dan password_hash valid.
    </div>
  </div>
</body>
</html>
