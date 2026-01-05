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

    <div class="card" style="margin-top:14px;">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
          <h3 style="margin:0;">Ringkasan Data</h3>
          <p class="muted" style="margin:6px 0 0;">Grafik sederhana jumlah data utama.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <div class="muted" style="font-size:12px;">Update otomatis</div>
          <div style="display:flex; gap:8px; align-items:center;">
            <span style="width:10px;height:10px;border-radius:999px;background:#4f46e5;display:inline-block;"></span>
            <span class="muted" style="font-size:12px;">Alternatif</span>
            <span style="width:10px;height:10px;border-radius:999px;background:#0ea5e9;display:inline-block;"></span>
            <span class="muted" style="font-size:12px;">Kriteria</span>
            <span style="width:10px;height:10px;border-radius:999px;background:#22c55e;display:inline-block;"></span>
            <span class="muted" style="font-size:12px;">Nilai</span>
          </div>
        </div>
      </div>
      <div style="margin-top:12px;">
        <canvas id="summaryChart" height="230" style="width:100%; max-width:100%;"></canvas>
      </div>
    </div>
  </div>
</main>
<script>
(() => {
  const canvas = document.getElementById("summaryChart");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");
  const data = [
    { label: "Alternatif", value: <?= (int)$altCount ?>, color: "#4f46e5" },
    { label: "Kriteria", value: <?= (int)$critCount ?>, color: "#0ea5e9" },
    { label: "Nilai", value: <?= (int)$evalCount ?>, color: "#22c55e" }
  ];

  const render = () => {
    const width = canvas.clientWidth;
    const height = canvas.height;
    canvas.width = width;

    ctx.clearRect(0, 0, width, height);
    const padding = 24;
    const maxVal = Math.max(1, ...data.map(d => d.value));
    const unit = (width - padding * 2) / data.length;
    const barWidth = unit * 0.72;
    const gap = unit * 0.28;

    ctx.strokeStyle = "#e5e7eb";
    ctx.beginPath();
    ctx.moveTo(padding, height - 30);
    ctx.lineTo(width - padding, height - 30);
    ctx.stroke();

    data.forEach((d, i) => {
      const x = padding + i * (barWidth + gap) + gap / 2;
      const barHeight = Math.round((height - 70) * (d.value / maxVal));
      const y = height - 30 - barHeight;

      ctx.fillStyle = d.color;
      ctx.fillRect(x, y, barWidth, barHeight);

      ctx.fillStyle = "#0f172a";
      ctx.font = "13px Arial, sans-serif";
      const valueText = d.value.toString();
      const valueWidth = ctx.measureText(valueText).width;
      ctx.fillText(valueText, x + (barWidth - valueWidth) / 2, y - 8);

      ctx.fillStyle = "#64748b";
      ctx.font = "12px Arial, sans-serif";
      const labelWidth = ctx.measureText(d.label).width;
      ctx.fillText(d.label, x + (barWidth - labelWidth) / 2, height - 10);
    });
  };

  render();
  window.addEventListener("resize", render);
})();
</script>
<?php require_once __DIR__ . "/../layouts/footer.php"; ?>
