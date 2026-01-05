<?php
// app/promethee/details.php
require_once __DIR__ . "/preference.php";

/**
 * PROMETHEE II + details:
 * return:
 * - ranges[cid] = range (max-min)
 * - pi[aid][bid] = preferensi global
 * - leaving[aid], entering[aid], net[aid]
 * - rankMap[aid]
 */
function promethee_compute_details(array $alternatives, array $criteria, array $values): array
{
  $n = count($alternatives);
  if ($n < 2) throw new Exception("Minimal butuh 2 alternatif.");

  // 1) hitung range tiap kriteria
  $ranges = [];
  foreach ($criteria as $c) {
    $cid = (int)$c["id"];
    $min = null; $max = null;

    foreach ($alternatives as $a) {
      $aid = (int)$a["id"];
      if (!isset($values[$aid][$cid])) {
        throw new Exception("Nilai belum lengkap: {$a['code']} untuk {$c['code']} belum diisi.");
      }
      $v = (float)$values[$aid][$cid];
      $min = ($min === null) ? $v : min($min, $v);
      $max = ($max === null) ? $v : max($max, $v);
    }
    $ranges[$cid] = (float)($max - $min);
  }

  // 2) matriks preferensi global π(a,b)
  $pi = []; // [aid][bid] = float
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    foreach ($alternatives as $b) {
      $bid = (int)$b["id"];
      if ($aid === $bid) continue;

      $sum = 0.0;
      foreach ($criteria as $c) {
        $cid  = (int)$c["id"];
        $w    = (float)$c["weight"];
        $type = (string)$c["type"];

        $va = (float)$values[$aid][$cid];
        $vb = (float)$values[$bid][$cid];

        $p = preference_value($va, $vb, $type, (float)$ranges[$cid]);
        $sum += $w * $p;
      }

      $pi[$aid][$bid] = $sum; // 0..1 (karena sum bobot 1)
    }
  }

  // 3) leaving & entering
  $leaving = [];
  $entering = [];
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    $sumLeave = 0.0;
    $sumEnter = 0.0;

    foreach ($alternatives as $b) {
      $bid = (int)$b["id"];
      if ($aid === $bid) continue;
      $sumLeave += (float)($pi[$aid][$bid] ?? 0);
      $sumEnter += (float)($pi[$bid][$aid] ?? 0);
    }

    $leaving[$aid]  = $sumLeave / ($n - 1);
    $entering[$aid] = $sumEnter / ($n - 1);
  }

  // 4) net + ranking
  $net = [];
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    $net[$aid] = $leaving[$aid] - $entering[$aid];
  }

  $sorted = $alternatives;
  usort($sorted, function($x, $y) use ($net) {
    $ax = $net[(int)$x["id"]];
    $ay = $net[(int)$y["id"]];
    if ($ax == $ay) return 0;
    return ($ax < $ay) ? 1 : -1;
  });

  $rankMap = [];
  $rank = 1;
  $prev = null;
  foreach ($sorted as $a) {
    $aid = (int)$a["id"];
    $val = $net[$aid];
    if ($prev !== null && abs($val - $prev) > 1e-12) $rank++;
    $rankMap[$aid] = $rank;
    $prev = $val;
  }

  return [
    "ranges" => $ranges,
    "pi" => $pi,
    "leaving" => $leaving,
    "entering" => $entering,
    "net" => $net,
    "rank" => $rankMap,
  ];
}
