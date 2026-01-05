-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 05 Jan 2026 pada 05.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_promethee`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatives`
--

CREATE TABLE `alternatives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `alternatives`
--

INSERT INTO `alternatives` (`id`, `code`, `name`, `description`) VALUES
(1, 'A', 'Lokasi A', NULL),
(2, 'B', 'Lokasi B', NULL),
(3, 'C', 'Lokasi C', 'dbwdweb');

-- --------------------------------------------------------

--
-- Struktur dari tabel `criteria`
--

CREATE TABLE `criteria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(160) NOT NULL,
  `type` enum('benefit','cost') NOT NULL DEFAULT 'benefit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `criteria`
--

INSERT INTO `criteria` (`id`, `code`, `name`, `type`) VALUES
(1, 'C1', 'Kesesuaian tanah', 'benefit'),
(2, 'C2', 'Curah hujan', 'benefit'),
(3, 'C3', 'Suhu', 'benefit'),
(4, 'C4', 'Ketinggian tempat', 'benefit'),
(5, 'C5', 'Aksesibilitas', 'benefit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alternative_id` bigint(20) UNSIGNED NOT NULL,
  `criteria_id` bigint(20) UNSIGNED NOT NULL,
  `value` decimal(10,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `results`
--

CREATE TABLE `results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `run_id` bigint(20) UNSIGNED NOT NULL,
  `alternative_id` bigint(20) UNSIGNED NOT NULL,
  `leaving_flow` decimal(12,6) NOT NULL DEFAULT 0.000000,
  `entering_flow` decimal(12,6) NOT NULL DEFAULT 0.000000,
  `net_flow` decimal(12,6) NOT NULL DEFAULT 0.000000,
  `rank` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `result_runs`
--

CREATE TABLE `result_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `run_at` datetime NOT NULL DEFAULT current_timestamp(),
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$U2lkFy9a.3E/u8.D94ipLOhOiOQ4DZSXXIrW07hLmCAejJQlkLq/G', 'admin', '2026-01-05 01:26:40');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_evaluations`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_evaluations` (
`alternative_id` bigint(20) unsigned
,`alternative_code` varchar(20)
,`alternative_name` varchar(160)
,`criteria_id` bigint(20) unsigned
,`criteria_code` varchar(20)
,`criteria_name` varchar(160)
,`value` decimal(10,4)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `weights`
--

CREATE TABLE `weights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `criteria_id` bigint(20) UNSIGNED NOT NULL,
  `weight` decimal(6,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `weights`
--

INSERT INTO `weights` (`id`, `criteria_id`, `weight`) VALUES
(1, 1, 0.300),
(2, 2, 0.250),
(3, 3, 0.200),
(4, 4, 0.150),
(5, 5, 0.100);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_evaluations`
--
DROP TABLE IF EXISTS `v_evaluations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_evaluations`  AS SELECT `a`.`id` AS `alternative_id`, `a`.`code` AS `alternative_code`, `a`.`name` AS `alternative_name`, `c`.`id` AS `criteria_id`, `c`.`code` AS `criteria_code`, `c`.`name` AS `criteria_name`, `e`.`value` AS `value` FROM ((`alternatives` `a` join `criteria` `c`) left join `evaluations` `e` on(`e`.`alternative_id` = `a`.`id` and `e`.`criteria_id` = `c`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_alternatives_code` (`code`);

--
-- Indeks untuk tabel `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_criteria_code` (`code`);

--
-- Indeks untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_evaluations_alt_crit` (`alternative_id`,`criteria_id`),
  ADD KEY `ix_evaluations_criteria` (`criteria_id`);

--
-- Indeks untuk tabel `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_results_run_alt` (`run_id`,`alternative_id`),
  ADD KEY `ix_results_rank` (`rank`),
  ADD KEY `fk_results_alternative` (`alternative_id`);

--
-- Indeks untuk tabel `result_runs`
--
ALTER TABLE `result_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_result_runs_run_at` (`run_at`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_users_username` (`username`);

--
-- Indeks untuk tabel `weights`
--
ALTER TABLE `weights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_weights_criteria` (`criteria_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `results`
--
ALTER TABLE `results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `result_runs`
--
ALTER TABLE `result_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `weights`
--
ALTER TABLE `weights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_evaluations_alternative` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluations_criteria` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_results_alternative` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_results_run` FOREIGN KEY (`run_id`) REFERENCES `result_runs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `weights`
--
ALTER TABLE `weights`
  ADD CONSTRAINT `fk_weights_criteria` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
