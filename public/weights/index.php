<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";

$title = "Bobot Kriteria - SPK PROMETHEE";

// ambil kriteria + bobot (kalau belum ada, default 0)
$sql = "
  SELECT c.id, c.code, c.name, c.type, COALESCE(w.weight, 0) AS weight
  FROM criteria c
  LEFT JOIN weights w ON w.criteria_id = c.id
  ORDER BY c.code ASC
";
$rows = $pdo->query($sql)->fetchAll();

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
      <h3 style="margin:0;">Bobot Kriteria</h3>
      <p class="muted" style="margin:6px 0 0;">
        Isi bobot tiap kriteria. Total bobot harus = <b>1.00</b> (contoh jurnal: 0.30 + 0.25 + 0.20 + 0.15 + 0.10).
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

      <form method="POST" action="/spk-promethee/public/weights/update.php" style="margin-top:14px;">
        <div style="overflow:auto;">
          <table style="width:100%; border-collapse:collapse; min-width:760px;">
            <thead>
              <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                <th style="padding:10px;">Kode</th>
                <th style="padding:10px;">Nama</th>
                <th style="padding:10px;">Tipe</th>
                <th style="padding:10px; width:220px;">Bobot (0-1)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$rows): ?>
                <tr>
                  <td colspan="4" style="padding:14px;" class="muted">
                    Belum ada kriteria. Tambahkan kriteria dulu.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($rows as $r): ?>
                  <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px; font-weight:800;"><?= htmlspecialchars($r["code"]) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($r["name"]) ?></td>
                    <td style="padding:10px;">
                      <span style="padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px;
                        background:<?= $r["type"] === "benefit" ? "rgba(34,197,94,.12)" : "rgba(239,68,68,.12)" ?>;
                        color:<?= $r["type"] === "benefit" ? "#166534" : "#991b1b" ?>;">
                        <?= htmlspecialchars($r["type"]) ?>
                      </span>
                    </td>
                    <td style="padding:10px;">
                      <input
                        type="number"
                        name="weights[<?= (int)$r["id"] ?>]"
                        step="0.001"
                        min="0"
                        max="1"
                        value="<?= htmlspecialchars((string)$r["weight"]) ?>"
                        style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px;"
                        required
                      >
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($rows): ?>
          <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <button class="btn btn-primary" type="submit">Simpan Bobot</button>
            <a class="btn" href="/spk-promethee/public/criteria/index.php">Kelola Kriteria</a>

            <div class="muted" style="margin-left:auto;">
              <span id="totalWeight">Total: -</span>
            </div>
          </div>
        <?php endif; ?>
      </form>
    </div>

  </div>
</main>

<script>
(function(){
  const inputs = document.querySelectorAll('input[type="number"][name^="weights["]');
  const el = document.getElementById('totalWeight');

  function calc(){
    let sum = 0;
    inputs.forEach(i => {
      const v = parseFloat(i.value || "0");
      sum += isNaN(v) ? 0 : v;
    });
    if (el) el.textContent = "Total: " + sum.toFixed(3);
  }

  inputs.forEach(i => i.addEventListener('input', calc));
  calc();
})();
</script>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
