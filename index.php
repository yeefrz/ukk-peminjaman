<?php
require_once "../../config/database.php";
require_once "../../config/auth.php";

cekRole('admin');

$stmt = $pdo->query("
    SELECT a.*, k.nama_kategori
    FROM tb_alat a
    JOIN kategori k ON a.id_kategori = k.id_kategori
    ORDER BY a.id_alat DESC
");
$alat = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Alat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8fafc;
        }
        .card {
            border: none;
            border-radius: 18px;
        }
        .table thead {
            background: #0f172a;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f5f9;
        }
        .badge-kondisi {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 20px;
        }
        .header-title {
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

            <a href="../dashboard.php" 
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <div class="d-flex align-items-center gap-3">
            <h3 class="header-title mb-0">Data Alat</h3>
        </div>

        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah Alat
        </a>

    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Kondisi</th>
                            <th>Lokasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (empty($alat)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i><br>
                                Belum ada data alat.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($alat as $a): ?>
                        <tr>
                            <td class="fw-semibold">
                                <?= htmlspecialchars($a['kode_alat']); ?>
                            </td>

                            <td><?= htmlspecialchars($a['nama_alat']); ?></td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    <?= htmlspecialchars($a['nama_kategori']); ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-dark">
                                    <?= $a['stok']; ?>
                                </span>
                            </td>

                            <td>
                                <?php
                                $kondisi = strtolower($a['kondisi']);
                                $warna = 'secondary';

                                if ($kondisi == 'baik') $warna = 'success';
                                elseif ($kondisi == 'rusak') $warna = 'danger';
                                elseif ($kondisi == 'perlu perbaikan') $warna = 'warning';
                                ?>

                                <span class="badge bg-<?= $warna ?> badge-kondisi">
                                    <?= ucfirst($a['kondisi']); ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($a['lokasi']); ?></td>

                            <td class="text-center">
                                <a href="edit.php?id=<?= $a['id_alat']; ?>" 
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="hapus.php?id=<?= $a['id_alat']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin hapus alat ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>