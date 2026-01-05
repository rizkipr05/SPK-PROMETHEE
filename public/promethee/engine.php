<?php
// app/promethee/engine.php
require_once __DIR__ . "/preference.php";

/**
 * Compute PROMETHEE II
 *
 * Input:
 * - $alternatives: array of [id, code, name]
 * - $criteria: array of [id, code, name, type, weight]
 * - $values: matrix values [alt_id][crit_id] = float
 *
 * Output:
 * - results: array per alt: leaving, entering, net, rank
 */
function promethee_compute(array $alternatives, array $criteria, array $values): array
{
  $n = count($alternatives);
  if ($n < 2) {
    throw new Exception("Minimal butuh 2 alternatif untuk PROMETHEE.");
  }

  // range per kriteria (max-min)
  $ranges = [];
  foreach ($criteria as $c) {
    $cid = (int)$c["id"];
    $min = null; $max = null;

    foreach ($alternatives as $a) {
      $aid = (int)$a["id"];
      if (!isset($values[$aid][$cid])) {
        throw new Exception("Nilai belum lengkap: alternatif {$a['code']} untuk kriteria {$c['code']} belum diisi.");
      }
      $v = (float)$values[$aid][$cid];
      $min = ($min === null) ? $v : min($min, $v);
      $max = ($max === null) ? $v : max($max, $v);
    }
    $ranges[$cid] = (float)($max - $min);
  }

  // matriks preferensi global π(a,b)
  $pi = []; // [aid][bid] = float
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    foreach ($alternatives as $b) {
      $bid = (int)$b["id"];
      if ($aid === $bid) continue;

      $sum = 0.0;
      foreach ($criteria as $c) {
        $cid = (int)$c["id"];
        $w = (float)$c["weight"];
        $type = (string)$c["type"];

        $va = (float)$values[$aid][$cid];
        $vb = (float)$values[$bid][$cid];
        $p  = preference_value($va, $vb, $type, (float)$ranges[$cid]);

        $sum += $w * $p;
      }
      $pi[$aid][$bid] = $sum;
    }
  }

  // leaving & entering
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

  // net flow
  $net = [];
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    $net[$aid] = $leaving[$aid] - $entering[$aid];
  }

  // ranking (dense rank: ties rank sama)
  $sorted = $alternatives;
  usort($sorted, function($x, $y) use ($net) {
    $ax = $net[(int)$x["id"]];
    $ay = $net[(int)$y["id"]];
    if ($ax == $ay) return 0;
    return ($ax < $ay) ? 1 : -1; // desc
  });

  $rankMap = [];
  $rank = 1;
  $prev = null;
  foreach ($sorted as $idx => $a) {
    $aid = (int)$a["id"];
    $val = $net[$aid];
    if ($prev !== null && abs($val - $prev) > 1e-12) {
      $rank = $rank + 1;
    }
    $rankMap[$aid] = $rank;
    $prev = $val;
  }

  // result per alt
  $out = [];
  foreach ($alternatives as $a) {
    $aid = (int)$a["id"];
    $out[$aid] = [
      "alternative_id" => $aid,
      "leaving_flow" => $leaving[$aid],
      "entering_flow" => $entering[$aid],
      "net_flow" => $net[$aid],
      "rank" => $rankMap[$aid] ?? 0,
    ];
  }

  return $out;
}
