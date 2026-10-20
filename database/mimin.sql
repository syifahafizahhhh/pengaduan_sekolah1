-- Database MIMIN : Aplikasi Pengaduan Sarana Sekolah (import langsung via phpMyAdmin / HeidiSQL Laragon)
CREATE DATABASE IF NOT EXISTS MIMIN CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE MIMIN;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS riwayat_status, pengaduan, kategori, users;
SET FOREIGN_KEY_CHECKS=1;

-- users: akun login admin & siswa
CREATE TABLE users(
 id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
 role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- kategori: jenis sarana yang diadukan
CREATE TABLE kategori(id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(80) NOT NULL) ENGINE=InnoDB;
-- pengaduan: laporan dari siswa
CREATE TABLE pengaduan(
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, kategori_id INT NOT NULL,
 judul VARCHAR(150) NOT NULL, lokasi VARCHAR(100) NOT NULL, deskripsi TEXT NOT NULL, foto VARCHAR(255) NULL,
 status ENUM('Menunggu','Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu',
 tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(kategori_id) REFERENCES kategori(id)
) ENGINE=InnoDB;
-- riwayat_status: perkembangan penanganan + umpan balik admin
CREATE TABLE riwayat_status(
 id INT AUTO_INCREMENT PRIMARY KEY, pengaduan_id INT NOT NULL, admin_id INT NULL,
 status ENUM('Menunggu','Diproses','Selesai','Ditolak') NOT NULL, catatan TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(pengaduan_id) REFERENCES pengaduan(id) ON DELETE CASCADE,
 FOREIGN KEY(admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Data awal. Login: admin / admin123 , murid1 / murid123 , murid2 / murid123
INSERT INTO users(nama,username,password,role) VALUES
('Administrator Sekolah','admin','$2b$10$WZCIcdd/DNbZnt1RvBSfI.dzQM5AXQaqjcx6Xgb3yuTt4d1hr9Mnm','admin'),
('Alya Putri','murid1','$2b$10$QgtRL4SivxYLy0.uU2JvU.HWmn2r5z8UpzGb4qIlr7iRwqFBhbNNm','siswa'),
('Bima Pratama','murid2','$2b$10$QgtRL4SivxYLy0.uU2JvU.HWmn2r5z8UpzGb4qIlr7iRwqFBhbNNm','siswa');
INSERT INTO kategori(nama) VALUES('Ruang Kelas'),('Toilet'),('Laboratorium'),('Lapangan & Olahraga'),('Perpustakaan'),('Listrik & Air');
INSERT INTO pengaduan(user_id,kategori_id,judul,lokasi,deskripsi,status,tanggal) VALUES
(2,1,'Kipas angin kelas rusak','Kelas XI RPL 1','Dua kipas angin di bagian belakang kelas tidak menyala sejak minggu lalu.','Diproses',NOW()-INTERVAL 3 DAY),
(2,2,'Keran toilet bocor','Toilet lantai 2','Keran wastafel terus meneteskan air dan lantai menjadi licin.','Menunggu',NOW()-INTERVAL 1 DAY),
(3,3,'Komputer lab tidak bisa menyala','Lab Komputer 1','Lima unit komputer di barisan paling belakang mati total.','Selesai',NOW()-INTERVAL 10 DAY);
INSERT INTO riwayat_status(pengaduan_id,admin_id,status,catatan,created_at) VALUES
(1,NULL,'Menunggu','Pengaduan dikirim.',NOW()-INTERVAL 3 DAY),(1,1,'Diproses','Teknisi sudah dijadwalkan untuk memeriksa kipas.',NOW()-INTERVAL 2 DAY),
(2,NULL,'Menunggu','Pengaduan dikirim.',NOW()-INTERVAL 1 DAY),
(3,NULL,'Menunggu','Pengaduan dikirim.',NOW()-INTERVAL 10 DAY),(3,1,'Selesai','Komputer sudah diperbaiki dan diuji.',NOW()-INTERVAL 7 DAY);
