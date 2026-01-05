<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

$title = "Input Nilai - SPK PROMETHEE";

$alts = $pdo->query("SELECT id, code, name FROM alternatives ORDER BY code ASC")->fetchAll();
$crits = $pdo->query("SELECT id, code, name, type FROM criteria ORDER BY code ASC")->fetchAll();

$evals = $pdo->query("SELECT alternative_id, criteria_id, value FROM evaluations")->fetchAll();
$values = [];
foreach ($evals as $e) {
  $values[(int)$e["alternative_id"]][(int)$e["criteria_id"]] = (float)$e["value"];
}

$flash = $_SESSION["_flash"] ?? [];
$msg_success = $flash["success"] ?? null;
$msg_error = $flash["error"] ?? null;
unset($_SESSION["_flash"]);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/navbar.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="main">
  <div class="container">
    <div class="card">
      <h3 style="margin:0;">Input Nilai Alternatif</h3>
      <p class="muted" style="margin:6px 0 0;">
        Isi nilai tiap alternatif untuk setiap kriteria. Semua nilai wajib diisi angka.
      </p>

      <?php if ($msg_success): ?>
        <div style="margin-top:12px; padding:10px 12px; border-radius:12px; background:rgba(34,197,94,.12); color:#166534; font-weight:700;">
          <?= htmlspecialchars($msg_success) ?>
        </div>
      <?php endif; ?>

      <?php if ($msg_error): ?>
        <div style="margin-top:12px; padding:10px 12px; border-radius:12px; background:rgba(239,68,68,.12); color:#991b1b; font-weight:700;">
          <?= htmlspecialchars($msg_error) ?>
        </div>
      <?php endif; ?>

      <?php if (!$alts || !$crits): ?>
        <div style="margin-top:14px;" class="muted">
          Data belum lengkap. Pastikan <b>Alternatif</b> dan <b>Kriteria</b> sudah diisi.
        </div>
        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
          <a class="btn" href="/spk-promethee/public/alternatives/index.php">Kelola Alternatif</a>
          <a class="btn" href="/spk-promethee/public/criteria/index.php">Kelola Kriteria</a>
        </div>
      <?php else: ?>
        <form method="POST" action="/spk-promethee/public/evaluations/store.php" style="margin-top:14px;">
          <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:920px;">
              <thead>
                <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                  <th style="padding:10px; width:220px;">Alternatif</th>
                  <?php foreach ($crits as $c): ?>
                    <th style="padding:10px;">
                      <div style="font-weight:800;"><?= htmlspecialchars($c["code"]) ?></div>
                      <div class="muted" style="font-size:11px;"><?= htmlspecialchars($c["name"]) ?></div>
                      <div style="font-size:11px; font-weight:800; color:<?= $c["type"] === "benefit" ? "#166534" : "#991b1b" ?>;">
                        <?= htmlspecialchars($c["type"]) ?>
                      </div>
                    </th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($alts as $a): ?>
                  <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px;">
                      <div style="font-weight:800;"><?= htmlspecialchars($a["code"]) ?></div>
                      <div class="muted" style="font-size:12px;"><?= htmlspecialchars($a["name"]) ?></div>
                    </td>
                    <?php foreach ($crits as $c): 
                      $aid = (int)$a["id"];
                      $cid = (int)$c["id"];
                      $val = $values[$aid][$cid] ?? "";
                    ?>
                      <td style="padding:10px;">
                        <input
                          type="number"
                          name="values[<?= $aid ?>][<?= $cid ?>]"
                          value="<?= htmlspecialchars((string)$val) ?>"
                          step="0.001"
                          style="width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:10px;"
                          required
                        >
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">Simpan Nilai</button>
            <a class="btn" href="/spk-promethee/public/weights/index.php">Cek Bobot</a>
            <a class="btn" href="/spk-promethee/public/promethee/calculate.php">Hitung PROMETHEE</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
