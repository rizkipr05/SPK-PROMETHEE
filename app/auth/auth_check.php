<?php
// app/auth/auth_check.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["user"])) {
  header("Location: /spk-promethee/public/login.php");
  exit;
}
