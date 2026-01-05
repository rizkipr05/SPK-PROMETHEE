<?php
// layouts/navbar.php
$userName = $_SESSION["user"]["name"] ?? "Admin";
$userRole = $_SESSION["user"]["role"] ?? "admin";
?>
<header class="topbar">
  <div class="brand">
    <div class="brand-mark">SPK</div>
    <div>
      <div class="brand-title">PROMETHEE</div>
      <div class="brand-subtitle">Sistem Pendukung Keputusan</div>
    </div>
  </div>

  <div class="topbar-right">
    <div class="user-chip">
      <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($userName) ?></div>
        <div class="user-role"><?= htmlspecialchars($userRole) ?></div>
      </div>
    </div>
    <a class="btn btn-danger" href="/spk-promethee/public/logout.php">
      <span class="icon"><?= icon_svg("logout") ?></span>
      Logout
    </a>
  </div>
</header>
