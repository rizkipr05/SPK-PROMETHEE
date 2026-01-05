<?php
// app/helpers/flash.php
if (session_status() === PHP_SESSION_NONE) session_start();

function flash_set(string $key, string $message): void {
  $_SESSION["_flash"][$key] = $message;
}

function flash_get(string $key): ?string {
  if (!isset($_SESSION["_flash"][$key])) return null;
  $msg = $_SESSION["_flash"][$key];
  unset($_SESSION["_flash"][$key]);
  return $msg;
}
