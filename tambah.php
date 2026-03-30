<?php
require_once "../../config/database.php";
require_once "../../config/auth.php";
require_once "../../helpers/log.php";

cekRole('admin');

$kategori = $pdo->query("SELECT * FROM kategori")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode     = trim($_POST['kode']);
    $nama     = trim($_POST['nama']);
    $kategori_id = $_POST['kategori'];
    $stok     = (int)$_POST['stok'];
    $kondisi  = $_POST['kondisi'];
    $lokasi   = trim($_POST['lokasi']);

    if ($kode === "" || $nama === "" || $stok < 0) {
        $error = "Data wajib diisi dengan benar!";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tb_alat
            (id_kategori, kode_alat, nama_alat, stok, kondisi, lokasi)
            VALUES (:kat, :kode, :nama, :stok, :kondisi, :lokasi)
        ");
        $stmt->execute([
            'kat' => $kategori_id,
            'kode' => $kode,
            'nama' => $nama,
            'stok' => $stok,
            'kondisi' => $kondisi,
            'lokasi' => $lokasi
        ]);

        tambahLog($pdo, 'TAMBAH', 'Menambahkan alat: ' . $nama);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Alat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
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
        .form-control, .form-select {
            border-radius: 10px;
        }
        .header-title {
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <h3 class="header-title mb-0">Tambah Alat</h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Alat</label>
                        <input type="text" name="kode" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Alat</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id_kategori']; ?>">
                                    <?= htmlspecialchars($k['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kondisi</label>
                        <select name="kondisi" class="form-select">
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>