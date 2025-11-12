-- sewa_db.sql
-- Buat database: CREATE DATABASE sewa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Lalu import file ini di phpMyAdmin atau mysql CLI.
CREATE TABLE `alat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `kondisi` varchar(100) DEFAULT NULL,
  `stok` int DEFAULT 1,
  `harga_per_hari` decimal(10,2) DEFAULT 0,
  `keterangan` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_penyewa` varchar(255) NOT NULL,
  `alat_id` int NOT NULL,
  `jumlah` int NOT NULL DEFAULT 1,
  `tgl_sewa` date NOT NULL,
  `tgl_kembali_rencana` date DEFAULT NULL,
  `tgl_dikembalikan` datetime DEFAULT NULL,
  `harga_total` decimal(12,2) DEFAULT 0,
  `status` varchar(50) DEFAULT 'sewa',
  PRIMARY KEY (`id`),
  KEY (`alat_id`),
  CONSTRAINT `transaksi_ibfk_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- sample data
INSERT INTO alat (nama,kategori,kondisi,stok,harga_per_hari,keterangan) VALUES
('Tenda 2P','Tenda','Baik',5,50000,'Tenda 2 orang'),
('Carrier 60L','Carrier','Baik',3,40000,'Carrier besar'),
('Matras','Perlengkapan Tidur','Baik',10,10000,'Matras lipat');
