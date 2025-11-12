<?php
require 'config.php';
$pdo = getPDO();
$action = $_GET['action'] ?? 'list';

if ($action === 'rent' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO transaksi (nama_penyewa, alat_id, jumlah, tgl_sewa, tgl_kembali_rencana, harga_total, status) VALUES (?,?,?,?,?,?,?)");
    $jumlah = intval($_POST['jumlah']);
    $alat_id = intval($_POST['alat_id']);
    $a = $pdo->prepare("SELECT stok,harga_per_hari FROM alat WHERE id=?");
    $a->execute([$alat_id]); $ad = $a->fetch();
    if (!$ad || $ad['stok'] < $jumlah) { header('Location: transaksi.php?error=stok'); exit; }
    $days = max(1,intval($_POST['lama'] ?? 1));
    $harga_total = $ad['harga_per_hari'] * $jumlah * $days;
    $stmt->execute([$_POST['nama_penyewa'],$alat_id,$jumlah,$_POST['tgl_sewa'],$_POST['tgl_kembali_rencana'],$harga_total,'sewa']);
    $pdo->prepare("UPDATE alat SET stok = stok - ? WHERE id=?")->execute([$jumlah,$alat_id]);
    header('Location: transaksi.php'); exit;
}

if ($action === 'return' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $t = $pdo->prepare("SELECT * FROM transaksi WHERE id=? AND status='sewa'");
    $t->execute([$id]); $td = $t->fetch();
    if ($td) {
        $pdo->prepare("UPDATE transaksi SET status='kembali', tgl_dikembalikan=NOW() WHERE id=?")->execute([$id]);
        $pdo->prepare("UPDATE alat SET stok = stok + ? WHERE id=?")->execute([$td['jumlah'],$td['alat_id']]);
    }
    header('Location: transaksi.php'); exit;
}

$trans = $pdo->query("SELECT tr.*, a.nama as nama_alat FROM transaksi tr LEFT JOIN alat a ON a.id=tr.alat_id ORDER BY tr.id DESC")->fetchAll();
$alatList = $pdo->query("SELECT * FROM alat WHERE stok>0 ORDER BY nama")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Transaksi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

<header class="mb-4"><h1 class="display-6">Transaksi Sewa</h1><p class="text-muted">Buat transaksi sewa dan proses pengembalian</p></header>
<nav class="mb-3">
  <a class="btn btn-outline-primary me-2" href="index.php">Home</a>
  <a class="btn btn-outline-success" href="alat.php">Kelola Alat</a>
</nav>

<section class="card p-3 mb-4 shadow-sm bg-white rounded">
<h2 class="h5 mb-3">Buat Sewa</h2>
<?php if (isset($_GET['error']) && $_GET['error']==='stok'): ?><div class="alert alert-danger">Stok tidak mencukupi.</div><?php endif; ?>
<form method="post" action="transaksi.php?action=rent">
  <div class="mb-2"><label>Nama Penyewa<input required name="nama_penyewa" class="form-control"></label></div>
  <div class="mb-2"><label>Alat<select name="alat_id" class="form-select" required>
    <?php foreach($alatList as $a): ?><option value="<?= $a['id'] ?>"><?= htmlentities($a['nama']) ?> (stok: <?= $a['stok'] ?>)</option><?php endforeach; ?>
  </select></label></div>
  <div class="mb-2"><label>Jumlah<input type="number" name="jumlah" value="1" min="1" class="form-control"></label></div>
  <div class="mb-2"><label>Tanggal Sewa<input type="date" name="tgl_sewa" value="<?= date('Y-m-d') ?>" class="form-control"></label></div>
  <div class="mb-2"><label>Lama (hari)<input type="number" name="lama" value="1" min="1" class="form-control"></label></div>
  <div class="mb-2"><label>Tanggal Kembali Rencana (opsional)<input type="date" name="tgl_kembali_rencana" class="form-control"></label></div>
  <div class="d-flex gap-2 mt-2"><button class="btn btn-success" type="submit">Buat Sewa</button></div>
</form>
</section>

<section>
<h2 class="h5 mb-3">Daftar Transaksi</h2>
<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
<thead class="table-dark"><tr><th>ID</th><th>Penyewa</th><th>Alat</th><th>Jumlah</th><th>Harga Total</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($trans as $t): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlentities($t['nama_penyewa']) ?></td>
<td><?= htmlentities($t['nama_alat']) ?></td>
<td><?= (int)$t['jumlah'] ?></td>
<td><?= number_format($t['harga_total'],0,',','.') ?></td>
<td><?= htmlentities($t['status']) ?><?= $t['tgl_dikembalikan'] ? ' ('.htmlentities($t['tgl_dikembalikan']).')' : '' ?></td>
<td>
<?php if ($t['status'] === 'sewa'): ?>
<a class="btn btn-sm btn-primary" href="transaksi.php?action=return&id=<?= $t['id'] ?>" onclick="return confirm('Proses pengembalian?')">Kembalikan</a>
<?php else: ?>
<span class="text-muted">Selesai</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>
