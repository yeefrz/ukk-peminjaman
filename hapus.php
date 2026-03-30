<?php
require_once "../../config/database.php";
require_once "../../config/auth.php";
require_once "../../helpers/log.php";

cekRole('admin');

$id = (int)($_GET['id'] ?? 0);

// ambil nama alat dulu
$stmt = $pdo->prepare("SELECT nama_alat FROM tb_alat WHERE id_alat = :id");
$stmt->execute(['id' => $id]);
$alat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alat) {
    die("Data alat tidak ditemukan.");
}

// cegah hapus kalau sedang dipinjam
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM peminjaman_detail WHERE id_alat = :id
");
$stmt->execute(['id' => $id]);

if ($stmt->fetchColumn() > 0) {
    die("Alat tidak bisa dihapus karena sedang / pernah dipinjam.");
}

// hapus
$stmt = $pdo->prepare("DELETE FROM tb_alat WHERE id_alat = :id");
$stmt->execute(['id' => $id]);

// LOG SETELAH BERHASIL DELETE
tambahLog($pdo, 'HAPUS', 'Menghapus alat: ' . $alat['nama_alat']);

header("Location: index.php");
exit;