<?php
// layouts/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/icons.php";
$title = $title ?? "SPK PROMETHEE";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="/spk-promethee/public/assets/css/style.css">
</head>
<body>
<div class="app">
