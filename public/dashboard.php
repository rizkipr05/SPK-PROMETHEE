<?php
require_once __DIR__ . "/../app/auth/auth_check.php";

$title = "Dashboard - SPK PROMETHEE";

// contoh KPI sementara (nanti kita ambil dari DB)
require_once __DIR__ . "/../app/config/database.php"; // $pdo

$altCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM alternatives")->fetch()["c"];
$critCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM criteria")->fetch()["c"];
$evalCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM evaluations")->fetch()["c"];

require_once __DIR__ . "/../layouts/header.php";
require_once __DIR__ . "/../layouts/navbar.php";
require_once __DIR__ . "/../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">
    <div class="card" style="margin-bottom:14px;">
      <h3>Selamat datang</h3>
      <p class="muted">
        Kelola data alternatif, kriteria, bobot, input nilai, lalu lakukan perhitungan PROMETHEE untuk mendapatkan ranking lokasi terbaik.
      </p>
      <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn btn-primary" href="/spk-promethee/public/promethee/calculate.php">
          <span class="icon"><?= icon_svg("calculate") ?></span>
          Hitung PROMETHEE
        </a>
        <a class="btn" href="/spk-promethee/public/evaluations/index.php">
          <span class="icon"><?= icon_svg("evaluations") ?></span>
          Input Nilai
        </a>
        <a class="btn" href="/spk-promethee/public/alternatives/index.php">
          <span class="icon"><?= icon_svg("alternatives") ?></span>
          Kelola Alternatif
        </a>
      </div>
    </div>

    <div class="grid grid-3">
      <div class="card">
        <div class="kpi">
          <div>
            <div class="label">Alternatif</div>
            <div class="num"><?= $altCount ?></div>
          </div>
          <span class="icon icon-lg"><?= icon_svg("alternatives") ?></span>
        </div>
        <p class="muted">Total lokasi yang akan diranking.</p>
      </div>

      <div class="card">
        <div class="kpi">
          <div>
            <div class="label">Kriteria</div>
            <div class="num"><?= $critCount ?></div>
          </div>
          <span class="icon icon-lg"><?= icon_svg("criteria") ?></span>
        </div>
        <p class="muted">Jumlah kriteria penilaian.</p>
      </div>

      <div class="card">
        <div class="kpi">
          <div>
            <div class="label">Nilai Tersimpan</div>
            <div class="num"><?= $evalCount ?></div>
          </div>
          <span class="icon icon-lg"><?= icon_svg("evaluations") ?></span>
        </div>
        <p class="muted">Baris penilaian alternatif × kriteria.</p>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
