<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

$title = "Hasil & Ranking - SPK PROMETHEE";

// pilih run_id: dari query atau terbaru
$runId = (int)($_GET["run_id"] ?? 0);
if ($runId <= 0) {
  $row = $pdo->query("SELECT id, run_at, note FROM result_runs ORDER BY id DESC LIMIT 1")->fetch();
  $runId = (int)($row["id"] ?? 0);
}

$run = null;
$results = [];

if ($runId > 0) {
  $stmt = $pdo->prepare("SELECT id, run_at, note FROM result_runs WHERE id = ? LIMIT 1");
  $stmt->execute([$runId]);
  $run = $stmt->fetch();

  $sql = "
    SELECT
      r.rank, a.code, a.name,
      r.leaving_flow, r.entering_flow, r.net_flow
    FROM results r
    JOIN alternatives a ON a.id = r.alternative_id
    WHERE r.run_id = ?
    ORDER BY r.rank ASC, r.net_flow DESC
  ";
  $stmt2 = $pdo->prepare($sql);
  $stmt2->execute([$runId]);
  $results = $stmt2->fetchAll();
}

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">

    <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
      <div>
        <h3 style="margin:0;">Hasil & Ranking PROMETHEE</h3>
        <p class="muted" style="margin:6px 0 0;">
          <?= $run ? "Run ID: " . (int)$run["id"] . " • " . htmlspecialchars($run["run_at"]) : "Belum ada hasil perhitungan." ?>
        </p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn btn-primary" href="/spk-promethee/public/promethee/calculate.php">🧮 Hitung Lagi</a>
      </div>
    </div>

    <?php if (!$run): ?>
      <div class="card" style="margin-top:14px;">
        <p class="muted" style="margin:0;">Belum ada data hasil. Silakan lakukan perhitungan dulu di menu <b>Hitung PROMETHEE</b>.</p>
      </div>
    <?php else: ?>

      <?php if (!$results): ?>
        <div class="card" style="margin-top:14px;">
          <p class="muted" style="margin:0;">Hasil untuk run ini tidak ditemukan.</p>
        </div>
      <?php else: ?>
        <div class="card" style="margin-top:14px; overflow:auto;">
          <table style="width:100%; border-collapse:collapse; min-width:860px;">
            <thead>
              <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                <th style="padding:10px; width:90px;">Rank</th>
                <th style="padding:10px;">Alternatif</th>
                <th style="padding:10px;">Leaving</th>
                <th style="padding:10px;">Entering</th>
                <th style="padding:10px;">Net Flow</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $r): ?>
                <tr style="border-bottom:1px solid #f1f5f9;">
                  <td style="padding:10px; font-weight:900;">
                    <?php if ((int)$r["rank"] === 1): ?>
                      🥇 <?= (int)$r["rank"] ?>
                    <?php else: ?>
                      <?= (int)$r["rank"] ?>
                    <?php endif; ?>
                  </td>
                  <td style="padding:10px;">
                    <div style="font-weight:900;"><?= htmlspecialchars($r["code"]) ?></div>
                    <div class="muted" style="font-size:12px;"><?= htmlspecialchars($r["name"]) ?></div>
                  </td>
                  <td style="padding:10px;"><?= number_format((float)$r["leaving_flow"], 6) ?></td>
                  <td style="padding:10px;"><?= number_format((float)$r["entering_flow"], 6) ?></td>
                  <td style="padding:10px; font-weight:900; color:#4f46e5;">
                    <?= number_format((float)$r["net_flow"], 6) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="card" style="margin-top:14px;">
          <p class="muted" style="margin:0;">
            Interpretasi: <b>Net Flow</b> paling besar → alternatif terbaik.
          </p>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
