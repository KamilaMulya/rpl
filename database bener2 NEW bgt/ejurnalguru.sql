-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 13 Jun 2025 pada 16.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ejurnalguru`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `status_kehadiran` enum('sakit','izin','alpa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `id_siswa`, `id_jurnal`, `status_kehadiran`) VALUES
(8, 1, 9, 'izin'),
(9, 2, 9, 'sakit'),
(10, 3, 9, 'alpa'),
(11, 1, 10, 'sakit'),
(12, 2, 10, 'sakit'),
(13, 3, 10, 'izin'),
(14, 4, 10, 'alpa'),
(15, 1, 11, 'izin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_mengajar`
--

CREATE TABLE `jadwal_mengajar` (
  `id_jadwal_mengajar` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_jam_pelajaran` int(11) NOT NULL,
  `id_mata_pelajaran` int(11) NOT NULL,
  `id_tahun_ajaran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal_mengajar`
--

INSERT INTO `jadwal_mengajar` (`id_jadwal_mengajar`, `id_user`, `id_kelas`, `id_jam_pelajaran`, `id_mata_pelajaran`, `id_tahun_ajaran`) VALUES
(1, 4, 1, 1, 3, 1),
(3, 1, 1, 1, 2, 1),
(4, 1, 2, 1, 2, 1),
(5, 1, 2, 1, 1, 1),
(6, 6, 1, 1, 3, 2),
(7, 1, 4, 1, 2, 2),
(9, 8, 1, 1, 1, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jam_pelajaran`
--

CREATE TABLE `jam_pelajaran` (
  `id_jam_pelajaran` int(11) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `hari` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jam_pelajaran`
--

INSERT INTO `jam_pelajaran` (`id_jam_pelajaran`, `jam_mulai`, `jam_selesai`, `hari`) VALUES
(1, '07:00:00', '08:00:00', 'Senin'),
(3, '08:15:00', '09:00:00', 'Rabu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_guru`
--

CREATE TABLE `jurnal_guru` (
  `id_jurnal_guru` int(11) NOT NULL,
  `id_jadwal_mengajar` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `use_id_user` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `materi` text NOT NULL,
  `catatan` text NOT NULL,
  `status_validasi` int(11) NOT NULL,
  `tanggal_validasi` datetime DEFAULT NULL,
  `catatan_validasi` text DEFAULT NULL,
  `dokumentasi_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurnal_guru`
--

INSERT INTO `jurnal_guru` (`id_jurnal_guru`, `id_jadwal_mengajar`, `id_user`, `use_id_user`, `tanggal`, `materi`, `catatan`, `status_validasi`, `tanggal_validasi`, `catatan_validasi`, `dokumentasi_path`) VALUES
(2, 6, 6, 7, '2025-06-01', 'Aljabar linier dua Variabel', '', 2, '2025-06-11 22:37:37', 'Ya bagus', 'dok_1748752783_4089.png'),
(3, 6, 6, 7, '2025-06-01', 'Matrix ', '', 2, '2025-06-01 11:49:14', 'ga ontime kamuu', 'dok_1748752809_5415.png'),
(4, 6, 6, 7, '2025-05-03', 'Segi Banyak', '', 2, '2025-06-01 11:49:23', NULL, 'dok_1748752840_6883.png'),
(5, 9, 8, 8, '2025-06-02', 'Ekonomi Bisnis Syariah', '', 1, NULL, NULL, 'dok_1748828431_6028.png'),
(6, 9, 8, 7, '2025-06-01', 'Perbankan', 'Sakit : Amelia', 2, '2025-06-02 08:43:39', NULL, 'dok_1748828538_6260.jpg'),
(9, 9, 8, 7, '2025-06-08', 'matriks', 'oke', 2, '2025-06-11 11:07:54', NULL, 'dok_1749388386_8419.png'),
(10, 9, 8, 8, '2025-06-08', 'perkaliannnn', 'polihhhhhhhhh', 1, NULL, NULL, 'dok_1749388719_1674.png'),
(11, 6, 6, 6, '2025-06-11', 'ay', 'ya', 1, NULL, NULL, 'dok_1749614038_9829.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `tingkat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `nama_kelas`, `tingkat`) VALUES
(1, 'X IPA 1', '10'),
(2, 'X IPA 2', '10'),
(4, 'X IPA 3', '10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id_mata_pelajaran` int(11) NOT NULL,
  `nama_mata_pelajaran` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id_mata_pelajaran`, `nama_mata_pelajaran`) VALUES
(1, 'Ekonomi'),
(2, 'Akuntansi'),
(3, 'Matematika Wajib'),
(4, 'Geografi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `jenis_kelamin`, `id_kelas`) VALUES
(1, 'Andi Pratama', 'L', 1),
(2, 'Budi Santoso', 'L', 1),
(3, 'Citra Dewi', 'P', 1),
(4, 'Dewi Lestari', 'P', 1),
(5, 'Eko Nugroho', 'L', 2),
(6, 'Fani Kusuma', 'P', 2),
(7, 'Gilang Ramadhan', 'L', 4),
(8, 'Hani Permata', 'P', 4),
(9, 'Indra Prasetya', 'L', 2),
(10, 'Joko Susilo', 'L', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id_tahun_ajaran` int(11) NOT NULL,
  `tahun` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id_tahun_ajaran`, `tahun`) VALUES
(1, '2023/2024'),
(2, '2024/2025');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `no_hp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `email`, `password`, `role`, `no_hp`) VALUES
(1, 'elvita', 'elvita@gmail.com', '$2y$10$YSmuubu2Bkob5KykIoEs2uYbTnKbd.iQsM1TkpphI3v1xoq802l6y', 1, '0'),
(2, 'titi', 'titi@gmail.com', '$2y$10$28chm.eyr6BQh9ZLqJ/Onue.JFNVKInU3nhSpSLQMnfj8N9eF4Y1a', 2, '0'),
(3, 'siti', 'siti@gmail.com', '$2y$10$GlR9eFmTiX9Fd0F40A5bKu9PkkPsASu77j/420/EdEKj.ozXiTVLG', 3, '0'),
(4, 'Faizah', 'izaa@gmail.com', '1234', 3, '085807166611'),
(6, 'agil munawar', 'agee@gmail.com', '11111', 1, '08577788899'),
(7, 'icha', 'icha@gmail.com', '99999', 2, '469767'),
(8, 'Nadiatul', 'nadia@gmail.com', '1234', 1, '0812345678');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_jurnal` (`id_jurnal`);

--
-- Indeks untuk tabel `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  ADD PRIMARY KEY (`id_jadwal_mengajar`),
  ADD KEY `fk_user` (`id_user`),
  ADD KEY `fk_kelas` (`id_kelas`),
  ADD KEY `fk_jam_pelajaran` (`id_jam_pelajaran`),
  ADD KEY `fk_mata_pelajaran` (`id_mata_pelajaran`),
  ADD KEY `fk_tahun_ajaran` (`id_tahun_ajaran`);

--
-- Indeks untuk tabel `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  ADD PRIMARY KEY (`id_jam_pelajaran`);

--
-- Indeks untuk tabel `jurnal_guru`
--
ALTER TABLE `jurnal_guru`
  ADD PRIMARY KEY (`id_jurnal_guru`),
  ADD KEY `fk_jadwal_mengajar` (`id_jadwal_mengajar`),
  ADD KEY `fk_user2` (`use_id_user`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indeks untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id_mata_pelajaran`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD KEY `siswa_ibfk_1` (`id_kelas`);

--
-- Indeks untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id_tahun_ajaran`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  MODIFY `id_jadwal_mengajar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  MODIFY `id_jam_pelajaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `jurnal_guru`
--
ALTER TABLE `jurnal_guru`
  MODIFY `id_jurnal_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id_mata_pelajaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id_tahun_ajaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_guru` (`id_jurnal_guru`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  ADD CONSTRAINT `fk_jam_pelajaran` FOREIGN KEY (`id_jam_pelajaran`) REFERENCES `jam_pelajaran` (`id_jam_pelajaran`),
  ADD CONSTRAINT `fk_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  ADD CONSTRAINT `fk_mata_pelajaran` FOREIGN KEY (`id_mata_pelajaran`) REFERENCES `mata_pelajaran` (`id_mata_pelajaran`),
  ADD CONSTRAINT `fk_tahun_ajaran` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id_tahun_ajaran`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `jurnal_guru`
--
ALTER TABLE `jurnal_guru`
  ADD CONSTRAINT `fk_jadwal_mengajar` FOREIGN KEY (`id_jadwal_mengajar`) REFERENCES `jadwal_mengajar` (`id_jadwal_mengajar`),
  ADD CONSTRAINT `fk_user2` FOREIGN KEY (`use_id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
