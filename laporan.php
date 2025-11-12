<?php
require 'config.php';
$pdo = getPDO();
$report = $pdo->query("SELECT tr.*, a.nama as nama_alat FROM transaksi tr LEFT JOIN alat a ON a.id=tr.alat_id ORDER BY tr.id DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Laporan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

<header class="mb-4"><h1 class="display-6">Laporan Transaksi</h1></header>
<nav class="mb-3"><a class="btn btn-outline-primary" href="index.php">Home</a></nav>

<section class="card p-3 shadow-sm bg-white rounded">
<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
<thead class="table-dark"><tr><th>ID</th><th>Penyewa</th><th>Alat</th><th>Jumlah</th><th>Harga</th><th>Status</th></tr></thead>
<tbody>
<?php foreach($report as $r): ?>
<tr><td><?= $r['id'] ?></td><td><?= htmlentities($r['nama_penyewa']) ?></td><td><?= htmlentities($r['nama_alat']) ?></td><td><?= (int)$r['jumlah'] ?></td><td><?= number_format($r['harga_total'],0,',','.') ?></td><td><?= htmlentities($r['status']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

<script src="https://
