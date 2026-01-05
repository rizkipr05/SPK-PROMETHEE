<?php
// layouts/sidebar.php
$base = "/spk-promethee/public";
$current = $_SERVER["SCRIPT_NAME"] ?? "";

function nav_active(string $current, array $paths): string
{
  return in_array($current, $paths, true) ? " active" : "";
}

?>
<aside class="sidebar">
  <div class="nav">
    <div class="nav-section">Utama</div>
    <a class="nav-item<?= nav_active($current, ["$base/dashboard.php"]) ?>" href="<?= $base ?>/dashboard.php">
      <span class="nav-icon"><?= icon_svg("dashboard") ?></span>
      <span>Dashboard</span>
    </a>

    <div class="nav-section">Data Master</div>
    <a class="nav-item<?= nav_active($current, ["$base/alternatives/index.php", "$base/alternatives/create.php", "$base/alternatives/edit.php"]) ?>" href="<?= $base ?>/alternatives/index.php">
      <span class="nav-icon"><?= icon_svg("alternatives") ?></span>
      <span>Alternatif</span>
    </a>
    <a class="nav-item<?= nav_active($current, ["$base/criteria/index.php", "$base/criteria/create.php", "$base/criteria/edit.php"]) ?>" href="<?= $base ?>/criteria/index.php">
      <span class="nav-icon"><?= icon_svg("criteria") ?></span>
      <span>Kriteria</span>
    </a>
    <a class="nav-item<?= nav_active($current, ["$base/weights/index.php"]) ?>" href="<?= $base ?>/weights/index.php">
      <span class="nav-icon"><?= icon_svg("weights") ?></span>
      <span>Bobot</span>
    </a>
    <a class="nav-item<?= nav_active($current, ["$base/evaluations/index.php"]) ?>" href="<?= $base ?>/evaluations/index.php">
      <span class="nav-icon"><?= icon_svg("evaluations") ?></span>
      <span>Nilai</span>
    </a>

    <div class="nav-section">PROMETHEE</div>
    <a class="nav-item<?= nav_active($current, ["$base/promethee/calculate.php"]) ?>" href="<?= $base ?>/promethee/calculate.php">
      <span class="nav-icon"><?= icon_svg("calculate") ?></span>
      <span>Hitung</span>
    </a>
    <a class="nav-item<?= nav_active($current, ["$base/promethee/result.php"]) ?>" href="<?= $base ?>/promethee/result.php">
      <span class="nav-icon"><?= icon_svg("result") ?></span>
      <span>Hasil</span>
    </a>
    <a class="nav-item<?= nav_active($current, ["$base/promethee/detail.php"]) ?>" href="<?= $base ?>/promethee/detail.php">
      <span class="nav-icon"><?= icon_svg("details") ?></span>
      <span>Detail</span>
    </a>
  </div>

  <div class="sidebar-footer">
    <div class="small-muted">SPK PROMETHEE • Navigation</div>
  </div>
</aside>
