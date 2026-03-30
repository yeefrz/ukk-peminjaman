<?php
require_once "../../config/database.php";
require_once "../../config/auth.php";

cekRole('admin');

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM tb_alat WHERE id_alat = :id");
$stmt->execute(['id' => $id]);
$alat = $stmt->fetch();

if (!$alat) {
    header("Location: index.php");
    exit;
}

$kategori = $pdo->query("SELECT * FROM kategori")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $kategori_id = $_POST['kategori'];
    $stok     = (int)$_POST['stok'];
    $kondisi  = $_POST['kondisi'];
    $lokasi   = trim($_POST['lokasi']);

    $stmt = $pdo->prepare("
        UPDATE tb_alat
        SET id_kategori = :kat,
            nama_alat = :nama,
            stok = :stok,
            kondisi = :kondisi,
            lokasi = :lokasi
        WHERE id_alat = :id
    ");
    $stmt->execute([
        'kat' => $kategori_id,
        'nama' => $nama,
        'stok' => $stok,
        'kondisi' => $kondisi,
        'lokasi' => $lokasi,
        'id' => $id
    ]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Alat</title>
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
        .kode-field {
            background: #e2e8f0;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <h3 class="header-title mb-0">Edit Alat</h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <form method="post">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Alat</label>
                        <input type="text"
                               class="form-control kode-field"
                               value="<?= htmlspecialchars($alat['kode_alat']); ?>"
                               disabled>
                        <div class="form-text">Kode tidak bisa diubah</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Alat</label>
                        <input type="text"
                               name="nama"
                               class="form-control"
                               value="<?= htmlspecialchars($alat['nama_alat']); ?>"
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id_kategori']; ?>"
                                    <?= $k['id_kategori']==$alat['id_kategori']?'selected':''; ?>>
                                    <?= htmlspecialchars($k['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stok</label>
                        <input type="number"
                               name="stok"
                               class="form-control"
                               value="<?= $alat['stok']; ?>"
                               min="0"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kondisi</label>
                        <select name="kondisi" class="form-select">
                            <option value="baik" <?= $alat['kondisi']=='baik'?'selected':''; ?>>Baik</option>
                            <option value="rusak" <?= $alat['kondisi']=='rusak'?'selected':''; ?>>Rusak</option>
                            <option value="maintenance" <?= $alat['kondisi']=='maintenance'?'selected':''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Lokasi</label>
                    <input type="text"
                           name="lokasi"
                           class="form-control"
                           value="<?= htmlspecialchars($alat['lokasi']); ?>">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>