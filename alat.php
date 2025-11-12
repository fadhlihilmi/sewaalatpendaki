<?php
require 'config.php';
$pdo = getPDO();
$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = null;
    if(isset($_FILES['foto']) && $_FILES['foto']['error']==0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid().'.'.$ext;
        move_uploaded_file($_FILES['foto']['tmp_name'],'uploads/'.$filename);
    }
    $stmt = $pdo->prepare("INSERT INTO alat (nama,kategori,kondisi,stok,harga_per_hari,keterangan,foto) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$_POST['nama'],$_POST['kategori'],$_POST['kondisi'],intval($_POST['stok']),floatval($_POST['harga_per_hari']),$_POST['keterangan'],$filename]);
    header('Location: alat.php'); exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = intval($_POST['id']);
    $stmt = $pdo->prepare("SELECT foto FROM alat WHERE id=?");
    $stmt->execute([$edit_id]);
    $old = $stmt->fetch();
    $filename = $old['foto'];

    if(isset($_FILES['foto']) && $_FILES['foto']['error']==0){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid().'.'.$ext;
        move_uploaded_file($_FILES['foto']['tmp_name'],'uploads/'.$filename);
    }

    $stmt = $pdo->prepare("UPDATE alat SET nama=?,kategori=?,kondisi=?,stok=?,harga_per_hari=?,keterangan=?,foto=? WHERE id=?");
    $stmt->execute([$_POST['nama'],$_POST['kategori'],$_POST['kondisi'],intval($_POST['stok']),floatval($_POST['harga_per_hari']),$_POST['keterangan'],$filename,$edit_id]);
    header('Location: alat.php'); exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    if($id){
        $stmt = $pdo->prepare("SELECT foto FROM alat WHERE id=?");
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        if($old['foto'] && file_exists('uploads/'.$old['foto'])) unlink('uploads/'.$old['foto']);
        $pdo->prepare("DELETE FROM alat WHERE id=?")->execute([$id]);
    }
    header('Location: alat.php'); exit;
}

$edit = null;
if($action==='edit'){
    $id=intval($_GET['id']??0);
    $stmt=$pdo->prepare("SELECT * FROM alat WHERE id=?");
    $stmt->execute([$id]);
    $edit=$stmt->fetch();
}

$alatList=$pdo->query("SELECT * FROM alat ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Kelola Alat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

<header class="mb-4"><h1 class="display-6">Kelola Alat</h1></header>

<nav class="mb-3">
  <a class="btn btn-outline-primary me-2" href="index.php">Home</a>
  <a class="btn btn-outline-warning" href="transaksi.php">Transaksi</a>
</nav>

<section class="card p-3 mb-4 shadow-sm bg-white rounded">
<h2 class="h5 mb-3"><?= $edit ? 'Edit Alat' : 'Tambah Alat' ?></h2>
<form method="post" action="alat.php?action=<?= $edit?'update':'create' ?>" enctype="multipart/form-data">
<?php if($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
<div class="mb-2"><label>Nama<input required name="nama" class="form-control" value="<?= $edit?htmlentities($edit['nama']):'' ?>"></label></div>
<div class="mb-2"><label>Kategori<input name="kategori" class="form-control" value="<?= $edit?htmlentities($edit['kategori']):'' ?>"></label></div>
<div class="mb-2"><label>Kondisi<input name="kondisi" class="form-control" value="<?= $edit?htmlentities($edit['kondisi']):'' ?>"></label></div>
<div class="mb-2"><label>Stok<input type="number" min="0" name="stok" class="form-control" value="<?= $edit?(int)$edit['stok']:1 ?>"></label></div>
<div class="mb-2"><label>Harga per hari<input name="harga_per_hari" class="form-control" value="<?= $edit?htmlentities($edit['harga_per_hari']):'' ?>"></label></div>
<div class="mb-2"><label>Keterangan<textarea name="keterangan" class="form-control"><?= $edit?htmlentities($edit['keterangan']):'' ?></textarea></label></div>
<div class="mb-2">
<label>Foto Alat</label>
<input type="file" name="foto" class="form-control">
<?php if($edit && $edit['foto']): ?>
<img src="uploads/<?= $edit['foto'] ?>" style="height:100px;margin-top:5px;">
<?php endif; ?>
</div>
<div class="d-flex gap-2 mt-2"><button class="btn btn-success" type="submit"><?= $edit?'Simpan':'Tambah' ?></button><a class="btn btn-secondary" href="alat.php">Batal</a></div>
</form>
</section>

<section>
<h2 class="h5 mb-3">Data Alat</h2>
<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
<thead class="table-dark"><tr><th>ID</th><th>Foto</th><th>Nama</th><th>Stok</th><th>Harga/Hari</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($alatList as $a): ?>
<tr>
<td><?= $a['id'] ?></td>
<td><?php if($a['foto']): ?><img src="uploads/<?= $a['foto'] ?>" style="height:50px;"><?php else:?><span class="text-muted">Tidak ada</span><?php endif;?></td>
<td><?= htmlentities($a['nama']) ?></td>
<td><?= (int)$a['stok'] ?></td>
<td><?= number_format($a['harga_per_hari'],0,',','.') ?></td>
<td>
<a class="btn btn-sm btn-warning me-1" href="alat.php?action=edit&id=<?= $a['id'] ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="alat.php?action=delete&id=<?= $a['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
</td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body></html>
