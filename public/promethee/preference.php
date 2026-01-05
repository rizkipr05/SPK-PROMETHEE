<?php
// app/promethee/preference.php

/**
 * Hitung preferensi P(a,b) untuk 1 kriteria
 * - benefit: semakin besar semakin baik -> d = va - vb
 * - cost: semakin kecil semakin baik -> d = vb - va
 * Normalisasi sederhana pakai range (max-min) agar 0..1 (lebih stabil).
 */
function preference_value(float $va, float $vb, string $type, float $range): float
{
  if ($type === "cost") {
    $d = $vb - $va;
  } else {
    $d = $va - $vb;
  }

  if ($d <= 0) return 0.0;

  // normalisasi by range (hindari skala beda jauh antar kriteria)
  if ($range <= 0) return 1.0; // kalau semua sama, anggap preferensi penuh saat d>0
  $p = $d / $range;

  // clamp
  if ($p < 0) $p = 0;
  if ($p > 1) $p = 1;

  return $p;
}
