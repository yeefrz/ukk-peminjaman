<?php
session_start();
require '../config/database.php';
require '../config/auth.php';
require_once "../helpers/icon.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$totalUser = $pdo->query("SELECT COUNT(*) FROM tb_user")->fetchColumn();

$totalAlat = $pdo->query("SELECT COUNT(*) FROM tb_alat")->fetchColumn();

$peminjamanAktif = $pdo->query("
    SELECT COUNT(*) FROM peminjaman 
    WHERE status = 'dipinjam'
")->fetchColumn();

$pengembalianHariIni = $pdo->query("
    SELECT COUNT(*) FROM pengembalian
    WHERE DATE(tgl_kembali) = CURDATE()
")->fetchColumn();

$stmt = $pdo->query("
    SELECT l.*, u.nama
    FROM log_aktivitas l
    LEFT JOIN tb_user u ON l.id_user = u.id_user
    ORDER BY l.created_at DESC
    LIMIT 5
");

$aktivitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtVerif = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tb_user 
    WHERE status_aktif = 0 
    AND role IN ('admin','petugas')
");
$stmtVerif->execute();
$totalVerifikasi = $stmtVerif->fetchColumn();

$nama = user()['nama'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | <?= htmlspecialchars($nama) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #ffffff, #f8f9ff);
            border-right: 1px solid #e3e6f0;
            padding-top: 20px;
        }

        .sidebar h6 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 12px;
            margin-top: 0px;
            letter-spacing: 0.5px;
        }

        /* beda warna tiap section */
        .sidebar h6:nth-of-type(1) {
            color: #3b5bdb; /* biru */
        }

        .sidebar h6:nth-of-type(2) {
            color: #20c997; /* hijau tosca */
        }

        .sidebar h6:nth-of-type(3) {
            color: #fd7e14; /* orange */
        }

        .sidebar .nav-link {
            font-size: 1.2rem;
            font-weight: 600;
            padding: 13px 15px;
            color: #495057;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all 0.25s ease;
            position: relative;
        }

        /* garis kiri modern */
        .sidebar .nav-link::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            height: 70%;
            width: 4px;
            background-color: transparent;
            border-radius: 4px;
            transition: 0.25s;
        }

        .sidebar .nav-link:hover {
            background-color: #eef2ff;
            color: #3b5bdb;
        }

        .sidebar .nav-link:hover::before {
            background-color: #3b5bdb;
        }

        /* ACTIVE */
        .sidebar .nav-link.active {
            background-color: #3b5bdb;
            color: white !important;
        }

        .sidebar .nav-link.active::before {
            background-color: white;
        }

        .navbar-custom {
            background-color: #1e2a38;
        }

        .navbar-custom .navbar-brand {
            font-weight: bold;
        }

        .content-area {
            padding: 25px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-3">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-box-seam me-2"></i>
        Peminjaman Barang
    </a>

    <div class="ms-auto dropdown">
        <a class="text-white text-decoration-none dropdown-toggle"
           href="#" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-4"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="profile/index.php">Profile</a></li>
            <li><a class="dropdown-item text-danger" href="../public/logout.php">Log Out</a></li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-3">

            <h6 class="text-muted">Manajemen User</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="user/index.php">
                        <i class="bi bi-people me-2"></i> Data User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" href="user/verifikasi.php">
    <span>
        <i class="bi bi-person-check me-2"></i> Verifikasi User
    </span>

    <?php if ($totalVerifikasi > 0): ?>
        <span class="badge text-danger border border-danger rounded-pill">
            <?= $totalVerifikasi ?>
        </span>
    <?php endif; ?>
</a>
                </li>
            </ul>

            <h6 class="text-muted">Inventaris</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="kategori/index.php">
                        <i class="bi bi-tags me-2"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="alat/index.php">
                        <i class="bi bi-tools me-2"></i> Alat
                    </a>
                </li>
            </ul>

            <h6 class="text-muted">Transaksi</h6>
            <ul class="nav flex-column mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="peminjaman/index.php">
                        <i class="bi bi-journal-text me-2"></i> Peminjaman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengembalian/index.php">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Pengembalian
                    </a>
                </li>
                </ul>

                <ul class="nav flex-column mb-3"></ul>
                <li class="nav-item">
                    <a class="nav-link" href="log_aktivitas/index.php">
                        <i class="bi bi-file-text me-2"></i> Log Aktivitas
                    </a>
                </li>
            </ul>

        </div>

        <!-- CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 content-area">

    <h4 class="mb-4 fw-semibold">Dashboard Overview</h4>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total User</h6>
                    <h2 class="fw-bold"><?= $totalUser ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Total Alat</h6>
                    <h2 class="fw-bold"><?= $totalAlat ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Peminjaman Aktif</h6>
                    <h2 class="fw-bold"><?= $peminjamanAktif ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="text-muted">Pengembalian Hari Ini</h6>
                    <h2 class="fw-bold"><?= $pengembalianHariIni ?></h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Aktivitas Terbaru</h5>

            <ul class="list-group list-group-flush">
            <?php foreach($aktivitas as $row): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>

                    <span style="font-size: 1.2rem;">
                    <?= getIcon($row['aksi']) ?>
                    </span>

                        <strong>
                            <?= $row['nama'] ?? 'System' ?>
                        </strong>

                        <div class="small text-muted">
                            <?= $row['deskripsi'] ?>
                        </div>
                    </div>

                    <span class="text-muted small">
                        <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                    </span>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>

</main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>