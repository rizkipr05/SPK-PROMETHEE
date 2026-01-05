<?php
require_once __DIR__ . "/../../app/auth/auth_check.php";
require_once __DIR__ . "/../../app/config/database.php";
require_once __DIR__ . "/../../app/promethee/engine.php";

$title = "Hitung PROMETHEE - SPK PROMETHEE";

function flash_set($k,$v){ $_SESSION["_flash"][$k]=$v; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    // Ambil alternatif & kriteria + bobot
    $alts = $pdo->query("SELECT id, code, name FROM alternatives ORDER BY code ASC")->fetchAll();

    $critSql = "
      SELECT c.id, c.code, c.name, c.type, COALESCE(w.weight, 0) AS weight
      FROM criteria c
      LEFT JOIN weights w ON w.criteria_id = c.id
      ORDER BY c.code ASC
    ";
    $crits = $pdo->query($critSql)->fetchAll();

    if (count($alts) < 2) throw new Exception("Minimal butuh 2 alternatif.");
    if (count($crits) < 1) throw new Exception("Kriteria belum ada.");

    // Validasi bobot
    $sumW = 0.0;
    foreach ($crits as $c) $sumW += (float)$c["weight"];
    if (abs($sumW - 1.0) > 0.001) {
      throw new Exception("Total bobot harus = 1.00. Sekarang: " . number_format($sumW, 3));
    }

    // Ambil nilai evaluations -> matrix
    $evals = $pdo->query("SELECT alternative_id, criteria_id, value FROM evaluations")->fetchAll();
    $values = [];
    foreach ($evals as $e) {
      $values[(int)$e["alternative_id"]][(int)$e["criteria_id"]] = (float)$e["value"];
    }

    // Hitung PROMETHEE
    $computed = promethee_compute($alts, $crits, $values);

    // Simpan ke DB (result_runs & results)
    $pdo->beginTransaction();

    $note = "Perhitungan PROMETHEE (auto)";
    $stmtRun = $pdo->prepare("INSERT INTO result_runs (note) VALUES (?)");
    $stmtRun->execute([$note]);
    $runId = (int)$pdo->lastInsertId();

    // bersihin kalau mau (harusnya gak perlu karena run baru)
    $ins = $pdo->prepare("
      INSERT INTO results (run_id, alternative_id, leaving_flow, entering_flow, net_flow, rank)
      VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($computed as $aid => $r) {
      $ins->execute([
        $runId,
        (int)$aid,
        (float)$r["leaving_flow"],
        (float)$r["entering_flow"],
        (float)$r["net_flow"],
        (int)$r["rank"],
      ]);
    }

    $pdo->commit();

    flash_set("success", "Perhitungan berhasil. Run ID: $runId");
    header("Location: /spk-promethee/public/promethee/result.php?run_id=" . $runId);
    exit;

  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash_set("error", $e->getMessage());
    header("Location: /spk-promethee/public/promethee/calculate.php");
    exit;
  }
}

// FLASH
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
      <h3 style="margin:0;">Hitung PROMETHEE</h3>
      <p class="muted" style="margin:6px 0 0;">
        Sistem akan menghitung <b>Leaving Flow</b>, <b>Entering Flow</b>, <b>Net Flow</b>, lalu membuat <b>Ranking</b>.
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

      <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
        <form method="POST" onsubmit="return confirm('Jalankan perhitungan PROMETHEE sekarang?');">
          <button class="btn btn-primary" type="submit">🧮 Jalankan Perhitungan</button>
        </form>
        <a class="btn" href="/spk-promethee/public/weights/index.php">⚖️ Cek Bobot</a>
        <a class="btn" href="/spk-promethee/public/evaluations/index.php">🧾 Cek Nilai</a>
      </div>

      <div style="margin-top:14px;" class="muted">
        <b>Catatan:</b> Total bobot harus 1.00 dan semua nilai matriks harus terisi.
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
