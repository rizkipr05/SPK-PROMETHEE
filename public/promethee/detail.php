<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";
require_once __DIR__ . "/details.php";

$title = "Detail Perhitungan - PROMETHEE";

// ambil alternatif
$alts = $pdo->query("SELECT id, code, name FROM alternatives ORDER BY code ASC")->fetchAll();

// ambil kriteria + bobot
$critSql = "
  SELECT c.id, c.code, c.name, c.type, COALESCE(w.weight, 0) AS weight
  FROM criteria c
  LEFT JOIN weights w ON w.criteria_id = c.id
  ORDER BY c.code ASC
";
$crits = $pdo->query($critSql)->fetchAll();

// ambil nilai evaluations -> matrix
$evals = $pdo->query("SELECT alternative_id, criteria_id, value FROM evaluations")->fetchAll();
$values = [];
foreach ($evals as $e) {
  $values[(int)$e["alternative_id"]][(int)$e["criteria_id"]] = (float)$e["value"];
}

$details = null;
$error = null;

try {
  if (count($alts) < 2) throw new Exception("Minimal butuh 2 alternatif.");
  if (count($crits) < 1) throw new Exception("Kriteria belum ada.");

  $sumW = 0.0;
  foreach ($crits as $c) $sumW += (float)$c["weight"];
  if (abs($sumW - 1.0) > 0.001) {
    throw new Exception("Total bobot harus = 1.00. Sekarang: " . number_format($sumW, 3));
  }

  $details = promethee_compute_details($alts, $crits, $values);
} catch (Throwable $e) {
  $error = $e->getMessage();
}

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">

    <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
      <div>
        <h3 style="margin:0;">Detail Langkah Perhitungan PROMETHEE</h3>
        <p class="muted" style="margin:6px 0 0;">Menampilkan range kriteria, matriks π(a,b), serta flow per alternatif.</p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="/spk-promethee/public/promethee/calculate.php">
          <span class="icon"><?= icon_svg("calculate") ?></span>
          Hitung & Simpan Run
        </a>
        <a class="btn btn-primary" href="/spk-promethee/public/promethee/result.php">
          <span class="icon"><?= icon_svg("result") ?></span>
          Lihat Hasil
        </a>
        <a class="btn" href="#" data-print="true">
          <span class="icon"><?= icon_svg("print") ?></span>
          Cetak PDF
        </a>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="card" style="margin-top:14px;">
        <div style="padding:10px 12px; border-radius:12px; background:rgba(239,68,68,.12); color:#991b1b; font-weight:800;">
          <?= htmlspecialchars($error) ?>
        </div>
        <p class="muted" style="margin:10px 0 0;">
          Pastikan: bobot total 1.00, dan semua nilai matriks sudah terisi.
        </p>
      </div>
    <?php else: ?>

      <!-- 1) RANGE PER KRITERIA -->
      <div class="card" style="margin-top:14px; overflow:auto;">
        <h3 style="margin:0 0 10px;">1) Range per Kriteria (max - min)</h3>
        <table style="width:100%; border-collapse:collapse; min-width:820px;">
          <thead>
            <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
              <th style="padding:10px;">Kode</th>
              <th style="padding:10px;">Nama</th>
              <th style="padding:10px;">Tipe</th>
              <th style="padding:10px;">Bobot</th>
              <th style="padding:10px;">Range</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($crits as $c): 
              $cid = (int)$c["id"];
              $range = (float)($details["ranges"][$cid] ?? 0);
            ?>
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px; font-weight:900;"><?= htmlspecialchars($c["code"]) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($c["name"]) ?></td>
                <td style="padding:10px;"><span class="muted"><?= htmlspecialchars($c["type"]) ?></span></td>
                <td style="padding:10px; font-weight:900;"><?= number_format((float)$c["weight"], 3) ?></td>
                <td style="padding:10px;"><?= number_format($range, 6) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p class="muted" style="margin:10px 0 0;">
          Range dipakai untuk normalisasi preferensi agar skala antar kriteria seimbang.
        </p>
      </div>

      <!-- 2) MATRIKS π(a,b) -->
      <div class="card" style="margin-top:14px; overflow:auto;">
        <h3 style="margin:0 0 10px;">2) Matriks Preferensi Global π(a,b)</h3>
        <p class="muted" style="margin:0 0 12px;">
          π(a,b) menunjukkan seberapa kuat alternatif <b>a</b> lebih disukai dibanding <b>b</b> (0..1). Diagonal kosong (a=a).
        </p>

        <table style="width:100%; border-collapse:collapse; min-width:980px;">
          <thead>
            <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
              <th style="padding:10px; width:220px;">a \ b</th>
              <?php foreach ($alts as $b): ?>
                <th style="padding:10px;"><?= htmlspecialchars($b["code"]) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($alts as $a): ?>
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px;">
                  <div style="font-weight:900;"><?= htmlspecialchars($a["code"]) ?></div>
                  <div class="muted" style="font-size:12px;"><?= htmlspecialchars($a["name"]) ?></div>
                </td>

                <?php foreach ($alts as $b): ?>
                  <?php
                    $aid = (int)$a["id"];
                    $bid = (int)$b["id"];
                    if ($aid === $bid) {
                      $cell = "-";
                    } else {
                      $val = (float)($details["pi"][$aid][$bid] ?? 0);
                      $cell = number_format($val, 6);
                    }
                  ?>
                  <td style="padding:10px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                    <?= $cell ?>
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- 3) FLOW -->
      <div class="card" style="margin-top:14px; overflow:auto;">
        <h3 style="margin:0 0 10px;">3) Leaving / Entering / Net Flow & Ranking</h3>

        <table style="width:100%; border-collapse:collapse; min-width:860px;">
          <thead>
            <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
              <th style="padding:10px; width:90px;">Rank</th>
              <th style="padding:10px;">Alternatif</th>
              <th style="padding:10px;">Leaving</th>
              <th style="padding:10px;">Entering</th>
              <th style="padding:10px;">Net</th>
            </tr>
          </thead>
          <tbody>
            <?php
              // urutkan berdasarkan net desc
              $altSorted = $alts;
              usort($altSorted, function($x,$y) use ($details){
                $ax = (float)$details["net"][(int)$x["id"]];
                $ay = (float)$details["net"][(int)$y["id"]];
                if ($ax == $ay) return 0;
                return ($ax < $ay) ? 1 : -1;
              });
            ?>
            <?php foreach ($altSorted as $a): 
              $aid = (int)$a["id"];
              $rank = (int)($details["rank"][$aid] ?? 0);
              $le = (float)($details["leaving"][$aid] ?? 0);
              $en = (float)($details["entering"][$aid] ?? 0);
              $net = (float)($details["net"][$aid] ?? 0);
            ?>
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px; font-weight:900;">
                  <?php if ($rank === 1): ?>
                    <span class="icon"><?= icon_svg("star") ?></span> <?= $rank ?>
                  <?php else: ?>
                    <?= $rank ?>
                  <?php endif; ?>
                </td>
                <td style="padding:10px;">
                  <div style="font-weight:900;"><?= htmlspecialchars($a["code"]) ?></div>
                  <div class="muted" style="font-size:12px;"><?= htmlspecialchars($a["name"]) ?></div>
                </td>
                <td style="padding:10px;"><?= number_format($le, 6) ?></td>
                <td style="padding:10px;"><?= number_format($en, 6) ?></td>
                <td style="padding:10px; font-weight:900; color:#4f46e5;"><?= number_format($net, 6) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <p class="muted" style="margin:10px 0 0;">
          Rumus: Leaving(a) = rata-rata π(a,b), Entering(a) = rata-rata π(b,a), Net(a) = Leaving(a) − Entering(a).
        </p>
      </div>

    <?php endif; ?>

  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
