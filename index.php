<?php
require 'config.php';
$pdo = getPDO();

$action = $_GET['action'] ?? 'list';
if ($action === 'list') {
    $q = trim($_GET['q'] ?? '');
    if ($q !== '') {
        $stmt = $pdo->prepare("SELECT * FROM alat WHERE nama LIKE ? OR kategori LIKE ? ORDER BY id DESC");
        $stmt->execute(["%$q%","%$q%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM alat ORDER BY id DESC");
    }
    $alatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sewa Alat Gunung - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

<header class="mb-4">
    <h1 class="display-6">Sewa Alat Gunung</h1>
    <p class="text-muted">GUNUNG KARANG — transaksi sewa/pengembalian</p>
</header>

<nav class="mb-4">
  <a class="btn btn-outline-primary me-2" href="index.php">Alat</a>
  <a class="btn btn-outline-success me-2" href="alat.php">Kelola Alat</a>
  <a class="btn btn-outline-warning me-2" href="transaksi.php">Transaksi</a>
  <a class="btn btn-outline-info" href="laporan.php">Laporan</a>
</nav>

<section class="card p-3 mb-4 shadow-sm bg-white rounded">
  <h2 class="h5 mb-3">Daftar Alat</h2>
  <form method="get" class="d-flex mb-3">
    <input type="text" name="q" class="form-control me-2" placeholder="Cari nama atau kategori..." value="<?= htmlentities($q ?? '') ?>">
    <button type="submit" class="btn btn-primary me-2">Cari</button>
    <a class="btn btn-secondary" href="index.php">Reset</a>
  </form>

  <div class="table-responsive">
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-dark"><tr>
      <th>ID</th><th>Foto</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Harga/Hari</th><th>Aksi</th>
    </tr></thead>
    <tbody>
    <?php if (empty($alatList)): ?>
      <tr><td colspan="7" class="text-center text-muted">Belum ada data alat.</td></tr>
    <?php else: foreach($alatList as $a): ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td>
            <?php if($a['foto']): ?>
              <img src="uploads/<?= $a['foto'] ?>" style="height:50px;">
            <?php else: ?>
              <span class="text-muted">Tidak ada</span>
            <?php endif; ?>
        </td>
        <td><?= htmlentities($a['nama']) ?></td>
        <td><?= htmlentities($a['kategori']) ?></td>
        <td><?= (int)$a['stok'] ?></td>
        <td><?= number_format($a['harga_per_hari'],0,',','.') ?></td>
        <td>
          <a class="btn btn-sm btn-primary me-1" href="transaksi.php?action=rent&alat_id=<?= $a['id'] ?>">Sewa</a>
          <a class="btn btn-sm btn-warning" href="alat.php?action=edit&id=<?= $a['id'] ?>">Edit</a>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
  </div>
</section>

<footer class="text-muted text-center mt-4">
    agar bawaan tidak ribet sewaa solusinya.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>
